<?php



namespace App\Http\Controllers;



use App\Models\RefQrcode;

use Illuminate\Http\Request;



class ScanQrController extends Controller

{

    public function index()

    {

        $data = RefQrcode::with(['user', 'status_qr'])

            ->where('status_qr_id', 2)->get();

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

            'data' => RefQrcode::with(['user'])

                ->where('status_qr_id', 2)->get()

        ]);
    }
}
