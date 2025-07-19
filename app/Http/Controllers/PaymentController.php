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
        $transaksi = Transaksi::find($transaksiId);
        $pemesanan = Pemesanan::find($transaksi->pemesanan_id);

        return view('pembayaran.index', compact('transaksi', 'pemesanan'));
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
            'pemesanan_id' => $pemesanans->first()->id,
            'tanggal_transaksi' => now(),
            'total' => $totalHarga,
            'metode_pembayaran' => 'MidTrans',
            'status_transaksi_id' => 1,
            'order_id' => $orderId,
        ]);

        \Midtrans\Config::$serverKey = config('midtrans.MIDTRANS_SERVER_KEY');
        \Midtrans\Config::$isProduction = config('midtrans.MIDTRANS_IS_PRODUCTION');
        \Midtrans\Config::$isSanitized = config('midtrans.MIDTRANS_IS_SANITIZED');
        \Midtrans\Config::$is3ds = config('midtrans.MIDTRANS_IS_3DS');

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
        $transaksi = Transaksi::whereHas('pemesanans', function ($query) {
            $query->where('user_id', Auth::id());
        })->latest('created_at')->first();

        if (!$transaksi) {
            return redirect()->route('lomba.saya')->withErrors('Transaksi tidak ditemukan.');
        }

        $refQrcode = $transaksi->refQrcode;

        if ($refQrcode) {
            $pathSvg = 'qr_code/' . $refQrcode->file_qrcode; // file_qrcode adalah SVG
            $pathPng = 'qr_code/' . $refQrcode->id . '.png'; // PNG versi untuk PDF

            // Cek apakah kedua file QR ada di storage/public
            $hasSvg = Storage::disk('public')->exists($pathSvg);
            $hasPng = Storage::disk('public')->exists($pathPng);

            if ($hasSvg && $hasPng) {
                // Ambil isi SVG untuk tampilan web (jika ingin ditampilkan di view web)
                $qrcodeSvg = Storage::disk('public')->get($pathSvg);

                // Ambil path lokal absolut untuk PNG agar bisa dibaca oleh DomPDF
                $qrcodePath = storage_path('app/public/' . $pathPng);

                $pdf = Pdf::loadView('pdf.bukti_pembayaran', [
                    'transaksi' => $transaksi,
                    'qrcodeSvg' => $qrcodeSvg,
                    'qrcodePath' => $qrcodePath, // Path lokal PNG untuk dipakai di <img src="">
                ]);

                return $pdf->download('bukti-pembayaran-' . $transaksi->order_id . '.pdf');
            }
        }

        // Jika QR tidak ditemukan, redirect kembali
        return redirect()->route('lomba.saya')->with([
            'download_pdf' => true,
            'transaksi_id' => $transaksi->id,
        ]);
    }

    public function callback(Request $request)
    {
        // \Log::info('Midtrans callback received', $request->all());
        // \Log::info('Callback Midtrans Masuk', $request->all());

        $serverKey = config('midtrans.MIDTRANS_SERVER_KEY');
        $hashedKey = hash('sha512', $request->order_id . $request->status_code . $request->gross_amount . $serverKey);
        // \Log::info('Midtrans callback signature check', [
        //     'hashedKey' => $hashedKey,
        //     'signature_key' => $request->signature_key,
        // ]);

        if ($request->transaction_status == 'pending') {
            $transaksi = Transaksi::where('order_id', $request->order_id)->first();
            if (!$transaksi) {
                return 0;
            }

            $transaksi->update(['status_transaksi_id' => 1]); // anggap 1 = pending

            preg_match('/silobur.{10}(.*)/', $transaksi->order_id, $matches);
            $pemesananIds = isset($matches[1]) ? explode('-', $matches[1]) : [$transaksi->pemesanan_id];

            foreach ($pemesananIds as $id) {
                $pemesanan = Pemesanan::find($id);
                if ($pemesanan && $pemesanan->status_pemesanan_id != 2) {
                    $pemesanan->update(['status_pemesanan_id' => 1]); // pending
                }
            }

            return 1; // atau kode apa pun untuk penanda sukses
        }


        //if ($hashedKey == $request->signature_key) {
        if ($request->transaction_status == 'capture' || $request->transaction_status == 'settlement') {
            $transaksi = Transaksi::where('order_id', $request->order_id)->first();
            if (!$transaksi) {
                // \Log::warning('Midtrans callback: transaksi tidak ditemukan', ['order_id' => $request->order_id]);
                return 0;
            }

            $transaksi->update(['status_transaksi_id' => 2]);

            preg_match('/silobur.{10}(.*)/', $transaksi->order_id, $matches);
            $pemesananIds = isset($matches[1]) ? explode('-', $matches[1]) : [$transaksi->pemesanan_id];

            // \Log::info('Midtrans callback: parsing pemesananIds', [
            //     'order_id' => $transaksi->order_id,
            //     'pemesananIds' => $pemesananIds,
            // ]);

            $userId = null;

            foreach ($pemesananIds as $id) {
                $pemesanan = Pemesanan::find($id);
                if ($pemesanan && $pemesanan->status_pemesanan_id != 2) {
                    $pemesanan->update(['status_pemesanan_id' => 2]);
                    $userId = $pemesanan->user_id;
                } else {
                    // \Log::warning('Midtrans callback: pemesanan tidak ditemukan atau sudah berhasil', ['pemesanan_id' => $id]);
                }
            }

            // Generate QR code
            $ref_qrcode = new RefQrcode();
            $ref_qrcode->user_id = $userId ?? auth::id();
            $ref_qrcode->status_qr_id = 1;
            $ref_qrcode->transaksi_id = $transaksi->id;
            $ref_qrcode->save();

            $ref_qrcode->file_qrcode = $ref_qrcode->id . ".svg";
            $ref_qrcode->save();

            $path_file = 'qr_code/' . $ref_qrcode->file_qrcode;
            $file_qr = QrCode::size(200)
                ->format('svg')
                ->generate($ref_qrcode->id);
            Storage::disk('public')->put($path_file, $file_qr);

            // Simpan juga versi PNG untuk PDF
            $path_png = 'qr_code/' . $ref_qrcode->id . '.png';
            $file_qr_png = QrCode::format('png')->size(200)->generate($ref_qrcode->id);
            Storage::disk('public')->put($path_png, $file_qr_png);
            if (!Storage::disk('public')->exists($path_png)) {
                Log::error("Gagal menyimpan PNG QRCode: " . $path_png);
            }

            return 2;
        }
        if ($request->transaction_status == 'expire') {
            $transaksi = Transaksi::where('order_id', $request->order_id)->first();
            $transaksiLain = Transaksi::where('pemesanan_id', $transaksi->pemesanan_id)
                ->where('status_transaksi_id', 2)
                ->get();

            if ($transaksiLain->isEmpty()) {
                // Delete pemesanan
                $pemesanan = DB::table('pemesanans')
                    ->where('id', $transaksi->pemesanan_id)
                    ->delete();

                return "Data Pemesanan berhasil dihapus";
            }
            return 3;
        }

        //return 1;
        //}

        return 0;
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
