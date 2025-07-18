<?php

namespace App\Http\Controllers;

use App\Models\Blok;
use App\Models\BlokGantangan;
use App\Models\Burung;
use App\Models\JuriTugas;
use App\Models\Penilaian;
use App\Models\Tahap;
use App\Models\Lomba;
use App\Models\JenisBurung;
use App\Models\Gantangan;
use App\Models\Kelas;
use App\Models\Bendera;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class JuriController extends Controller
{
    // Menampilkan halaman pilih lomba
    public function pilihLomba()
    {
        $lombas = Lomba::where('status_lomba_id', 1)->get(); // Ambil lomba yang statusnya aktif
        return view('juri.ajuan.pilih_lomba', compact('lombas'));
    }


    public function ajuanIndex($lombaId)
    {
        $lomba = Lomba::findOrFail($lombaId);
        $user = Auth::user(); // juri yang login

        // Ambil blok yang ditugaskan ke juri pada lomba tersebut
        $bloks = Blok::with('gantangans.gantangan')
            ->where('lomba_id', $lomba->id)
            ->whereIn('id', function ($query) use ($lomba, $user) {
                $query->select('blok_id')
                    ->from('juri_tugas')
                    ->where('lomba_id', $lomba->id)
                    ->where('user_id', $user->id)
                    ->whereNull('deleted_at');
            })
            ->get();

        // Ambil semua kelas di lomba ini
        $kelas = $lomba->kelas;

        // Ambil semua burung yang terkait dengan kelas tersebut
        $kelasIds = $kelas->pluck('id');

        $burungs = \App\Models\Burung::whereIn('kelas_id', $kelasIds)
            ->whereNull('deleted_at')
            ->get();

        // Ambil jenis burung unik dari burungs
        $jenisBurungIds = $burungs->pluck('jenis_burung_id')->unique();

        $jenisBurungs = \App\Models\JenisBurung::whereIn('id', $jenisBurungIds)
            ->whereNull('deleted_at')
            ->get();

        // Tampilkan data pada view
        return view('juri.ajuan.index', compact('lomba', 'jenisBurungs', 'kelas', 'bloks'));
    }


    public function getBlok(Request $request)
    {
        $userId = Auth::id();
        $jenis_burung_id = $request->jenis_burung_id;
        $kelas_id = $request->kelas_id;
        $lomba_id = $request->lomba_id;

        $lomba = Lomba::with(['kelas.burungs'])->find($lomba_id);

        $filterJuri = JuriTugas::with(['blok.burung'])
            ->where('user_id', $userId)
            ->whereHas('blok.burung', function ($query) use ($kelas_id) {
                $query->where('kelas_id', $kelas_id);
            })
            ->get();

        if ($filterJuri->isEmpty()) {
            return response()->json([
                'bloks' => []
            ]);
        }

        // Filter burung sesuai filter
        $filteredBurungs = $lomba->kelas->flatMap(function ($kelas) use ($jenis_burung_id, $kelas_id) {
            return $kelas->burungs->filter(function ($burung) use ($jenis_burung_id, $kelas_id, $kelas) {
                $matchJenis = !$jenis_burung_id || $burung->jenis_burung_id == $jenis_burung_id;
                $matchKelas = !$kelas_id || $kelas->id == $kelas_id;
                return $matchJenis && $matchKelas;
            });
        });

        // Jika tidak ada burung sesuai filter, berarti blok tidak perlu ditampilkan
        if ($filteredBurungs->isEmpty()) {
            return response()->json(['bloks' => []]);
        }

        $burungInKelas = Burung::where('kelas_id', $kelas_id)
            ->where('jenis_burung_id', $jenis_burung_id)
            ->get()->pluck('id');
        // Ambil blok yang ditugaskan ke juri di lomba
        $bloks = Blok::where('lomba_id', $lomba_id)
            ->whereNull('deleted_at')
            ->whereIn('burung_id', $burungInKelas)
            ->whereIn('id', function ($query) use ($userId, $lomba_id) {
                $query->select('blok_id')
                    ->from('juri_tugas')
                    ->where('lomba_id', $lomba_id)
                    ->where('user_id', $userId)
                    ->whereNull('deleted_at');
            })
            ->get();
        // return $bloks;

        // Status penilaian tahap ajuan
        $tahapAjuan = Tahap::where('nama', 'Ajuan')->first();
        $blokWithStatus = $bloks->map(function ($blok) use ($userId, $lomba_id, $tahapAjuan, $filteredBurungs) {
            // Ambil daftar blok_gantangan id untuk blok ini
            $blokGantanganIds = BlokGantangan::where('blok_id', $blok->id)->pluck('id');

            // Buat array id burung dari $filteredBurungs
            $burungIds = $filteredBurungs->pluck('id');

            // Cek kombinasi user, lomba, tahap, blok_gantangan, dan burung
            $sudahDinilai = Penilaian::where('user_id', $userId)
                ->where('lomba_id', $lomba_id)
                ->where('tahap_id', $tahapAjuan->id)
                ->whereIn('blok_gantangan_id', $blokGantanganIds)
                ->whereIn('burung_id', $burungIds)
                ->exists();

            return [
                'id' => $blok->id,
                'nama' => $blok->nama,
                'sudah_dinilai' => $sudahDinilai,
            ];
        });

        return response()->json([
            'bloks' => $blokWithStatus,
        ]);
    }

    public function getKelas(Request $request)
    {
        $jenis_burung_id = $request->jenis_burung_id;

        $kelas = Kelas::whereIn('id', function ($query) use ($jenis_burung_id) {
            $query->select('kelas_id')
                ->from('burungs')
                ->where('jenis_burung_id', $jenis_burung_id)
                ->whereNull('deleted_at');
        })->get();

        return response()->json([
            'kelas' => $kelas,
        ]);
    }

    public function ajuanShow($lombaId, $blokId)
    {
        $userId = Auth::id();
        $lombaAktif = Lomba::findOrFail($lombaId);

        // Load blok dengan relasi gantangans dan gantangan
        $blok = Blok::with(['gantangans.gantangan'])->where('id', $blokId)->firstOrFail();

        $cekTugas = JuriTugas::where('user_id', $userId)
            ->where('blok_id', $blokId)
            ->where('lomba_id', $lombaAktif->id)
            ->exists();

        if (!$cekTugas) {
            abort(403, 'Anda tidak memiliki akses ke blok ini.');
        }

        $benderas = Bendera::all();

        // ✅ Sorting berdasarkan nomor gantangan
        $blok->gantangans = $blok->gantangans->sortBy(function ($item) {
            return $item->gantangan->nomor;
        })->values(); // reset key agar foreach tidak aneh

        $hitamId = $benderas->where('nama', 'hitam')->first()?->id ?? null;
        $blokGantanganIds = $blok->gantangans->pluck('id')->toArray();
        $blokGantanganTerblokir = Penilaian::whereIn('blok_gantangan_id', $blokGantanganIds)
            ->where('lomba_id', $lombaAktif->id)
            ->where('bendera_id', $hitamId)
            ->where('user_id', '!=', $userId)
            ->pluck('blok_gantangan_id')
            ->toArray();

        return view('juri.ajuan.show', [
            'blok' => $blok,
            'lomba' => $lombaAktif,
            'benderas' => $benderas,
            'lombaId' => $lombaAktif->id,
            'blokId' => $blok->id,
            'gantangans' => $blok->gantangans->pluck('gantangan')->flatten(), // tidak wajib sebenarnya
            'blokGantanganTerblokir' => $blokGantanganTerblokir, // 👈 kirim ke view
        ]);
    }
    // Menampilkan nomor gantangan dalam blok tertentu untuk tahap ajuan

    public function storePenilaian(Request $request, $lombaId)
    {
        $userId = Auth::id();
        $blokId = $request->blok_id;

        $validated = $request->validate([
            'jenis_burung_id' => 'required|exists:jenis_burungs,id',
            'kelas_id' => 'required|exists:kelas,id',
            'lomba_id' => 'required',
            'penilaian' => 'nullable|array',
            'penilaian.*.gantanganId' => 'required|exists:blok_gantangans,id',
            'penilaian.*.bendera' => 'nullable|exists:benderas,id',
        ]);

        // dd($request->all());
        // Ambil burung_id dari jenis burung + kelas
        $burung = Burung::where('jenis_burung_id', $validated['jenis_burung_id'])
            ->where('kelas_id', $validated['kelas_id'])
            ->first();

        if (!$burung) {
            return redirect()->back()->with('error', 'Burung untuk jenis dan kelas yang dipilih tidak ditemukan.');
        }

        $tahapAjuan = Tahap::where('nama', 'Ajuan')->firstOrFail();

        DB::beginTransaction();

        try {
            $sudahDiproses = [];

            if (!empty($validated['penilaian'])) {
                foreach ($validated['penilaian'] as $penilaianData) {
                    $blokGantanganId = $penilaianData['gantanganId'];

                    if (in_array($blokGantanganId, $sudahDiproses)) continue;
                    $sudahDiproses[] = $blokGantanganId;

                    $benderaId = $penilaianData['bendera'] ?? null;

                    Penilaian::updateOrCreate(
                        [
                            'user_id' => $userId,
                            'lomba_id' => $lombaId,
                            'blok_gantangan_id' => $blokGantanganId,
                            'tahap_id' => $tahapAjuan->id,
                            'burung_id' => $burung->id,  // pakai burung dari filter jenis+kelas
                        ],
                        [
                            'bendera_id' => $benderaId,
                            'status_penilaian_id' => $benderaId ? 2 : 7,
                        ]
                    );
                }
            } else {
                if (!$blokId) {
                    throw new \Exception('ID blok tidak tersedia.');
                }

                $blokGantanganIds = BlokGantangan::where('blok_id', $blokId)->pluck('id');

                foreach ($blokGantanganIds as $blokGantanganId) {
                    Penilaian::firstOrCreate(
                        [
                            'user_id' => $userId,
                            'lomba_id' => $lombaId,
                            'blok_gantangan_id' => $blokGantanganId,
                            'tahap_id' => $tahapAjuan->id,
                            'burung_id' => $burung->id,  // juga gunakan burung yang sama
                        ],
                        [
                            'bendera_id' => null,
                            'status_penilaian_id' => 7,
                        ]
                    );
                }
            }
            DB::commit();

            return redirect()->route('penilaian.index', $lombaId)
                ->with('success', 'Penilaian berhasil disimpan.')
                ->with('kelas_id', $request->kelas_id)
                ->with('jenis_id', $request->jenis_burung_id)
                ->with('lomba_id', $request->lomba_id);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function koncerIndex()
    {
        $lombas = Lomba::where('status_lomba_id', 1)->get(); // Ambil lomba yang statusnya aktif
        return view('juri.koncer.index', compact('lombas'));
    }

    public function koncerShow($lombaId)
    {
        $user = Auth::user(); // Juri yang sedang login
        Log::info('User ID: ' . $user->id . ' - Mengakses Penilaian Koncer', ['lomba_id' => $lombaId]);

        // Ambil data lomba jika juri memang ditugaskan di lomba tersebut
        $lomba = Lomba::where('id', $lombaId)
            ->whereHas('juriTugas', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->first();
        // dd($user, $lomba);


        if (!$lomba) {
            return view('juri.koncer.show', [
                'lomba' => null, // Tambahkan ini!
                'nomorLolosKoncer' => [],
                'menunggu' => true,
                'jenisBurungs' => collect(),
                'nomorDetail' => [],
                'benderas' => [],
            ]);
        }


        // Ambil semua kelas di lomba ini
        $kelas = $lomba->kelas;
        $kelasIds = $kelas->pluck('id');

        // Ambil semua burung yang terkait dengan kelas tersebut
        $burungs = \App\Models\Burung::whereIn('kelas_id', $kelasIds)
            ->whereNull('deleted_at')
            ->get();

        // Ambil jenis burung unik dari burungs
        $jenisBurungIds = $burungs->pluck('jenis_burung_id')->unique();
        $jenisBurungs = \App\Models\JenisBurung::whereIn('id', $jenisBurungIds)
            ->whereNull('deleted_at')
            ->get();

        // Hitung jumlah juri & blok
        $jumlahJuri = JuriTugas::where('lomba_id', $lomba->id)
            ->distinct('user_id')
            ->count('user_id');

        $totalBlok = Blok::where('lomba_id', $lomba->id)->count();
        $totalPenilaianSeharusnya = $jumlahJuri * $totalBlok;

        Log::info('Jumlah juri: ' . $jumlahJuri . ' - Total Blok: ' . $totalBlok, ['lomba_id' => $lombaId]);

        // Hitung total penilaian tahap Ajuan
        $penilaianTerkumpul = Penilaian::where('lomba_id', $lomba->id)
            ->whereHas('tahap', fn($q) => $q->where('nama', 'Ajuan'))
            ->count();

        Log::info('Penilaian terkumpul: ' . $penilaianTerkumpul, ['lomba_id' => $lombaId]);

        // Belum cukup penilaian ajuan
        if ($penilaianTerkumpul < $totalPenilaianSeharusnya) {
            Log::warning('Penilaian Ajuan belum cukup', ['lomba_id' => $lombaId, 'penilaian_terkumpul' => $penilaianTerkumpul]);
            return view('juri.koncer.show', [
                'lomba' => $lomba,
                'nomorLolosKoncer' => [],
                'nomorDetail' => [],
                'benderas' => [],
                'jenisBurungs' => $jenisBurungs,
                'menunggu' => true,
            ]);
        }

        // Ambil ID bendera Hijau
        $benderaHijauId = Bendera::where('nama', 'Hijau')->value('id');
        Log::info('ID Bendera Hijau: ' . $benderaHijauId);

        // Hitung jumlah bendera hijau per nomor
        $jumlahHijauPerNomor = Penilaian::select('blok_gantangan_id', DB::raw('COUNT(*) as total_hijau'))
            ->where('lomba_id', $lomba->id)
            ->where('bendera_id', $benderaHijauId)
            ->whereHas('tahap', fn($q) => $q->where('nama', 'Ajuan'))
            ->groupBy('blok_gantangan_id')
            ->get();

        Log::info('Jumlah bendera hijau per nomor', ['jumlah_hijau' => $jumlahHijauPerNomor]);

        if ($jumlahHijauPerNomor->isEmpty()) {
            Log::warning('Tidak ada bendera hijau ditemukan', ['lomba_id' => $lomba->id]);
            return view('juri.koncer.show', [
                'lomba' => $lomba,
                'nomorLolosKoncer' => [],
                'nomorDetail' => [],
                'benderas' => [],
                'jenisBurungs' => $jenisBurungs,
                'menunggu' => false,
            ]);
        }

        // Cari nilai hijau tertinggi
        $maxHijau = $jumlahHijauPerNomor->max('total_hijau');

        // Filter nomor yang lolos ke koncer
        $nomorLolosKoncer = $jumlahHijauPerNomor->filter(function ($item) use ($maxHijau) {
            return $item->total_hijau == $maxHijau;
        });

        $nomorLolosKoncerIds = $nomorLolosKoncer->pluck('blok_gantangan_id');

        // Ambil detail nomor yang lolos koncer
        $nomorDetail = BlokGantangan::whereIn('id', $nomorLolosKoncerIds)
            ->with(['blok', 'gantangan'])
            ->get();

        // Ambil bendera untuk koncer
        $benderas = Bendera::all();

        return view('juri.koncer.show', [
            'lomba' => $lomba,
            'nomorLolosKoncer' => $nomorLolosKoncer,
            'nomorDetail' => $nomorDetail,
            'benderas' => $benderas,
            'jenisBurungs' => $jenisBurungs,
            'menunggu' => false,
        ]);
    }

    public function getNomorLolosKoncer(Request $request)
    {
        $lombaId = $request->input('lomba_id');
        $jenisBurungId = $request->input('jenis_burung_id');
        $kelasId = $request->input('kelas_id');
        $juriId = Auth::id();

        Log::info('Memulai proses mendapatkan nomor lolos koncer', compact('lombaId', 'jenisBurungId', 'kelasId', 'juriId'));

        // Validasi apakah juri memang ditugaskan untuk jenis burung dan kelas ini
        $ditugaskan = JuriTugas::where('user_id', $juriId)
            ->where('lomba_id', $lombaId)
            ->whereHas('blok.burung', function ($q) use ($jenisBurungId, $kelasId) {
                $q->where('jenis_burung_id', $jenisBurungId)
                    ->where('kelas_id', $kelasId);
            })
            ->exists();

        if (!$ditugaskan) {
            Log::warning('Juri tidak ditugaskan pada jenis burung & kelas ini', compact('juriId', 'lombaId', 'jenisBurungId', 'kelasId'));

            return response()->json([
                'status' => 'empty',
                'message' => 'Anda tidak ditugaskan pada jenis burung dan kelas ini.',
                'nomorLolosKoncer' => [],
            ], 200);
        }

        if (!$lombaId || !$jenisBurungId || !$kelasId) {
            Log::error('Input tidak lengkap');
            return response()->json([
                'status' => 'error',
                'message' => 'Parameter lomba_id, jenis_burung_id, dan kelas_id wajib diisi',
            ], 400);
        }

        $tahapAjuanId = Tahap::where('nama', 'Ajuan')->value('id');
        $tahapKoncerId = Tahap::where('nama', 'Koncer')->value('id');
        $benderaHijauId = Bendera::where('nama', 'Hijau')->value('id');
        $benderaMerahId = Bendera::where('nama', 'Merah')->value('id');
        $benderaBiruId = Bendera::where('nama', 'Biru')->value('id');
        $benderaKuningId = Bendera::where('nama', 'Kuning')->value('id');
        $benderaPutihId = Bendera::where('nama', 'Putih')->value('id');

        // Hitung kebutuhan penilaian tahap Ajuan
        // $jumlahJuri = JuriTugas::where('lomba_id', $lombaId)->distinct('user_id')->count('user_id');
        $jumlahJuri = JuriTugas::where('lomba_id', $lombaId)
            ->whereHas('blok.burung', function ($q) use ($jenisBurungId, $kelasId) {
                $q->where('jenis_burung_id', $jenisBurungId)
                    ->where('kelas_id', $kelasId);
            })
            ->distinct('user_id')
            ->count('user_id');

        $burungIds = Burung::where('jenis_burung_id', $jenisBurungId)
            ->where('kelas_id', $kelasId)
            ->pluck('id');

        $blokIds = Blok::where('lomba_id', $lombaId)
            ->whereIn('burung_id', $burungIds)
            ->pluck('id');

        $blokGantanganIds = BlokGantangan::whereIn('blok_id', $blokIds)
            ->pluck('id');

        // $blokIds = Blok::where('lomba_id', $lombaId)
        //     ->whereHas('lomba.kelas.burungs', function ($q) use ($jenisBurungId, $kelasId) {
        //         $q->where('jenis_burung_id', $jenisBurungId)
        //             ->where('kelas_id', $kelasId);
        //     })
        //     ->pluck('id');

        // $blokGantanganIds = BlokGantangan::whereIn('blok_id', $blokIds)
        //     ->whereHas('blok.lomba.kelas.burungs', function ($q) use ($jenisBurungId, $kelasId) {
        //         $q->where('jenis_burung_id', $jenisBurungId)
        //             ->where('kelas_id', $kelasId);
        //     })
        //     ->pluck(column: 'id');

        Log::debug('Jumlah juri dan blok gantangan terkait', [
            'jumlah_juri' => $jumlahJuri,
            'blok_gantangan_ids' => $blokGantanganIds->toArray(),
        ]);

        $totalSeharusnya = $jumlahJuri * $blokGantanganIds->count();

        $terisi = Penilaian::where('lomba_id', $lombaId)
            ->where('tahap_id', $tahapAjuanId)
            ->whereIn('blok_gantangan_id', $blokGantanganIds)
            ->whereHas('burung', function ($q) use ($jenisBurungId, $kelasId) {
                $q->where('jenis_burung_id', $jenisBurungId)
                    ->where('kelas_id', $kelasId);
            })
            ->count();

        Log::debug('blokGantanganIds', $blokGantanganIds->toArray());
        Log::debug('Penilaian Ajuan terisi', ['terisi' => $terisi, 'dibutuhkan' => $totalSeharusnya]);

        if ($terisi < $totalSeharusnya) {
            Log::warning('Penilaian Ajuan belum lengkap', ['total_dibutuhkan' => $totalSeharusnya, 'terisi' => $terisi]);
            return response()->json([
                'status' => 'success',
                'menunggu' => true,
                'sudahMenilai' => false,
                'nomorLangsungJuara1' => null,
                'nomorLolosKoncer' => [],
            ]);
        }

        // Hitung jumlah bendera hijau per nomor
        // Ambil semua penilaian tahap Ajuan untuk bendera Hijau dan Putih
        $rekapPenilaian = Penilaian::select(
            'blok_gantangan_id',
            DB::raw("SUM(CASE WHEN bendera_id = {$benderaHijauId} THEN 1 ELSE 0 END) as total_hijau"),
            DB::raw("SUM(CASE WHEN bendera_id = {$benderaPutihId} THEN 1 ELSE 0 END) as total_putih")
        )
            ->where('tahap_id', $tahapAjuanId)
            ->where('lomba_id', $lombaId)
            ->whereIn('blok_gantangan_id', $blokGantanganIds)
            ->whereHas('burung', function ($query) use ($jenisBurungId, $kelasId) {
                $query->where('jenis_burung_id', $jenisBurungId)
                    ->where('kelas_id', $kelasId);
            })
            ->groupBy('blok_gantangan_id')
            ->get()
            ->sortBy([
                ['total_hijau', 'desc'],
                ['total_putih', 'asc'], // putih sebagai penentu tie-break
            ])
            ->values();
        Log::debug('Hasil rekapPenilaian:', $rekapPenilaian->toArray());
        if ($rekapPenilaian->isEmpty()) {
            return response()->json([
                'status' => 'empty',
                'message' => 'Data penilaian tidak ditemukan atau belum lengkap.',
                'nomorLolosKoncer' => [],
            ]);
        }

        $maxHijau = $rekapPenilaian->first()->total_hijau; // karena sudah diurutkan
        $blokGantanganMaxHijau = $rekapPenilaian->filter(fn($item) => $item->total_hijau == $maxHijau);

        $blokGantanganLangsungJuara1 = null;
        $calonKoncer = $rekapPenilaian->filter(
            fn($item) => $item->total_hijau == ($maxHijau - 1)
        );

        $minPutih = $calonKoncer->min('total_putih');

        $blokGantanganIdsKoncer = $calonKoncer
            ->filter(fn($item) => $item->total_putih == $minPutih)
            ->pluck('blok_gantangan_id')
            ->toArray();


        if ($blokGantanganMaxHijau->count() === 1) {
            // Langsung Juara 1
            $blokGantanganLangsungJuara1 = $blokGantanganMaxHijau->first()->blok_gantangan_id;

            // Ambil yang satu tingkat di bawah (total_hijau = max - 1)
            $calonKoncer = $rekapPenilaian->filter(
                fn($item) => $item->total_hijau == ($maxHijau - 1)
            );

            if ($calonKoncer->isNotEmpty()) {
                $minPutih = $calonKoncer->min('total_putih');

                $blokGantanganIdsKoncer = $calonKoncer->filter(
                    fn($item) => $item->total_putih == $minPutih
                )->pluck('blok_gantangan_id')->toArray();
            } else {
                $blokGantanganIdsKoncer = [];
            }
        }

        $dataNomor = BlokGantangan::with(['blok.lomba.kelas.burungs.jenisBurung', 'gantangan'])
            ->whereIn('id', $blokGantanganIdsKoncer)
            ->whereHas('blok.lomba', fn($q) => $q->where('id', $lombaId))
            ->whereHas('blok.lomba.kelas', fn($q) => $q->where('id', $kelasId))
            ->whereHas('blok.lomba.kelas.burungs.jenisBurung', fn($q) => $q->where('id', $jenisBurungId))
            ->get()
            ->map(function ($item) use (
                $juriId,
                $lombaId,
                $tahapKoncerId,
                $benderaMerahId,
                $benderaBiruId,
                $benderaKuningId,
                $jenisBurungId,
                $kelasId
            ) {
                $sudahDinilai = Penilaian::where('user_id', $juriId)
                    ->where('lomba_id', $lombaId)
                    ->where('tahap_id', $tahapKoncerId)
                    ->where('blok_gantangan_id', $item->id)
                    ->whereHas('burung', function ($query) use ($jenisBurungId, $kelasId) {
                        $query->where('jenis_burung_id', $jenisBurungId)
                            ->where('kelas_id', $kelasId);
                    })
                    ->exists();

                return [
                    'id' => $item->id,
                    'gantangan' => [
                        'nomor' => $item->gantangan->nomor,
                    ],
                    'bendera_merah_id' => $benderaMerahId,
                    'bendera_biru_id' => $benderaBiruId,
                    'bendera_kuning_id' => $benderaKuningId,
                    'sudah_dinilai' => $sudahDinilai,
                ];
            });

        $sudahMenilai = $dataNomor->every(fn($item) => $item['sudah_dinilai']);

        // Urutkan berdasarkan nomor gantangan
        $dataNomor = $dataNomor->sortBy(fn($item) => (int) $item['gantangan']['nomor'])->values();


        $nomorLangsungJuara1 = null;
        if ($blokGantanganLangsungJuara1) {
            $item = BlokGantangan::with(['blok.lomba.kelas.burungs.jenisBurung', 'gantangan'])
                ->where('id', $blokGantanganLangsungJuara1)
                ->whereHas('blok.lomba', fn($q) => $q->where('id', $lombaId))
                ->whereHas('blok.lomba.kelas', fn($q) => $q->where('id', $kelasId))
                ->whereHas('blok.lomba.kelas.burungs.jenisBurung', fn($q) => $q->where('id', $jenisBurungId))
                ->first();

            if ($item) {
                $nomorLangsungJuara1 = [
                    'id' => $item->id,
                    'gantangan' => [
                        'nomor' => $item->gantangan->nomor,
                    ],
                ];
            }
        }

        return response()->json([
            'status' => 'success',
            'menunggu' => false,
            'sudahMenilai' => $sudahMenilai,
            'nomorLangsungJuara1' => $nomorLangsungJuara1,
            'nomorLolosKoncer' => $dataNomor,
        ]);
    }

    public function storeKoncer(Request $request, $lombaId)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'jenis_burung_id' => 'required|exists:jenis_burungs,id',
            'kelas_id' => 'required|exists:kelas,id',
            'penilaian' => 'required|array',
            'penilaian.*.gantanganId' => 'required|exists:blok_gantangans,id',
            'penilaian.*.bendera' => 'nullable|exists:benderas,id',
        ]);

        $burung = Burung::where('jenis_burung_id', $validated['jenis_burung_id'])
            ->where('kelas_id', $validated['kelas_id'])
            ->first();

        if (!$burung) {
            return redirect()->back()->with('error', 'Burung untuk jenis dan kelas yang dipilih tidak ditemukan.');
        }

        $tahapKoncer = Tahap::where('nama', 'Koncer')->first();

        if (!$tahapKoncer) {
            return redirect()->back()->with('error', 'Tahap Koncer tidak ditemukan.');
        }
        // dd($request->all());

        DB::beginTransaction();
        try {
            foreach ($validated['penilaian'] as $penilaian) {
                $blokGantanganId = $penilaian['gantanganId'];
                $benderaId = $penilaian['bendera'] ?? null;

                if ($benderaId) {
                    Penilaian::create([
                        'lomba_id' => $lombaId,
                        'blok_gantangan_id' => $blokGantanganId,
                        'user_id' => $user->id,
                        'tahap_id' => $tahapKoncer->id,
                        'bendera_id' => $benderaId,
                        'status_penilaian_id' => 2, // Sudah Dinilai
                        'burung_id' => $burung->id,
                    ]);
                } else {
                    Penilaian::create([
                        'lomba_id' => $lombaId,
                        'blok_gantangan_id' => $blokGantanganId,
                        'user_id' => $user->id,
                        'tahap_id' => $tahapKoncer->id,
                        'bendera_id' => null,
                        'status_penilaian_id' => 7, // Tidak Dinilai
                        'burung_id' => $burung->id,
                    ]);
                }
            }
            DB::commit();
            return redirect()->back()->with('success', 'Penilaian koncer berhasil disimpan.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Gagal menyimpan penilaian koncer', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->back()->with('error', 'Gagal menyimpan penilaian koncer.');
        }
    }
    // Menampilkan riwayat penilaian
    public function riwayatIndex()
    {
        $userId = Auth::id();

        $penilaians = Penilaian::with([
            'blokGantangan.blok.lomba.kelas.burungs.jenisBurung',
            'bendera',
            'tahap'
        ])
            ->where('user_id', $userId)
            ->get();

        $riwayatAjuan = [];
        $riwayatKoncer = [];

        foreach ($penilaians as $penilaian) {
            $tahap = $penilaian->tahap->nama ?? '';
            $poin = $penilaian->bendera->point ?? 0;

            $penilaian->total_poin = $poin;

            if ($tahap === 'Koncer') {
                $riwayatKoncer[] = $penilaian;
            } else {
                $riwayatAjuan[] = $penilaian;
            }
        }

        return view('juri.riwayat_penilaian.index', [
            'riwayatAjuan' => $riwayatAjuan,
            'riwayatKoncer' => $riwayatKoncer
        ]);
    }

    public function riwayatShow($id)
    {
        $userId = Auth::id();

        // Ambil data riwayat penilaian berdasarkan ID dan user, dengan eager loading relasi yang diperlukan
        $penilaian = Penilaian::where('user_id', $userId)
            ->where('id', $id)
            ->with([
                'blokGantangan.blok',
                'blokGantangan.gantangan',
                'bendera',
                'tahap',
                'burung.jenisBurung',  // relasi burung ke jenisBurung
                'burung.kelas'         // relasi burung ke kelas
            ])
            ->firstOrFail();

        // Pastikan poin diambil dari bendera (jika ada)
        $penilaian->total_poin = $penilaian->bendera->point ?? 0;

        return view('juri.riwayat_penilaian.show', compact('penilaian'));
    }
}

// public function koncerShow($lombaId)
// {
//     $user = Auth::user(); // Juri yang sedang login
//     Log::info('User ID: ' . $user->id . ' - Mengakses Penilaian Koncer', ['lomba_id' => $lombaId]);

//     // Pastikan juri ditugaskan
//     $lomba = Lomba::where('id', $lombaId)
//         ->whereHas('juriTugas', fn($query) => $query->where('user_id', $user->id))
//         ->first();

//     if (!$lomba) {
//         Log::warning('Lomba tidak ditemukan untuk juri', ['user_id' => $user->id, 'lomba_id' => $lombaId]);
//         return view('juri.koncer.show', [
//             'lomba' => null,
//             'jenisBurungs' => JenisBurung::all(),
//         ]);
//     }

//     return view('juri.koncer.show', [
//         'lomba' => $lomba,
//         'jenisBurungs' => JenisBurung::all(),
//     ]);
// }
