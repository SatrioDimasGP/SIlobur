<?php

namespace App\Http\Controllers;

use App\Models\Gantangan;
use App\Models\HargaBurung;
use App\Models\StatusGantangan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;



class GantanganController extends Controller
{
    // Menampilkan daftar gantangan
    public function index($lomba_id)
    {
        $gantangans = Gantangan::orderBy('nomor')->get();

        return view('korlap.manajemen_lomba.gantangan.index', compact('gantangans', 'lomba_id'));
    }


    public function store(Request $request, $lomba_id)
    {
        $request->validate([
            'nomor_awal' => 'required|integer|min:1',
            'nomor_akhir' => 'nullable|integer|gte:nomor_awal',
        ]);

        $awal = $request->nomor_awal;
        $akhir = $request->nomor_akhir ?: $awal;
        $createdBy = Auth::id();

        DB::beginTransaction();

        try {
            $failedNumbers = [];

            for ($i = $awal; $i <= $akhir; $i++) {
                $existing = Gantangan::withTrashed()->where('nomor', $i)->first();

                if ($existing) {
                    if ($existing->trashed()) {
                        $existing->restore();
                        $existing->update(['updated_by' => $createdBy]);
                    } else {
                        $failedNumbers[] = $i;
                    }
                } else {
                    Gantangan::create([
                        'nomor' => $i,
                        'created_by' => $createdBy,
                        'updated_by' => $createdBy,
                    ]);
                }
            }

            if (!empty($failedNumbers)) {
                throw new \Exception("Gagal menambahkan. Nomor sudah ada: " . implode(', ', $failedNumbers));
            }

            DB::commit();
            return redirect()->back()->with('success', 'Gantangan berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }


    // Menampilkan form edit gantangan
    public function edit($lomba_id, $id)
    {
        // dd(compact('lomba_id', 'id'));
        $gantangan = Gantangan::findOrFail($id);

        return view('korlap.manajemen_lomba.gantangan.edit', compact('gantangan', 'lomba_id'));
    }

    // Memperbarui data gantangan
    public function update(Request $request, $lomba_id, $id)
    {
        $request->validate([
            'nomor' => 'required|numeric',
        ]);

        DB::beginTransaction();

        try {
            $gantangan = Gantangan::findOrFail($id);

            // Cek apakah nomor gantangan sudah dipakai nomor lain
            $exists = Gantangan::where('nomor', $request->nomor)
                ->where('id', '!=', $id)
                ->exists();

            if ($exists) {
                throw new \Exception("Nomor gantangan {$request->nomor} sudah digunakan.");
            }

            $gantangan->update([
                'nomor' => $request->nomor,
                'updated_by' => Auth::id(),
            ]);

            DB::commit();
            return redirect()->route('manajemen-lomba.kelola.gantangan.index', $lomba_id ?? null)->with('success', 'Gantangan berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage())->withInput();
        }
    }


    // Menghapus data gantangan
    public function destroy($lomba_id, $id)
    {
        $gantangan = Gantangan::findOrFail($id);
        $gantangan->delete();

        return redirect()->route('manajemen-lomba.kelola.gantangan.index', $lomba_id)
            ->with('success', 'Gantangan berhasil dihapus.');
    }
}
