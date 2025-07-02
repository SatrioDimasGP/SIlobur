<?php

namespace App\Http\Controllers;

use App\Models\Penilaian;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ExportHasilLombaController extends Controller
{
    public function export()
    {
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

        $koncer = Penilaian::with([
            'blokGantangan.gantangan.pemesanan.user',
            'bendera',
            'burung.jenisBurung',
            'burung.kelas',
        ])
            ->where('tahap_id', 2)
            ->whereNull('penilaians.deleted_at')
            ->get();

        $hasilGabungan = collect();

        $ajuanGroupedByJenisKelas = $ajuan->groupBy(
            fn($item) => ($item->burung->jenisBurung->nama ?? '-') . '|' . ($item->burung->kelas->nama ?? '-')
        );

        $koncerGroupedByJenisKelas = $koncer->groupBy(
            fn($item) => ($item->burung->jenisBurung->nama ?? '-') . '|' . ($item->burung->kelas->nama ?? '-')
        );

        foreach ($ajuanGroupedByJenisKelas as $jenisKelas => $ajuanGroup) {
            [$jenis, $kelas] = explode('|', $jenisKelas);
            $ajuanGroupedByNomor = $ajuanGroup->groupBy(fn($item) => $item->blokGantangan->gantangan->nomor);
            $hijauCounts = $ajuanGroupedByNomor->map(fn($group) => $group->count());
            $maxHijau = $hijauCounts->max();
            $nomorDenganHijauMax = $hijauCounts->filter(fn($count) => $count === $maxHijau);

            if ($nomorDenganHijauMax->count() === 1) {
                $nomorJuara = $nomorDenganHijauMax->keys()->first();
                $first = $ajuanGroupedByNomor[$nomorJuara]->first();

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
                ]);
            }

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
                ];
            });

            $hasilKoncer = $hasilKoncer->sortByDesc('total_poin')->values();
            $rank = $hasilGabungan->filter(fn($item) => $item->jenis === ($jenis . ' - ' . $kelas))->count() + 1;
            $kelompokPoin = $hasilKoncer->groupBy('total_poin');

            foreach ($kelompokPoin as $poin => $items) {
                if ($items->count() > 1) {
                    foreach ($items as $item) {
                        $item->status = 'Toss';
                        $hasilGabungan->push($item);
                    }
                    $rank += $items->count();
                } else {
                    $item = $items->first();
                    $item->status = 'Juara ' . $rank;
                    $hasilGabungan->push($item);
                    $rank++;
                }
            }
        }

        $hasilGabungan = $hasilGabungan->sortBy([
            fn($item) => $item->jenis,
            fn($item) => intval(str_replace('Juara ', '', $item->status)),
        ])->values();

        // EXPORT TO EXCEL
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Nomor Gantangan');
        $sheet->setCellValue('C1', 'Pemilik');
        $sheet->setCellValue('D1', 'Jenis Burung');
        $sheet->setCellValue('E1', 'Total Poin');
        $sheet->setCellValue('F1', 'Status Juara');

        $row = 2;
        foreach ($hasilGabungan as $index => $p) {
            $sheet->setCellValue("A$row", $index + 1);
            $sheet->setCellValue("B$row", $p->nomor_gantangan);
            $sheet->setCellValue("C$row", $p->pemesan);
            $sheet->setCellValue("D$row", $p->jenis);
            $sheet->setCellValue("E$row", $p->total_poin);
            $sheet->setCellValue("F$row", $p->status);
            $row++;
        }

        $filename = 'hasil_lomba.xlsx';
        $writer = new Xlsx($spreadsheet);
        $tempFile = tempnam(sys_get_temp_dir(), $filename);
        $writer->save($tempFile);

        return response()->download($tempFile, $filename)->deleteFileAfterSend(true);
    }
}
