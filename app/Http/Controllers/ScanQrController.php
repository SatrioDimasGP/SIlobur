<?php



namespace App\Http\Controllers;



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

        $ref_qr = RefQrcode::find($qr_id);



        if (!$ref_qr || $ref_qr->status_qr_id == 2) {

            return response()->json([

                'status' => 400,

                'message' => 'QR Code tidak ditemukan',

            ]);
        }



        $ref_qr->status_qr_id = 2;

        $ref_qr->save();



        return response()->json([

            'status' => 200,

            'message' => 'Berhasil scan',

            'data' => RefQrcode::with([
    'user',
    'transaksi.pemesanans.burung.jenisBurung',
    'transaksi.pemesanans.burung.kelas'
])->where('status_qr_id', 2)->get()

        ]);
    }
}
