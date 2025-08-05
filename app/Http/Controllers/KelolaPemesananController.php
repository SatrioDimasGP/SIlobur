<?php

namespace App\Http\Controllers;

use App\Models\Pemesanan;
use App\Models\StatusPemesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KelolaPemesananController extends Controller
{
    public function index()
    {
        $pemesanans = Pemesanan::with([
            'user',
            'lomba',
            'status',
            'gantangan',
            'burung.jenisBurung',
            'burung.kelas',
        ])->orderBy('created_at', 'asc')->get(); // gunakan get() karena pakai DataTables

        return view('bendahara.index', compact('pemesanans'));
    }

    public function show(Pemesanan $pemesanan)
    {
        $statusList = StatusPemesanan::all();

        $pemesanan->load([
            'user',
            'gantangan',
            'burung.jenisBurung',
            'burung.kelas',
            'lomba',
            'status',
        ]);

        return view('bendahara.show', compact('pemesanan', 'statusList'));
    }

    public function updateStatus(Request $request, Pemesanan $pemesanan)
    {
        $request->validate([
            'status_pemesanan_id' => 'required|exists:status_pemesanans,id'
        ]);

        DB::beginTransaction();

        try {
            if ($pemesanan->status_pemesanan_id == $request->status_pemesanan_id) {
                return redirect()->route('data-pendaftaran.show', $pemesanan)
                    ->with('error', 'Status pemesanan tidak berubah.');
            }

            $pemesanan->status_pemesanan_id = $request->status_pemesanan_id;
            $pemesanan->save();

            DB::commit();

            return redirect()->route('data-pendaftaran.index')
                ->with('success', 'Status pembayaran berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->route('data-pendaftaran.show', $pemesanan)
                ->with('error', 'Terjadi kesalahan saat memperbarui status pembayaran. Silakan coba lagi.');
        }
    }

    public function destroy(Pemesanan $pemesanan)
    {
        DB::beginTransaction();

        try {
            $pemesanan->forceDelete(); // ganti delete() dengan forceDelete()

            DB::commit();

            return redirect()->route('data-pendaftaran.index')
                ->with('success', 'Data pemesanan berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->with('error', 'Gagal menghapus data pemesanan.');
        }
    }
}
