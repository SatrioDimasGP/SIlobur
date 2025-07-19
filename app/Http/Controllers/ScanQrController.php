<?php



namespace App\Http\Controllers;

use App\Models\Pemesanan;
use App\Models\RefQrcode;
use App\Models\Transaksi;

use Illuminate\Http\Request;



class ScanQrController extends Controller

{

    public function index()
    {
        $ref_qrs = RefQrcode::with('transaksi')->where('status_qr_id', 2)->get();

        $allPemesanan = collect();

        foreach ($ref_qrs as $ref_qr) {
            $transaksi = $ref_qr->transaksi;
            if (!$transaksi) continue;

            $pemesanan_awal = Pemesanan::find($transaksi->pemesanan_id);
            if (!$pemesanan_awal) continue;

            $pemesanans = Pemesanan::with(['burung.jenisBurung', 'burung.kelas', 'user'])
                ->where('user_id', $pemesanan_awal->user_id)
                ->whereBetween('created_at', [
                    $pemesanan_awal->created_at->subMinutes(1),
                    $pemesanan_awal->created_at->addMinutes(1),
                ])
                ->get();

            $allPemesanan = $allPemesanan->merge($pemesanans);
        }

        return view('bendahara.scan', [
            'data' => $allPemesanan
        ]);
    }



    public function show($qr_id)
    {
        $ref_qr = RefQrcode::with('transaksi')->find($qr_id);

        if (!$ref_qr || $ref_qr->status_qr_id == 2) {
            return response()->json([
                'status' => 400,
                'message' => 'QR Code tidak ditemukan',
            ]);
        }

        $ref_qr->status_qr_id = 2;
        $ref_qr->save();

        $transaksi = $ref_qr->transaksi;
        $pemesanan_awal = Pemesanan::find($transaksi->pemesanan_id);

        $pemesanans = Pemesanan::with(['burung.jenisBurung', 'burung.kelas', 'user'])
            ->where('user_id', $pemesanan_awal->user_id)
            ->whereBetween('created_at', [
                $pemesanan_awal->created_at->subMinutes(1),
                $pemesanan_awal->created_at->addMinutes(1),
            ])
            ->get();

        return response()->json([
            'status' => 200,
            'message' => 'Berhasil scan',
            'data' => $pemesanans
        ]);
    }
}
