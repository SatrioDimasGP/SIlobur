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
        //         $data = RefQrcode::with([
        //     'transaksi.pemesanans.burung.jenisBurung',
        //     'transaksi.pemesanans.burung.kelas'
        // ])->where('status_qr_id', 2)->first();

        // dd($data->transaksi?->pemesanans?->burung?->jenisBurung?->nama);


        $data = RefQrcode::with([
            'user',
            'status_qr',
            'transaksi.pemesanans.burung.jenisBurung',
            'transaksi.pemesanans.burung.kelas'
        ])->where('status_qr_id', 2)->get();

        // dd ($data);

        // return $data;

        return view('bendahara.scan', compact('data'));
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
        dd($pemesanans);

        return response()->json([
            'status' => 200,
            'message' => 'Berhasil scan',
            'data' => $pemesanans
        ]);
    }
}
