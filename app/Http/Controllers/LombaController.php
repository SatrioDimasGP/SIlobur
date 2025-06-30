<?php

namespace App\Http\Controllers;

use App\Models\Lomba;
use App\Models\Pemesanan;
use App\Models\Penilaian;
use App\Models\BlokGantangan;
use Illuminate\Support\Facades\Auth;

class LombaController extends Controller
{

    // 2. Tampilkan lomba yang diikuti user
    public function index()
    {
        $pemesanans = Pemesanan::with(['lomba', 'gantangan', 'status', 'burung.jenisBurung', 'burung.kelas'])
            ->where('user_id', Auth::id())
            ->orderByDesc('created_at')
            ->get()
            ->groupBy(function ($item) {
                // Group berdasarkan created_at detik yang sama
                return $item->created_at->format('Y-m-d H:i:s');
            });
        // dd($pemesanans);

        return view('customer.lomba.index', compact('pemesanans'));
    }

    // Menampilkan hasil lomba yang dimiliki oleh user
    public function hasilLombaUser()
    {
        $user = auth::user();

        // Ambil semua jenis-kelas burung dari pemesanan user
        $jenisKelasUser = \App\Models\Pemesanan::with(['burung.jenisBurung', 'burung.kelas'])
            ->where('user_id', $user->id)
            ->get()
            ->pluck('burung')
            ->filter()
            ->map(fn($b) => $b->jenis_burung_id . '-' . $b->kelas_id)
            ->unique();

        // Ambil semua penilaian yang relevan dengan eager loading lengkap
        $semuaPenilaian = \App\Models\Penilaian::with([
            'user',
            'blokGantangan.gantangan',
            'blokGantangan.blok.lomba',
            'bendera',
            'tahap',
            'burung.jenisBurung',
            'burung.kelas',
            'statusPenilaian'
        ])
            ->whereNull('penilaians.deleted_at')
            ->get()
            ->filter(function ($p) use ($jenisKelasUser) {
                $burung = $p->burung;
                if (!$burung) return false;
                $key = $burung->jenis_burung_id . '-' . $burung->kelas_id;
                return $jenisKelasUser->contains($key);
            });

        // Kelompokkan penilaian per jenis-kelas
        $groupByJenisKelas = $semuaPenilaian->groupBy(fn($p) => $p->burung->jenis_burung_id . '-' . $p->burung->kelas_id);

        $hasilFinal = collect();

        foreach ($groupByJenisKelas as $jenisKelasKey => $penilaianPerJenisKelas) {
            // Kelompokkan per nomor gantangan
            $groupByNomor = $penilaianPerJenisKelas->groupBy(fn($p) => $p->blokGantangan->gantangan->nomor);

            $dataNomor = collect();

            foreach ($groupByNomor as $nomor => $penilaianPerNomor) {
                $first = $penilaianPerNomor->first();

                $common = [
                    'nomor_gantangan' => $nomor,
                    'nama_lomba' => $first->blokGantangan->blok->lomba->nama ?? '-',
                    'jenis' => $first->burung->jenisBurung->nama ?? '-',
                    'kelas' => $first->burung->kelas->nama ?? '-',
                    'blok_id' => $first->blokGantangan->blok->id,
                    'blok_gantangan_id' => $first->blok_gantangan_id ?? $first->blokGantangan->id ?? null,
                    'jenis_kelas_key' => $jenisKelasKey,
                    'burung_id' => $first->burung->id ?? null, // <<< tambahan ini
                ];

                // Ambil penilaian per tahap: Ajuan (1) dan Koncer (2)
                $ajuan = $penilaianPerNomor->where('tahap_id', 1);
                $koncer = $penilaianPerNomor->where('tahap_id', 2);

                // Hitung total poin untuk masing-masing tahap
                $totalAjuan = $ajuan->where('bendera_id', 1)->count(); // bendera hijau
                $totalKoncer = $koncer->sum(fn($p) => $p->bendera->point ?? 0);

                // Simpan detail tiap penilaian juri untuk tahap Ajuan dan Koncer (optional, bisa ditampilkan di detail)
                $detailAjuan = $ajuan->map(fn($p) => [
                    'juri' => $p->user->name,
                    'bendera' => $p->bendera->nama ?? '-',
                    'point' => $p->bendera->point ?? 0,
                    'status_penilaian' => $p->statusPenilaian->nama ?? '-',
                ]);

                $detailKoncer = $koncer->map(fn($p) => [
                    'juri' => $p->user->name,
                    'bendera' => $p->bendera->nama ?? '-',
                    'point' => $p->bendera->point ?? 0,
                    'status_penilaian' => $p->statusPenilaian->nama ?? '-',
                ]);

                // Masukkan data tahap Ajuan sebagai satu record
                $dataNomor->push((object) array_merge($common, [
                    'total' => $totalAjuan,
                    'status_juara' => null,
                    'tahap' => 'Ajuan',
                    'tahap_id' => 1,
                    'detail' => $detailAjuan,
                ]));

                // Masukkan data tahap Koncer sebagai satu record jika ada penilaian tahap 2
                if ($koncer->count() > 0) {
                    $dataNomor->push((object) array_merge($common, [
                        'total' => $totalKoncer,
                        'status_juara' => null,
                        'tahap' => 'Koncer',
                        'tahap_id' => 2,
                        'detail' => $detailKoncer,
                    ]));
                }
            }

            // PENENTUAN JUARA SESUAI LOGIKA:

            $ajuanData = $dataNomor->where('tahap_id', 1);

            if ($ajuanData->isEmpty()) {
                // Jika tidak ada data ajuan, lanjut ke jenis-kelas berikutnya
                $hasilFinal = $hasilFinal->merge($dataNomor);
                continue;
            }

            $maxAjuan = $ajuanData->max('total');
            $nomorMaxAjuan = $ajuanData->where('total', $maxAjuan);

            if ($nomorMaxAjuan->count() > 1) {
                // Lebih dari satu nomor dengan bendera hijau terbanyak → semuanya masuk Koncer untuk rebut juara 1 dst

                $koncerData = $dataNomor->where('tahap_id', 2)
                    ->filter(fn($item) => $nomorMaxAjuan->pluck('nomor_gantangan')->contains($item->nomor_gantangan));

                if ($koncerData->isNotEmpty()) {
                    // Ranking berdasarkan total poin koncer, juara mulai dari 1
                    $rank = 1;
                    $prevPoint = null;
                    foreach ($koncerData->sortByDesc('total')->values() as $item) {
                        if ($prevPoint !== null && $item->total < $prevPoint) {
                            $rank++;
                        }
                        $item->status_juara = 'Juara ' . $rank;
                        $prevPoint = $item->total;
                    }
                }

                // Nomor yang tidak ikut Koncer dan bukan peserta hijau terbanyak status juara tetap null
            } else {
                // Hanya satu nomor dengan hijau terbanyak → langsung Juara 1
                $juara1 = $nomorMaxAjuan->first();
                $juara1->status_juara = 'Juara 1';

                // Nomor dengan bendera hijau terbanyak kedua (bisa banyak) masuk Koncer rebut juara 2 dst
                $secondMaxAjuan = $ajuanData->where('total', '<', $maxAjuan)->max('total');
                if ($secondMaxAjuan !== null) {
                    $nomorSecondAjuan = $ajuanData->where('total', $secondMaxAjuan);

                    $koncerData = $dataNomor->where('tahap_id', 2)
                        ->filter(fn($item) => $nomorSecondAjuan->pluck('nomor_gantangan')->contains($item->nomor_gantangan));

                    if ($koncerData->isNotEmpty()) {
                        $rank = 2;
                        $prevPoint = null;
                        $kelompokPoin = $koncerData->groupBy('total')->sortKeysDesc();

                        foreach ($kelompokPoin as $poin => $items) {
                            if ($items->count() > 1) {
                                foreach ($items as $item) {
                                    $item->status_juara = 'Toss';
                                }
                                $rank += $items->count(); // lewati rank sebanyak peserta toss
                            } else {
                                $item = $items->first();
                                $item->status_juara = 'Juara ' . $rank;
                                $rank++;
                            }
                        }
                    }
                }

                // Nomor lain selain juara 1 dan peserta Koncer kedua tidak berstatus juara
            }

            $hasilFinal = $hasilFinal->merge($dataNomor);
        }

        // Urutkan hasil akhir sebelum dikirim ke view
        $hasilFinal = $hasilFinal->sortBy([
            fn($item) => $item->jenis,
            fn($item) => $item->kelas,
            fn($item) => intval(str_replace('Juara ', '', $item->status_juara ?? '999')),
        ])->values();

        return view('customer.hasil_lomba.index', ['penilaians' => $hasilFinal]);
    }

    // Menampilkan detail hasil lomba untuk user tertentu
    public function hasilLombaShow($blokGantanganId, $burungId, $tahapId)
    {
        $blokGantangan = BlokGantangan::with([
            'gantangan',
            'gantangan.pemesanan', // tambahkan ini
            'gantangan.pemesanan.burung.jenisBurung',
            'gantangan.pemesanan.burung.kelas',
            'blok.lomba'
        ])->findOrFail($blokGantanganId);

        $penilaians = Penilaian::with(['user', 'bendera', 'tahap'])
            ->where('blok_gantangan_id', $blokGantanganId)
            ->where('burung_id', $burungId)    // filter berdasarkan burung_id
            ->where('tahap_id', $tahapId)
            ->get();
        // dd($penilaians);

        return view('customer.hasil_lomba.show', compact('penilaians', 'blokGantangan', 'tahapId', 'burungId'));
    }
}
