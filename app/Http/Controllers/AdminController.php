<?php

namespace App\Http\Controllers;

use App\Models\Penilaian;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function monitorPenilaian()
    {
        $penilaians = Penilaian::with([
            'user',
            'blokGantangan.gantangan',
            'blokGantangan.blok',
            'bendera',
            'tahap',
            'burung.jenisBurung',
            'burung.kelas'
        ])->get();

        return view('admin.monitor.index', compact('penilaians'));
    }

    public function monitorShow($id)
    {
        $penilaian = Penilaian::with([
            'user',
            'blokGantangan.gantangan',
            'blokGantangan.blok',
            'bendera',
            'tahap',
            'burung.jenisBurung',
            'burung.kelas'
        ])->findOrFail($id);

        return view('admin.monitor.show', compact('penilaian'));
    }

    // Controller untuk hasil lomba (juara)
    public function hasilLomba()
    {
        // Ambil penilaian tahap Ajuan (tahap_id = 1) dengan bendera hijau
        $ajuan = Penilaian::with([
            'blokGantangan.gantangan.pemesanan.user',
            'bendera',
            'burung.jenisBurung',
            'burung.kelas',
        ])
            ->where('tahap_id', 1)
            ->whereHas('bendera', fn($q) => $q->where('nama', 'hijau'))
            ->whereNull('penilaians.deleted_at')
            ->get();

        // Ambil penilaian tahap Koncer
        $koncer = Penilaian::with([
            'blokGantangan.gantangan.pemesanan.user',
            'bendera',
            'burung.jenisBurung',
            'burung.kelas',
        ])
            ->where('tahap_id', 2)
            ->whereNull('penilaians.deleted_at')
            ->get();

        // Gabung hasil akhir semua grup
        $hasilGabungan = collect();

        // Kelompokkan berdasarkan jenis burung dan kelas: "jenis|kelas"
        $ajuanGroupedByJenisKelas = $ajuan->groupBy(
            fn($item) => ($item->burung->jenisBurung->nama ?? '-') . '|' . ($item->burung->kelas->nama ?? '-')
        );

        $koncerGroupedByJenisKelas = $koncer->groupBy(
            fn($item) => ($item->burung->jenisBurung->nama ?? '-') . '|' . ($item->burung->kelas->nama ?? '-')
        );

        // Proses tiap grup jenis_kelas
        foreach ($ajuanGroupedByJenisKelas as $jenisKelas => $ajuanGroup) {
            [$jenis, $kelas] = explode('|', $jenisKelas);

            // Group lagi berdasarkan nomor gantangan untuk hitung jumlah hijau
            $ajuanGroupedByNomor = $ajuanGroup->groupBy(fn($item) => $item->blokGantangan->gantangan->nomor);
            $hijauCounts = $ajuanGroupedByNomor->map(fn($group) => $group->count());

            $maxHijau = $hijauCounts->max();
            $nomorDenganHijauMax = $hijauCounts->filter(fn($count) => $count === $maxHijau);

            // Jika hanya satu nomor dengan bendera hijau terbanyak -> Juara 1 dari tahap Ajuan
            if ($nomorDenganHijauMax->count() === 1) {
                $nomorJuara = $nomorDenganHijauMax->keys()->first();
                $first = $ajuanGroupedByNomor[$nomorJuara]->first();

                // Ambil pemesanan yang gantangan dan lomba sama, dan jenis burung + kelas dari burung sesuai
                $pemesanan = $first->blokGantangan->gantangan->pemesanan
                    ->where('lomba_id', $first->lomba_id)
                    ->filter(function ($pemesanan) use ($first) {
                        return $pemesanan->burung->jenisBurung->id === ($first->burung->jenisBurung->id ?? null)
                            && $pemesanan->burung->kelas->id === ($first->burung->kelas->id ?? null);
                    })
                    ->first();

                $pemesan = $pemesanan?->user?->name ?? 'Belum Dipesan';

                $hasilGabungan->push((object)[
                    'nomor_gantangan' => $nomorJuara,
                    'pemesan' => $pemesan,
                    'jenis' => $jenis . ' - ' . $kelas,
                    'total_poin' => $maxHijau,
                    'status' => 'Juara 1',
                    'jenis_burung_id' => $first->burung->jenisBurung->id ?? null,
                    'kelas_id' => $first->burung->kelas->id ?? null,
                ]);
            }

            // Proses Koncer untuk grup jenis_kelas ini
            $koncerGroup = $koncerGroupedByJenisKelas->get($jenisKelas, collect());

            $koncerGroupedByNomor = $koncerGroup->groupBy(fn($item) => $item->blokGantangan->gantangan->nomor);

            $hasilKoncer = $koncerGroupedByNomor->map(function ($group) use ($jenis, $kelas) {
                $totalPoin = $group->sum(fn($item) => $item->bendera->point ?? 0);
                $first = $group->first();

                $pemesanan = $first->blokGantangan->gantangan->pemesanan
                    ->where('lomba_id', $first->lomba_id)
                    ->filter(function ($pemesanan) use ($first) {
                        return $pemesanan->burung->jenisBurung->id === ($first->burung->jenisBurung->id ?? null)
                            && $pemesanan->burung->kelas->id === ($first->burung->kelas->id ?? null);
                    })
                    ->first();

                $pemesan = $pemesanan?->user?->name ?? 'Belum Dipesan';

                return (object)[
                    'nomor_gantangan' => $first->blokGantangan->gantangan->nomor,
                    'pemesan' => $pemesan,
                    'jenis' => $jenis . ' - ' . $kelas,
                    'total_poin' => $totalPoin,
                    'status' => 'Belum Dinilai',
                    'jenis_burung_id' => $first->burung->jenisBurung->id ?? null,
                    'kelas_id' => $first->burung->kelas->id ?? null,
                ];
            });

            // Urutkan berdasarkan poin tertinggi
            $hasilKoncer = $hasilKoncer->sortByDesc('total_poin')->values();

            // Penentuan status juara tahap Koncer, nomor mulai dari setelah Juara 1 (jika ada)
            $rank = $hasilGabungan->filter(fn($item) => $item->jenis === ($jenis . ' - ' . $kelas))->count() + 1;
            $prevPoin = null;
            $prevRank = $rank;

            $kelompokPoin = $hasilKoncer->groupBy('total_poin');

            foreach ($kelompokPoin as $poin => $items) {
                if ($items->count() > 1) {
                    foreach ($items as $item) {
                        $item->status = 'Toss';
                        $hasilGabungan->push($item);
                    }
                    $rank += $items->count(); // skip peringkat karena toss
                } else {
                    $item = $items->first();
                    $item->status = 'Juara ' . $rank;
                    $hasilGabungan->push($item);
                    $rank++;
                }
            }
        }

        // Urutkan hasil akhir berdasarkan jenis, kelas, dan rank (Juara 1,2,...)
        $hasilGabungan = $hasilGabungan->sortBy([
            fn($item) => $item->jenis,
            fn($item) => intval(str_replace('Juara ', '', $item->status)),
        ])->values();

        return view('admin.hasil_lomba.index', ['penilaians' => $hasilGabungan]);
    }

    public function hasilShow($nomor, Request $request)
    {
        $jenisBurungId = $request->query('jenisBurungId');
        $kelasId = $request->query('kelasId');

        // Ambil data koncer (tahap_id = 2)
        $penilaians = Penilaian::with([
            'user',
            'blokGantangan.gantangan',
            'blokGantangan.blok',
            'bendera',
            'tahap',
            'burung.jenisBurung',
            'burung.kelas',
        ])
            ->whereHas('blokGantangan.gantangan', fn($query) => $query->where('nomor', $nomor))
            ->whereHas('burung', function ($query) use ($jenisBurungId, $kelasId) {
                $query->where('jenis_burung_id', $jenisBurungId)
                    ->where('kelas_id', $kelasId);
            })
            ->where('tahap_id', 2)
            ->get();

        // Jika koncer kosong, coba ambil ajuan
        if ($penilaians->isEmpty()) {
            $penilaians = Penilaian::with([
                'user',
                'blokGantangan.gantangan',
                'blokGantangan.blok',
                'bendera',
                'tahap',
                'burung.jenisBurung',
                'burung.kelas',
            ])
                ->whereHas('blokGantangan.gantangan', fn($query) => $query->where('nomor', $nomor))
                ->whereHas('burung', function ($query) use ($jenisBurungId, $kelasId) {
                    $query->where('jenis_burung_id', $jenisBurungId)
                        ->where('kelas_id', $kelasId);
                })
                ->where('tahap_id', 1) // tahap ajuan
                ->get();

            // Jika tetap tidak ada, baru 404
            if ($penilaians->isEmpty()) {
                abort(404, 'Data tidak ditemukan');
            }
        }

        return view('admin.hasil_lomba.show', compact('penilaians', 'nomor'));
    }
}
