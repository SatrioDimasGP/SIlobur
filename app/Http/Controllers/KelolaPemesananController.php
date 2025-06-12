<?php

namespace App\Http\Controllers;

use App\Models\Pemesanan;
use App\Models\StatusPemesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KelolaPemesananController extends Controller
{
    public function index(Request $request)
    {
        $query = Pemesanan::with([
            'user',
            'lomba',
            'status',
            'gantangan',
            'burung.jenisBurung',
            'burung.kelas',
        ]);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%$search%");
            })->orWhereHas('lomba', function ($q) use ($search) {
                $q->where('nama', 'like', "%$search%");
            })->orWhereHas('burung.jenisBurung', function ($q) use ($search) {
                $q->where('nama', 'like', "%$search%");
            })->orWhereHas('burung.kelas', function ($q) use ($search) {
                $q->where('nama', 'like', "%$search%");
            })->orWhereHas('gantangan', function ($q) use ($search) {
                $q->where('nomor', 'like', "%$search%");
            });
        }

        $pemesanans = $query->orderBy('created_at', 'asc')->paginate(10); // urut dari yang paling awal

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
}
