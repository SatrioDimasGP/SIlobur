<?php

namespace App\Http\Controllers;

use App\Models\JuriTugas;
use App\Models\User;
use App\Models\Lomba;
use App\Models\Blok;
use App\Models\HargaBurung;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class PenjadwalanJuriController extends Controller
{
    // Menampilkan halaman penjadwalan juri
    public function index()
    {
        // Mengambil data lomba dan blok untuk ditampilkan dalam penjadwalan
        $lombas = Lomba::all();
        $bloks = Blok::all();
        $juris = User::role('juri')->get(); // ✅ menggunakan nama role
        $penugasanJuri = JuriTugas::with([
            'user',
            'lomba',
            'blok.burung.jenisBurung',
            'blok.burung.kelas'
        ])
            ->whereNull('deleted_at')
            ->whereHas('user')   // hanya jika relasi user masih ada
            ->whereHas('lomba')  // hanya jika relasi lomba masih ada
            ->whereHas('blok')   // hanya jika relasi blok masih ada
            ->get();
        // // dd($penugasanJuri;
        // //  foreach ($penugasanJuri as $penugasan) {
        // //         if (!$penugasan->lomba || !$penugasan->user || !$penugasan->blok) {
        // //             dd([
        // //                 'id' => $penugasan->id,
        // //                 'lomba' => $penugasan->lomba,
        // //                 'user' => $penugasan->user,
        // //                 'blok' => $penugasan->blok,
        // //             ]);
        // //         }
        // //     }
        return view('korlap.penugasan_juri.index', compact('lombas', 'bloks', 'juris', 'penugasanJuri'));
    }

    // Menyimpan penugasan juri baru
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'lomba_id' => 'required|exists:lombas,id',
            'burung_id' => 'required|exists:burungs,id',
        ]);

        DB::beginTransaction();
        try {
            $userId = $request->user_id;
            $lombaId = $request->lomba_id;
            $burungId = $request->burung_id;

            // Ambil jenis_burung_id dan kelas_id dari burung
            $burung = DB::table('burungs')
                ->select('jenis_burung_id', 'kelas_id')
                ->where('id', $burungId)
                ->first();

            if (!$burung) {
                return redirect()->back()->withInput()->withErrors(['burung_id' => 'Data burung tidak ditemukan']);
            }

            // Ambil semua kelas dari lomba
            $kelasIds = DB::table('kelas')
                ->where('lomba_id', $lombaId)
                ->pluck('id');

            // Pastikan kelas burung termasuk kelas lomba
            if (!in_array($burung->kelas_id, $kelasIds->toArray())) {
                return redirect()->back()->withInput()->withErrors(['burung_id' => 'Burung tidak sesuai dengan kelas lomba']);
            }

            // Ambil semua blok dari lomba
            $blokIds = Blok::where('burung_id', $burungId)
                ->whereNull('deleted_at')
                ->get()
                ->pluck('id');
            $sudahAda = [];

            foreach ($blokIds as $blokId) {
                $existing = JuriTugas::withTrashed()
                    ->where('user_id', $userId)
                    ->where('lomba_id', $lombaId)
                    ->where('blok_id', $blokId)
                    ->first();

                if ($existing) {
                    if ($existing->trashed()) {
                        $existing->restore();
                        $existing->updated_by = auth::id();
                        $existing->save();
                    } else {
                        $sudahAda[] = $blokId;
                        continue;
                    }
                } else {
                    JuriTugas::create([
                        'user_id' => $userId,
                        'lomba_id' => $lombaId,
                        'blok_id' => $blokId,
                        'created_by' => auth::id(),
                    ]);
                }
            }

            DB::commit();

            if (count($sudahAda) > 0) {
                return redirect()->route('penjadwalan-juri.index')
                    ->with('success', 'Penugasan berhasil, kecuali pada blok: ' . implode(', ', $sudahAda));
            }

            return redirect()->route('penjadwalan-juri.index')->with('success', 'Penugasan juri berhasil ditambahkan untuk semua blok.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->withErrors([
                'error' => 'Gagal menyimpan data: ' . $e->getMessage()
            ]);
        }
    }


    public function getJenisBurungByLomba($lombaId)
    {
        try {
            $data = DB::table('kelas')
                ->join('burungs', 'kelas.id', '=', 'burungs.kelas_id')
                ->join('jenis_burungs', 'burungs.jenis_burung_id', '=', 'jenis_burungs.id')
                ->where('kelas.lomba_id', $lombaId)   // asumsi kelas ada relasi lomba
                ->whereNull('kelas.deleted_at')
                ->whereNull('burungs.deleted_at')
                ->whereNull('jenis_burungs.deleted_at')
                ->distinct()
                ->select(
                    'burungs.id as id',
                    DB::raw("CONCAT(jenis_burungs.nama, ' - ', kelas.nama) as label")
                )
                ->get();

            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal memuat data: ' . $e->getMessage()], 500);
        }
    }

    // Menampilkan halaman untuk mengedit penjadwalan juri
    public function edit($id)
    {
        $penugasanJuri = JuriTugas::findOrFail($id); // Ganti nama variabel
        $lombas = Lomba::all();
        $bloks = Blok::where('lomba_id', $penugasanJuri->lomba_id)->get(); // blok sesuai lomba
        $juris = User::role('juri')->get();

        return view('korlap.penugasan_juri.edit', compact('penugasanJuri', 'lombas', 'bloks', 'juris'));
    }

    // Memperbarui penjadwalan juri
    public function update(Request $request, $id)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'lomba_id' => 'required|exists:lombas,id',
            'blok_id' => 'required|exists:bloks,id',
        ]);

        $penjadwalanJuri = JuriTugas::findOrFail($id);

        $exists = JuriTugas::withTrashed()
            ->where('id', '!=', $id)
            ->where('user_id', $request->user_id)
            ->where('lomba_id', $request->lomba_id)
            ->where('blok_id', $request->blok_id)
            ->exists();

        if ($exists) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['kombinasi' => 'Penugasan juri ini sudah ada.']);
        }

        DB::beginTransaction();
        try {
            $penjadwalanJuri->update([
                'user_id' => $request->user_id,
                'lomba_id' => $request->lomba_id,
                'blok_id' => $request->blok_id,
                'updated_by' => Auth::id(),
            ]);
            DB::commit();
            return redirect()->route('penjadwalan-juri.index')->with('success', 'Penjadwalan juri berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->withErrors(['error' => 'Gagal memperbarui data: ' . $e->getMessage()]);
        }
    }




    // Tambahkan fungsi ini di PenjadwalanJuriController
    // public function getBlokByLomba($lombaId)
    // {
    //     $bloks = Blok::where('lomba_id', $lombaId)->get();
    //     return response()->json($bloks);
    // }


    // Menghapus penugasan juri


    public function destroy($id)
    {
        $penugasan = JuriTugas::findOrFail($id);
        $penugasan->delete();

        return redirect()->route('penjadwalan-juri.index')->with('success', 'Penugasan juri berhasil dihapus.');
    }
}
