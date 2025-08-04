<?php

namespace App\Http\Controllers;

use App\Models\Burung;
use App\Models\JenisBurung;
use App\Models\Kelas;
use App\Models\Lomba;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; // Import DB

class BurungController extends Controller
{
    // Menampilkan semua jenis burung dan kelas
    public function index()
    {
        // Ambil data jenis burung dan kelas
        $jenisBurungs = JenisBurung::all();
        $kelas = Kelas::all();

        // Cek jika data kosong dan kirimkan pesan
        if ($jenisBurungs->isEmpty()) {
            $jenisBurungMessage = 'Tidak ada jenis burung yang terdaftar.';
        } else {
            $jenisBurungMessage = null;
        }

        if ($kelas->isEmpty()) {
            $kelasMessage = 'Tidak ada kelas yang terdaftar.';
        } else {
            $kelasMessage = null;
        }

        return view('korlap.manajemen_lomba.burung.index', compact('jenisBurungs', 'kelas'));
    }

    // public function create()
    // {
    //     return view('korlap.manajemen_lomba.burung.create');
    // }
    // Menyimpan data jenis burung dan kelas baru

    public function storeJenisBurung(Request $request)
    {
        DB::beginTransaction(); // Mulai transaksi

        try {
            $request->validate([
                'jenis_burung' => 'required|string|max:255',
            ], [
                'jenis_burung.required' => 'Nama jenis burung wajib diisi.',
            ]);

            $nama = strtoupper($request->jenis_burung);

            // Cari data termasuk yang di-soft-delete
            $existing = JenisBurung::withTrashed()
                ->whereRaw('UPPER(nama) = ?', [$nama])
                ->first();

            if ($existing) {
                if ($existing->trashed()) {
                    // Jika soft-deleted, restore dan update updated_by
                    $existing->restore();
                    $existing->update([
                        'updated_by' => Auth::id(),
                    ]);

                    DB::commit();
                    return redirect()->back()->with('success', 'Jenis burung berhasil dipulihkan.');
                } else {
                    // Sudah ada dan aktif
                    return redirect()->back()->with('error', 'Jenis burung ini sudah terdaftar.')->withInput();
                }
            }

            // Belum ada, buat baru
            JenisBurung::create([
                'nama' => $nama,
                'created_by' => Auth::id(),
            ]);

            DB::commit(); // Jika semua sukses, commit transaksi
            return redirect()->back()->with('success', 'Jenis burung berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack(); // Jika ada error, rollback transaksi
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    public function storeKelas(Request $request, $lomba_id)
    {
        DB::beginTransaction();

        try {
            $request->validate([
                'kelas' => 'required|string|max:255',
                'harga' => 'required|integer|min:0',
                // 'lomba_id' tidak divalidasi karena dari route parameter
            ], [
                'kelas.required' => 'Nama kelas wajib diisi.',
                'harga.required' => 'Harga wajib diisi.',
                'harga.integer' => 'Harga harus berupa angka.',
                'harga.min' => 'Harga tidak boleh kurang dari 0.',
            ]);

            $nama = strtoupper($request->kelas);
            $harga = str_replace('.', '', $request->harga); // hilangkan titik

            // Validasi lomba_id ada di DB
            $lomba = Lomba::findOrFail($lomba_id);

            // Cari termasuk soft deleted dengan nama dan harga yang sama untuk lomba ini
            $existing = Kelas::withTrashed()
                ->whereRaw('UPPER(nama) = ?', [$nama])
                ->where('harga', $harga)
                ->where('lomba_id', $lomba_id)
                ->first();

            if ($existing) {
                if ($existing->trashed()) {
                    $existing->restore();
                    $existing->update([
                        'updated_by' => Auth::id(),
                    ]);

                    DB::commit();
                    return redirect()->back()->with('success', 'Kelas berhasil dipulihkan.');
                } else {
                    return redirect()->back()->with('error', 'Kelas ini sudah terdaftar.')->withInput();
                }
            }

            // Create baru
            Kelas::create([
                'nama' => $nama,
                'harga' => $harga,
                'lomba_id' => $lomba_id,
                'created_by' => Auth::id(),
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Kelas berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }


    public function edit($lomba_id, $id)
    {
        $jenisBurung = JenisBurung::findOrFail($id);
        $kelas = Kelas::findOrFail($id); // Jika Kelas memiliki relasi dengan JenisBurung

        return view('manajemen_lomba.burung.edit', compact('jenisBurung', 'kelas'));
    }

    public function createGabungan($lomba_id)
    {
        $lomba = Lomba::findOrFail($lomba_id);
        $jenisBurungs = JenisBurung::all();
        $kelas = Kelas::where('lomba_id', $lomba_id)->get();

        // Ambil kelas yang punya lomba_id sama dengan $lomba_id
        $kelasIds = Kelas::where('lomba_id', $lomba_id)->pluck('id')->toArray();

        // Ambil burung yang kelas_id-nya ada di kelasIds
        $burungGabungan = Burung::with(['jenisBurung', 'kelas'])
            ->whereIn('kelas_id', $kelasIds)
            ->get();

        return view('korlap.manajemen_lomba.burung.create', compact('lomba', 'jenisBurungs', 'kelas', 'burungGabungan'));
    }

    public function storeGabungan(Request $request, $lomba_id)
    {
        DB::beginTransaction();

        try {
            $request->validate([
                'jenis_burung_id' => 'required|exists:jenis_burungs,id',
                'kelas_id' => 'required|exists:kelas,id',
            ]);

            $existing = Burung::withTrashed()
                ->where('jenis_burung_id', $request->jenis_burung_id)
                ->where('kelas_id', $request->kelas_id)
                ->first();

            if ($existing) {
                if ($existing->trashed()) {
                    $existing->restore();
                    $existing->update(['updated_by' => Auth::id()]);

                    DB::commit();
                    return redirect()->route('manajemen-lomba.kelola.burung.create', $lomba_id)
                        ->with('success', 'Gabungan berhasil dipulihkan.');
                } else {
                    return redirect()->route('manajemen-lomba.kelola.burung.create', $lomba_id)
                        ->with('error', 'Kombinasi jenis burung dan kelas sudah ada.');
                }
            }

            Burung::create([
                'jenis_burung_id' => $request->jenis_burung_id,
                'kelas_id' => $request->kelas_id,
                'created_by' => Auth::id(),
            ]);

            DB::commit();
            return redirect()->route('manajemen-lomba.kelola.burung.create', $lomba_id)
                ->with('success', 'Gabungan berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('manajemen-lomba.kelola.burung.create', $lomba_id)
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function editGabungan($lomba_id, $burung_id)
    {
        $burung = Burung::findOrFail($burung_id);
        $jenisBurungs = JenisBurung::all();
        $kelas = Kelas::all();

        return view('korlap.manajemen_lomba.burung.edit', compact('burung', 'jenisBurungs', 'kelas', 'lomba_id'));
    }

    public function updateGabungan(Request $request, $lomba_id, $id)
    {
        DB::beginTransaction();

        try {
            $request->validate([
                'jenis_burung_id' => 'required|exists:jenis_burungs,id',
                'kelas_id' => 'required|exists:kelas,id',
            ]);

            $existing = Burung::withTrashed()
                ->where('jenis_burung_id', $request->jenis_burung_id)
                ->where('kelas_id', $request->kelas_id)
                ->where('id', '!=', $id)
                ->first();

            if ($existing) {
                if ($existing->trashed()) {
                    $existing->restore();
                    $existing->update(['updated_by' => Auth::id()]);

                    Burung::findOrFail($id)->delete();

                    DB::commit();
                    return redirect()->route('manajemen-lomba.kelola.burung.create', $lomba_id)
                        ->with('success', 'Data gabungan dipulihkan dan diperbarui.');
                } else {
                    return redirect()->route('manajemen-lomba.kelola.burung.create', $lomba_id)
                        ->with('error', 'Kombinasi jenis burung dan kelas sudah digunakan.');
                }
            }

            $burung = Burung::findOrFail($id);
            $burung->update([
                'jenis_burung_id' => $request->jenis_burung_id,
                'kelas_id' => $request->kelas_id,
                'updated_by' => Auth::id(),
            ]);

            DB::commit();
            return redirect()->route('manajemen-lomba.kelola.burung.create', $lomba_id)
                ->with('success', 'Gabungan berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('manajemen-lomba.kelola.burung.create', $lomba_id)
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function destroyGabungan($lomba_id, $id)
    {
        DB::beginTransaction();

        try {
            $burung = Burung::findOrFail($id);
            $burung->delete();

            DB::commit();
            return redirect()->route('manajemen-lomba.kelola.burung.create', $lomba_id)
                ->with('success', 'Gabungan berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('manajemen-lomba.kelola.burung.create', $lomba_id)
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }


    // Menyimpan perubahan jenis burung dan kelas
    public function update(Request $request, $id)
    {
        $request->validate([
            'jenis_burung' => 'required|string|max:255',
            'kelas' => 'required|string|max:255',
        ]);

        // Update atau restore jenis burung
        $jenisBurung = JenisBurung::withTrashed()->findOrFail($id);
        $jenisBurung->nama = $request->jenis_burung;
        if ($jenisBurung->trashed()) {
            $jenisBurung->restore();
        }
        $jenisBurung->save();

        // Update atau restore kelas
        $kelas = Kelas::withTrashed()->findOrFail($id);
        $kelas->nama = $request->kelas;
        if ($kelas->trashed()) {
            $kelas->restore();
        }
        $kelas->save();

        return redirect()->route('manajemen-lomba.burung.index')
            ->with('success', 'Jenis Burung dan Kelas berhasil diperbarui atau dipulihkan.');
    }

    public function editJenisBurung($lomba_id, $id)
    {
        $jenisBurung = JenisBurung::findOrFail($id);
        return view('korlap.manajemen_lomba.burung.edit_jenis', compact('jenisBurung', 'lomba_id'));
    }

    public function editKelas($lomba_id, $id)
    {
        $kelas = Kelas::findOrFail($id);

        // dd([
        //     'lomba_id' => $lomba_id,
        //     'kelas' => $kelas
        // ]);

        return view('korlap.manajemen_lomba.burung.edit_kelas', compact('kelas', 'lomba_id'));
    }

    public function updateJenisBurung(Request $request, $lomba_id, $id)
    {
        DB::beginTransaction();

        try {
            $request->validate([
                'jenis_burung' => 'required|string|max:255',
            ], [
                'jenis_burung.required' => 'Nama jenis burung wajib diisi.',
            ]);

            $nama = strtoupper($request->jenis_burung);

            // Cari entri lain (termasuk soft deleted) dengan nama sama
            $existing = JenisBurung::withTrashed()
                ->whereRaw('UPPER(nama) = ?', [$nama])
                ->where('id', '!=', $id)
                ->first();

            if ($existing) {
                if ($existing->trashed()) {
                    // Restore dan update jika sebelumnya soft deleted
                    $existing->restore();
                    $existing->update([
                        'updated_by' => Auth::id(),
                    ]);
                    // Hapus data lama
                    JenisBurung::findOrFail($id)->delete();

                    DB::commit();
                    return redirect()->route('manajemen-lomba.kelola', ['id' => $lomba_id])
                        ->with('success', 'Jenis burung berhasil dipulihkan dan diperbarui.');
                } else {
                    return redirect()->back()->with('error', 'Jenis burung ini sudah ada.')->withInput();
                }
            }

            $jenisBurung = JenisBurung::findOrFail($id);
            $jenisBurung->update([
                'nama' => $nama,
                'updated_by' => Auth::id(),
            ]);

            DB::commit();
            return redirect()->route('manajemen-lomba.kelola', ['id' => $lomba_id])
                ->with('success', 'Jenis burung berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('manajemen-lomba.kelola', ['id' => $lomba_id])
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function updateKelas(Request $request, $lomba_id, $id)
    {
        DB::beginTransaction();

        try {
            $lomba = Lomba::findOrFail($lomba_id);

            $request->validate([
                'kelas' => 'required|string|max:255',
                'harga' => 'required|integer|min:0',
            ], [
                'kelas.required' => 'Nama kelas wajib diisi.',
                'harga.required' => 'Harga wajib diisi.',
                'harga.integer' => 'Harga harus berupa angka.',
                'harga.min' => 'Harga tidak boleh kurang dari 0.',
            ]);

            $nama = strtoupper($request->kelas);
            $harga = str_replace('.', '', $request->harga); // hilangkan titik (misal: 50.000 → 50000)

            // Cek jika ada nama dan harga sama tapi id beda
            $existing = Kelas::withTrashed()
                ->whereRaw('UPPER(nama) = ?', [$nama])
                ->where('harga', $harga)
                ->where('lomba_id', $lomba_id)
                ->where('id', '!=', $id)
                ->first();

            if ($existing) {
                if ($existing->trashed()) {
                    // Restore yang lama
                    $existing->restore();
                    $existing->update([
                        'updated_by' => Auth::id(),
                    ]);

                    // Soft delete yang sedang diedit
                    Kelas::findOrFail($id)->delete();

                    DB::commit();
                    return redirect()->route('manajemen-lomba.kelola', ['id' => $lomba_id])
                        ->with('success', 'Kelas berhasil dipulihkan dan diperbarui.');
                } else {
                    return redirect()->back()->with('error', 'Kelas dengan nama dan harga ini sudah ada.')->withInput();
                }
            }

            $kelas = Kelas::where('id', $id)->where('lomba_id', $lomba_id)->firstOrFail();

            if ($kelas->nama === $nama && $kelas->harga == $harga) {
                return redirect()->back()
                    ->with('error', 'Tidak ada perubahan data yang dilakukan.')
                    ->withInput();
            }

            $kelas->update([
                'nama' => $nama,
                'harga' => $harga,
                'updated_by' => Auth::id(),
            ]);

            DB::commit();
            return redirect()->route('manajemen-lomba.kelola', ['id' => $lomba_id])
                ->with('success', 'Kelas berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('manajemen-lomba.kelola', ['id' => $lomba_id])
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function destroyJenisBurung($lomba_id, $id)
    {
        DB::beginTransaction();

        try {
            $jenisBurung = JenisBurung::findOrFail($id);
            $jenisBurung->delete(); // ini soft delete kalau model pakai SoftDeletes

            DB::commit();
            return redirect()->route('manajemen-lomba.kelola', ['id' => $lomba_id])
                ->with('success', 'Jenis burung berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('manajemen-lomba.kelola', ['id' => $lomba_id])
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function destroyKelas($lomba_id, $id)
    {
        DB::beginTransaction();

        try {
            $kelas = Kelas::findOrFail($id);
            $kelas->delete(); // soft delete

            DB::commit();
            return redirect()->route('manajemen-lomba.kelola', ['id' => $lomba_id])
                ->with('success', 'Kelas berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('manajemen-lomba.kelola', ['id' => $lomba_id])
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
