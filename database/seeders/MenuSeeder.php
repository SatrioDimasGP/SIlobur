<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Menu;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        $menus = [
            ['nama_menu' => 'Manajemen Teknis Lomba', 'url' => '#', 'icon' => 'fas fa-cogs', 'parent_id' => '1', 'urutan' => '1'],
            ['nama_menu' => 'Manajemen Lomba', 'url' => 'manajemen-lomba', 'icon' => 'fas fa-dove', 'parent_id' => '9', 'urutan' => '1'],
            ['nama_menu' => 'Penjadwalan / Penugasan Juri', 'url' => 'penugasan-juri', 'icon' => null, 'parent_id' => '9', 'urutan' => '1'],
            ['nama_menu' => 'Konfigurasi Gantangan & Blok', 'url' => 'konfigurasi-blok', 'icon' => null, 'parent_id' => '9', 'urutan' => '1'],
            ['nama_menu' => 'Monitoring & Hasil', 'url' => '#', 'icon' => 'fas fa-chart-line', 'parent_id' => '1', 'urutan' => '1'],
            ['nama_menu' => 'Penilaian (monitor semua)', 'url' => 'monitor-penilaian', 'icon' => 'fas fa-eye', 'parent_id' => '13', 'urutan' => '1'],
            ['nama_menu' => 'Data Penilaian / Hasil Lomba', 'url' => 'hasil-lomba', 'icon' => 'fas fa-award', 'parent_id' => '13', 'urutan' => '1'],
            ['nama_menu' => 'Pendaftaran', 'url' => null, 'icon' => 'fas fa-clipboard-list', 'parent_id' => '1', 'urutan' => '1'],
            ['nama_menu' => 'Data Pendaftaran', 'url' => 'data-pendaftaran', 'icon' => 'fas fa-clipboard-list', 'parent_id' => '16', 'urutan' => '1'],
            ['nama_menu' => 'Penilaian Saya', 'url' => null, 'icon' => 'fas fa-clipboard-check', 'parent_id' => '1', 'urutan' => '1'],
            ['nama_menu' => 'Tahap Ajuan', 'url' => 'penilaian-ajuan', 'icon' => null, 'parent_id' => '18', 'urutan' => '1'],
            ['nama_menu' => 'Tahap Koncer', 'url' => 'penilaian-koncer', 'icon' => null, 'parent_id' => '18', 'urutan' => '1'],
            ['nama_menu' => 'Riwayat Penilaian', 'url' => 'riwayat-penilaian', 'icon' => 'fas fa-history', 'parent_id' => '1', 'urutan' => '1'],
            ['nama_menu' => 'Lomba & Jadwal', 'url' => 'lomba-jadwal', 'icon' => 'fas fa-calendar-alt', 'parent_id' => '1', 'urutan' => '1'],
            ['nama_menu' => 'Lomba Saya', 'url' => 'lomba-saya', 'icon' => 'fas fa-dove', 'parent_id' => '1', 'urutan' => '1'],
            ['nama_menu' => 'Hasil Lomba saya', 'url' => 'hasil-lomba-saya', 'icon' => 'fas fa-trophy', 'parent_id' => '1', 'urutan' => '1'],
            ['nama_menu' => 'Scan QR', 'url' => 'scan-qr', 'icon' => 'fas fa-qrcode', 'parent_id' => '16', 'urutan' => '1'],
        ];

        foreach ($menus as $menu) {
            Menu::updateOrCreate($menu);
        }
    }
}
