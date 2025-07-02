<?php

namespace App\Http\Controllers;

use App\Models\Pemesanan;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class PendaftaranExportController extends Controller
{
    public function export()
    {
        $pemesanans = Pemesanan::with([
            'user',
            'lomba',
            'status',
            'gantangan',
            'burung.jenisBurung',
            'burung.kelas',
        ])->orderBy('created_at', 'asc')->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header kolom
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Nama Pemesan');
        $sheet->setCellValue('C1', 'Lomba');
        $sheet->setCellValue('D1', 'Jenis Burung');
        $sheet->setCellValue('E1', 'Kelas');
        $sheet->setCellValue('F1', 'No Gantangan');
        $sheet->setCellValue('G1', 'Status Pembayaran');

        $row = 2;
        foreach ($pemesanans as $index => $pemesanan) {
            $sheet->setCellValue("A{$row}", $index + 1);
            $sheet->setCellValue("B{$row}", $pemesanan->user->name ?? '-');
            $sheet->setCellValue("C{$row}", $pemesanan->lomba->nama ?? '-');
            $sheet->setCellValue("D{$row}", $pemesanan->burung->jenisBurung->nama ?? '-');
            $sheet->setCellValue("E{$row}", $pemesanan->burung->kelas->nama ?? '-');
            $sheet->setCellValue("F{$row}", $pemesanan->gantangan->nomor ?? '-');
            $sheet->setCellValue("G{$row}", $pemesanan->status->nama ?? '-');
            $row++;
        }

        $filename = 'data_pendaftaran.xlsx';
        $writer = new Xlsx($spreadsheet);
        $tempFile = tempnam(sys_get_temp_dir(), $filename);
        $writer->save($tempFile);

        return response()->download($tempFile, $filename)->deleteFileAfterSend(true);
    }
}
