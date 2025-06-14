<?php

namespace App\Http\Controllers;

use App\Models\Blok;
use App\Models\Gantangan;
use App\Models\Lomba;
use App\Models\BlokGantangan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class KonfigurasiBlokController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');

        $bloks = Blok::with('lomba')->get();
        $lombas = Lomba::all();
        $gantangans = Gantangan::all();

        $blokGantangans = BlokGantangan::with('blok', 'gantangan')
            ->when($search, function ($query, $search) {
                $query->whereHas('blok', fn($q) => $q->where('nama', 'like', "%$search%"))
                    ->orWhereHas('gantangan', fn($q) => $q->where('nomor', 'like', "%$search%"));
            })
            ->paginate(10);

        return view('korlap.konfigurasi_blok.index', compact('bloks', 'lombas', 'gantangans', 'blokGantangans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'lomba_id' => 'required|exists:lombas,id',
        ]);

        DB::beginTransaction();
        try {
            $existing = Blok::withTrashed()
                ->where('nama', $request->nama)
                ->where('lomba_id', $request->lomba_id)
                ->first();

            if ($existing) {
                if ($existing->trashed()) {
                    $existing->restore();
                    $existing->update([
                        'updated_by' => Auth::id(),
                        'updated_at' => now(),
                    ]);
                } else {
                    throw new \Exception("Blok dengan nama '{$request->nama}' sudah ada untuk lomba ini.");
                }
            } else {
                Blok::create([
                    'nama' => $request->nama,
                    'lomba_id' => $request->lomba_id,
                    'created_by' => Auth::id(),
                    'updated_by' => Auth::id(),
                ]);
            }

            DB::commit();
            return redirect()->route('konfigurasi-blok.index')->with('success', 'Blok berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menambahkan blok: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $blok = Blok::findOrFail($id);
        $lombas = Lomba::all();
        return view('korlap.konfigurasi_blok.edit_blok', compact('blok', 'lombas'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string|max:50',
            'lomba_id' => 'required|exists:lombas,id',
        ]);

        DB::beginTransaction();
        try {
            $blok = Blok::withTrashed()->findOrFail($id);

            $duplicate = Blok::withTrashed()
                ->where('nama', $request->nama)
                ->where('lomba_id', $request->lomba_id)
                ->where('id', '!=', $id)
                ->first();

            if ($duplicate) {
                if ($duplicate->trashed()) {
                    $duplicate->forceDelete();
                } else {
                    return redirect()->route('konfigurasi-blok.index')
                        ->with('error', 'Blok dengan nama dan lomba ini sudah ada.');
                }
            }

            $blok->update([
                'nama' => $request->nama,
                'lomba_id' => $request->lomba_id,
                'updated_by' => Auth::id(),
                'deleted_at' => null,
            ]);

            DB::commit();
            return redirect()->route('konfigurasi-blok.index')->with('success', 'Blok berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memperbarui blok: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $blok = Blok::findOrFail($id);
            $blok->delete();

            DB::commit();
            return redirect()->route('konfigurasi-blok.index')->with('success', 'Blok berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menghapus blok: ' . $e->getMessage());
        }
    }

    public function storeGantangan(Request $request)
    {
        $request->validate([
            'blok_id' => 'required|exists:bloks,id',
            'gantangan_id' => 'required|exists:gantangans,id',
        ]);

        DB::beginTransaction();
        try {
            $gantanganId = $request->gantangan_id;

            $existingGantangan = BlokGantangan::withTrashed()
                ->where('blok_id', $request->blok_id)
                ->where('gantangan_id', $gantanganId)
                ->first();

            if ($existingGantangan) {
                if ($existingGantangan->trashed()) {
                    // Jika pernah dihapus, maka restore dan update
                    $existingGantangan->restore();
                    $existingGantangan->update([
                        'updated_by' => Auth::id(),
                        'updated_at' => now(),
                    ]);
                } else {
                    // Sudah ada dan aktif → return error
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'Gantangan sudah terdaftar di blok ini.');
                }
            } else {
                // Belum ada → buat baru
                BlokGantangan::create([
                    'blok_id' => $request->blok_id,
                    'gantangan_id' => $gantanganId,
                    'created_by' => Auth::id(),
                    'updated_by' => Auth::id(),
                ]);
            }

            DB::commit();
            return redirect()->route('konfigurasi-blok.index')->with('success', 'Gantangan berhasil ditambahkan ke blok.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menambahkan gantangan: ' . $e->getMessage());
        }
    }

    public function editGantangan($id)
    {
        $blokGantangan = BlokGantangan::with('gantangan')->findOrFail($id);
        $bloks = Blok::all();
        $gantangans = Gantangan::all();

        return view('korlap.konfigurasi_blok.edit_blok_gantangan', compact('blokGantangan', 'bloks', 'gantangans'));
    }



    public function updateGantangan(Request $request, $id)
    {
        $request->validate([
            'blok_id' => 'required|exists:bloks,id',
            'gantangan_id' => 'required|exists:gantangans,id',
        ]);

        DB::beginTransaction();
        try {
            $blokGantangan = BlokGantangan::withTrashed()->findOrFail($id);

            $existing = BlokGantangan::withTrashed()
                ->where('blok_id', $request->blok_id)
                ->where('gantangan_id', $request->gantangan_id)
                ->where('id', '!=', $blokGantangan->id)
                ->first();

            if ($existing) {
                if ($existing->trashed()) {
                    $existing->forceDelete();
                } else {
                    throw new \Exception('Gantangan sudah digunakan di blok tersebut.');
                }
            }

            $blokGantangan->update([
                'blok_id' => $request->blok_id,
                'gantangan_id' => $request->gantangan_id,
                'updated_by' => Auth::id(),
            ]);

            if ($blokGantangan->trashed()) {
                $blokGantangan->restore();
            }

            DB::commit();
            return redirect()->route('konfigurasi-blok.index')->with('success', 'Relasi blok dan gantangan berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memperbarui: ' . $e->getMessage());
        }
    }




    public function destroyGantangan($id)
    {
        DB::beginTransaction();
        try {
            $blokGantangan = BlokGantangan::findOrFail($id);
            $blokGantangan->delete();

            DB::commit();
            return redirect()->route('konfigurasi-blok.index')->with('success', 'Gantangan berhasil dihapus dari blok.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
