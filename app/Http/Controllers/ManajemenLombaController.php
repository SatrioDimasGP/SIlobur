<?php

namespace App\Http\Controllers;

use App\Models\JenisBurung;
use App\Models\Kelas;
use App\Models\Lomba;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Burung;

class ManajemenLombaController extends Controller
{
    // Tampilkan semua lomba
    public function index()
    {
        $lombas = Lomba::latest()->get();
        return view('korlap.manajemen_lomba.index', compact('lombas'));
    }

    public function kelola($id)
    {
        // Ambil data lomba lengkap dengan relasi statusLomba
        $lomba = Lomba::with('statusLomba')->findOrFail($id);

        // Ambil semua lomba untuk navigasi (opsional)
        $lombas = Lomba::latest()->get();

        // Ambil data jenis burung secara global (semua)
        $jenisBurungs = JenisBurung::all();

        // Ambil kelas yang hanya terkait dengan lomba tersebut
        // Misal ada relasi di model Lomba:
        // public function kelas() { return $this->hasMany(Kelas::class); }
        $kelas = $lomba->kelas;

        // Pesan kalau kosong
        $jenisBurungMessage = $jenisBurungs->isEmpty() ? 'Tidak ada jenis burung yang terdaftar.' : null;
        $kelasMessage = $kelas->isEmpty() ? 'Tidak ada kelas yang terdaftar untuk lomba ini.' : null;

        return view('korlap.manajemen_lomba.kelola', compact(
            'lomba',
            'lombas',
            'jenisBurungs',
            'kelas',
            'jenisBurungMessage',
            'kelasMessage',
        ));
    }


    // Form tambah lomba
    public function create()
    {
        return view('korlap.manajemen_lomba.create');
    }

    // Simpan data lomba baru
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'lokasi' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'deskripsi' => 'required|string|max:255',
            'status_lomba_id' => 'required|integer|in:1,2', // Validasi status_lomba_id
        ]);

        DB::beginTransaction(); // Memulai transaksi DB

        try {
            Lomba::create([
                'nama' => $request->nama,
                'lokasi' => $request->lokasi,
                'tanggal' => $request->tanggal,
                'deskripsi' => $request->deskripsi,
                'status_lomba_id' => $request->status_lomba_id, // Menambahkan status_lomba_id
                'aktif' => true,
                'created_by' => Auth::id(),
            ]);

            DB::commit(); // Menyimpan transaksi

            return redirect()->route('manajemen-lomba.index')->with('success', 'Lomba berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack(); // Membatalkan transaksi jika terjadi error

            return redirect()->route('manajemen-lomba.index')->with('error', 'Terjadi kesalahan. Silakan coba lagi.');
        }
    }

    // Form edit lomba
    public function edit($id)
    {
        $lomba = Lomba::findOrFail($id);
        return view('korlap.manajemen_lomba.edit', compact('lomba'));
    }

    // Simpan perubahan data lomba
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'lokasi' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'deskripsi' => 'required|string|max:255',
            'status_lomba_id' => 'required|integer|in:1,2',
        ]);

        DB::beginTransaction(); // Memulai transaksi DB

        try {
            $lomba = Lomba::findOrFail($id);
            $lomba->update([
                'nama' => $request->nama,
                'lokasi' => $request->lokasi,
                'tanggal' => $request->tanggal,
                'deskripsi' => $request->deskripsi,
                'status_lomba_id' => $request->status_lomba_id,
                'updated_by' => Auth::id(),
            ]);

            DB::commit(); // Menyimpan transaksi

            return redirect()->route('manajemen-lomba.index')->with('success', 'Lomba berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack(); // Membatalkan transaksi jika terjadi error

            return redirect()->route('manajemen-lomba.index')->with('error', 'Terjadi kesalahan. Silakan coba lagi.');
        }
    }

    // public function burungIndex()
    // {
    //     // Menampilkan data burung yang ada
    //     $burungs = Burung::with('jenisBurung', 'kelas')->get(); // Jika ada relasi dengan jenisBurung atau kelas
    //     return view('manajemen_lomba.burung.index', compact('burungs'));
    // }

    // Hapus lomba
    public function destroy($id)
    {
        DB::beginTransaction(); // Memulai transaksi DB

        try {
            $lomba = Lomba::findOrFail($id);
            $lomba->delete();

            DB::commit(); // Menyimpan transaksi

            return redirect()->route('manajemen-lomba.index')->with('success', 'Lomba berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack(); // Membatalkan transaksi jika terjadi error

            return redirect()->route('manajemen-lomba.index')->with('error', 'Terjadi kesalahan. Silakan coba lagi.');
        }
    }
}
