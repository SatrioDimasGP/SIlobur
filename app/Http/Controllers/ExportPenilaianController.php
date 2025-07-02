<?php

namespace App\Http\Controllers;

use App\Models\Penilaian;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Facades\Response;

class ExportPenilaianController extends Controller
{
    public function export()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header kolom
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Nama Juri');
        $sheet->setCellValue('C1', 'Nomor Gantangan');
        $sheet->setCellValue('D1', 'Blok');
        $sheet->setCellValue('E1', 'Lomba');
        $sheet->setCellValue('F1', 'Bendera');
        $sheet->setCellValue('G1', 'Tahap');
        $sheet->setCellValue('H1', 'Status');

        // Ambil data penilaian
        $data = Penilaian::with([
            'user',
            'blokGantangan',
            'blokGantangan.blok',
            'blokGantangan.gantangan',
            'lomba',
            'bendera',
            'tahap',
            'statusPenilaian'
        ])->get();

        $row = 2;
        foreach ($data as $index => $item) {
            $sheet->setCellValue("A$row", $index + 1);
            $sheet->setCellValue("B$row", $item->user->name ?? '-');
            $sheet->setCellValue("C$row", $item->blokGantangan->gantangan->nomor ?? '-');
            $sheet->setCellValue("D$row", $item->blokGantangan->blok->nama ?? '-');
            $sheet->setCellValue("E$row", $item->lomba->nama ?? '-');
            $sheet->setCellValue("F$row", $item->bendera->nama ?? '-');
            $sheet->setCellValue("G$row", $item->tahap->nama ?? '-');
            $sheet->setCellValue("H$row", $item->statusPenilaian->nama ?? '-');
            $row++;
        }

        // Simpan ke file sementara
        $filename = 'penilaian.xlsx';
        $writer = new Xlsx($spreadsheet);
        $tempFile = tempnam(sys_get_temp_dir(), $filename);
        $writer->save($tempFile);

        // Kembalikan file ke user sebagai download
        return response()->download($tempFile, $filename)->deleteFileAfterSend(true);
    }
}
