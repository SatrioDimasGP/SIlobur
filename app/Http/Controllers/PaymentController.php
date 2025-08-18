<?php

namespace App\Http\Controllers;

use App\Models\Gantangan;
use App\Models\Pemesanan;
use App\Models\RefQrcode;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;
// use PDF; // Tambahkan di atas controller



class PaymentController extends Controller
{
    //
    public function pembayaran($transaksiId)
    {
        $transaksi = Transaksi::with('pemesanans.burung.kelas')->findOrFail($transaksiId);
        // Debug sementara
        Log::info('Transaksi snap_token:', [$transaksi->snap_token]);

        return view('pembayaran.index', [
            'transaksi' => $transaksi,
            'pemesanan' => $transaksi->pemesanans, // dikembalikan dalam array
        ]);
    }



    public function proccess(Request $request)
    {
        $pemesananIds = (array) $request->input('pemesanan_ids');

        if (empty($pemesananIds)) {
            return redirect()->back()->withErrors('Pemesanan tidak ditemukan.');
        }

        $pemesanans = Pemesanan::with('burung.kelas')->whereIn('id', $pemesananIds)->get();


        if ($pemesanans->isEmpty()) {
            return redirect()->back()->withErrors('Data pemesanan tidak ditemukan.');
        }
        // Cek apakah sudah ada transaksi belum lunas untuk salah satu pemesanan
        $transaksiEksisting = Transaksi::whereHas('pemesanans', function ($query) use ($pemesananIds) {
            $query->whereIn('pemesanan_id', $pemesananIds);
        })
            ->where('status_transaksi_id', 1)
            ->orderByDesc('created_at')
            ->first();

        if ($transaksiEksisting) {
            return redirect()->route('pembayaran.index', $transaksiEksisting->id)
                ->with('info', 'Transaksi sudah dibuat. Silakan selesaikan pembayaran terlebih dahulu.');
        }
        $totalHarga = 0;

        foreach ($pemesanans as $pemesanan) {
            $gantangan = Gantangan::find($pemesanan->gantangan_id);
            if (!$gantangan) {
                return redirect()->back()->withErrors("Gantangan untuk pemesanan ID {$pemesanan->id} tidak ditemukan.");
            }

            $blokGantangan = $gantangan->blokGantangans()
                ->whereHas('blok', function ($query) use ($pemesanan) {
                    $query->where('lomba_id', $pemesanan->lomba_id);
                })
                ->first();

            if (!$blokGantangan) {
                return redirect()->back()->withErrors("Blok Gantangan tidak ditemukan untuk pemesanan ID {$pemesanan->id}.");
            }

            $kelas = $pemesanan->burung->kelas ?? null;
            $harga = $kelas->harga ?? 0;

            if ($harga < 0.01) {
                return redirect()->back()->withErrors("Harga tidak valid untuk pemesanan ID {$pemesanan->id}.");
            }

            $totalHarga += $harga;
        }

        // Encode semua pemesanan ID ke dalam order_id
        $encodedPemesananIds = implode('-', $pemesananIds);
        $orderId = 'silobur' . Str::random(10) . $encodedPemesananIds;

        $transaksi = Transaksi::create([
            'tanggal_transaksi' => now(),
            'total' => $totalHarga,
            'metode_pembayaran' => 'MidTrans',
            'status_transaksi_id' => 1,
            'order_id' => $orderId,
        ]);
        Log::info('Callback mencari transaksi', ['order_id' => $orderId]);

        // Simpan relasi ke pivot
        $transaksi->pemesanans()->attach($pemesananIds, [
            'created_at' => now(),
            'updated_at' => now(),
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        \Midtrans\Config::$serverKey = config('midtrans.serverKey');
        \Midtrans\Config::$clientKey = config('midtrans.clientKey');
        \Midtrans\Config::$isProduction = config('midtrans.isProduction');
        \Midtrans\Config::$isSanitized = config('midtrans.isSanitized');
        \Midtrans\Config::$is3ds = config('midtrans.is3ds');

        $params = [
            'transaction_details' => [
                'order_id' => $transaksi->order_id,
                'gross_amount' => $transaksi->total,
            ],
            'callbacks' => [
                'finish' => url()->route('pembayaran.sukses'), // full URL, aman untuk mobile
            ],
        ];

        $snapToken = \Midtrans\Snap::getSnapToken($params);
        $transaksi->snap_token = $snapToken;
        $transaksi->save();

        return redirect()->route('pembayaran.index', $transaksi->id);
    }
    public function pembayaranSukses()
    {
        $transaksi = Transaksi::with('pemesanans')->whereHas('pemesanans', function ($query) {
            $query->where('user_id', Auth::id());
        })->latest('created_at')->first();

        if (!$transaksi) {
            return redirect()->route('lomba.saya')->withErrors('Transaksi tidak ditemukan.');
        }

        $refQrcode = $transaksi->refQrcode;

        if ($refQrcode) {
            $pathSvg = 'qr_code/' . $refQrcode->file_qrcode;
            $pathPng = 'qr_code/' . $refQrcode->id . '.png';

            $hasSvg = Storage::disk('public')->exists($pathSvg);
            $hasPng = Storage::disk('public')->exists($pathPng);

            if ($hasSvg && $hasPng) {
                $qrcodeSvg = Storage::disk('public')->get($pathSvg);
                $qrcodePath = storage_path('app/public/' . $pathPng);

                $pdf = Pdf::loadView('pdf.bukti_pembayaran', [
                    'transaksi' => $transaksi,
                    'qrcodeSvg' => $qrcodeSvg,
                    'qrcodePath' => $qrcodePath,
                ]);

                return $pdf->download('bukti-pembayaran-' . $transaksi->order_id . '.pdf');
            }
        }

        return redirect()->route('lomba.saya')->with([
            'download_pdf' => true,
            'transaksi_id' => $transaksi->id,
        ]);
    }

    public function callback(Request $request)
    {
        Log::info('MIDTRANS CALLBACK MASUK', [
            'request' => request()->all(),
        ]);

        $serverKey = config('midtrans.serverKey');
        $hashedKey = hash('sha512', $request->order_id . $request->status_code . $request->gross_amount . $serverKey);

        if ($hashedKey === $request->signature_key) {
            $transaksi = Transaksi::with('pemesanans')->where('order_id', $request->order_id)->first();

            if (!$transaksi) {
                return response()->json(['message' => 'Transaksi tidak ditemukan'], 404);
            }

            // === Jika sukses bayar ===
            if (in_array($request->transaction_status, ['capture', 'settlement'])) {
                $transaksi->update(['status_transaksi_id' => 2]);
                Log::info('Generate QR berhasil');
                $userId = null;
                foreach ($transaksi->pemesanans as $pemesanan) {
                    if ($pemesanan->status_pemesanan_id != 2) {
                        $pemesanan->update(['status_pemesanan_id' => 2]);
                        $userId = $pemesanan->user_id;
                    }
                }

                // Generate QR Code
                $ref_qrcode = new RefQrcode();
                $ref_qrcode->user_id = $userId ?? Auth::id();
                $ref_qrcode->status_qr_id = 1;
                $ref_qrcode->transaksi_id = $transaksi->id;
                $ref_qrcode->save();

                $ref_qrcode->file_qrcode = $ref_qrcode->id . ".svg";
                $ref_qrcode->save();

                $pathSvg = 'qr_code/' . $ref_qrcode->file_qrcode;
                $pathPng = 'qr_code/' . $ref_qrcode->id . '.png';

                $svgQr = QrCode::size(200)->format('svg')->generate($ref_qrcode->id);
                Storage::disk('public')->put($pathSvg, $svgQr);

                $pngQr = QrCode::format('png')->size(200)->generate($ref_qrcode->id);
                Storage::disk('public')->put($pathPng, $pngQr);

                return response()->json(['message' => 'Berhasil diproses'], 200);
            }

            // === Jika expired ===
            if ($request->transaction_status === 'expire') {
                $pemesanans = $transaksi->pemesanans;

                foreach ($pemesanans as $pemesanan) {
                    $hasTransaksiLain = $pemesanan->transaksis()
                        ->where('status_transaksi_id', 2)
                        ->exists();

                    if (!$hasTransaksiLain) {
                        $pemesanan->forceDelete();
                    }
                }

                return response()->json(['message' => 'Transaksi expired, data pemesanan dihapus jika tidak punya transaksi sukses lain'], 200);
            }

            return response()->json(['message' => 'Status transaksi tidak ditangani'], 200);
        }

        return response()->json(['message' => 'Signature tidak valid'], 403);
    }

    public function cekStatusQr($id)
    {
        $qr = RefQrcode::find($id); // Benar, karena UUID disimpan di kolom 'id'

        if (!$qr) {
            return response()->json(['message' => 'QR tidak ditemukan'], 404);
        }

        return response()->json([
            'status_qr_id' => $qr->status_qr_id,
        ]);
    }

    public function generateBuktiPembayaranPdf($transaksiId)
    {
        $transaksi = Transaksi::findOrFail($transaksiId);
        $qrcode = RefQrcode::where('transaksi_id', $transaksiId)->first();
        $qrcodePath = null;
        // dd($qrcodePath, file_exists(storage_path('app/public/qr_code/' . $qrcode->id . '.png')));

        if ($qrcode && Storage::disk('public')->exists('qr_code/' . $qrcode->id . '.png')) {
            $qrcodePath = storage_path('app/public/qr_code/' . $qrcode->id . '.png');
        }
        // dd($qrcodePath, file_exists($qrcodePath));


        // dd($qrcodePath, file_exists(storage_path('app/public/qr_code/' . $qrcode->id . '.png')));

        $pdf = Pdf::loadView('pdf.bukti_pembayaran', [
            'transaksi' => $transaksi,
            'qrcodePath' => $qrcodePath,
        ]);

        return $pdf->download('bukti-pembayaran-' . $transaksi->order_id . '.pdf');
    }
}
