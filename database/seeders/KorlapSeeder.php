<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Menu;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class KorlapSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buat Role Korlap
        $role = Role::firstOrCreate(['name' => 'korlap', 'guard_name' => 'web']);

        // // Menu Induk: Menu Manajemen (jika belum ada)
        // $menuManajemen = Menu::firstOrCreate([
        //     'nama_menu' => 'Menu Korlap',
        // ], [
        //     'url' => '#',
        //     'icon' => null,
        //     'parent_id' => null,
        // ]);


        // // Menu: Manajemen Teknis Lomba
        // $manajemenTeknis = Menu::firstOrCreate([
        //     'nama_menu' => 'Manajemen Teknis Lomba',
        // ], [
        //     'url' => '#',
        //     'icon' => 'fas fa-cogs',
        //     'parent_id' => $menuManajemen->id,
        // ]);

        // $menuLomba = Menu::firstOrCreate([
        //     'nama_menu' => 'Manajemen Lomba',
        // ], [
        //     'url' => 'manajemen-lomba',
        //     'icon' => 'fas fa-dove',
        //     'parent_id' => $manajemenTeknis->id,
        // ]);

        // $menuPenugasan = Menu::firstOrCreate([
        //     'nama_menu' => 'Penjadwalan / Penugasan Juri',
        // ], [
        //     'url' => 'penugasan-juri',
        //     'icon' => null,
        //     'parent_id' => $manajemenTeknis->id,
        // ]);

        // $menuKonfigurasi = Menu::firstOrCreate([
        //     'nama_menu' => 'Konfigurasi Gantangan & Blok',
        // ], [
        //     'url' => 'konfigurasi-blok',
        //     'icon' => null,
        //     'parent_id' => $manajemenTeknis->id,
        // ]);

        // // Menu: Monitoring & Hasil
        // $monitoring = Menu::firstOrCreate([
        //     'nama_menu' => 'Monitoring & Hasil',
        // ], [
        //     'url' => '#',
        //     'icon' => 'fas fa-chart-line',
        //     'parent_id' => $menuManajemen->id,
        // ]);

        // $menuMonitorPenilaian = Menu::firstOrCreate([
        //     'nama_menu' => 'Penilaian (monitor semua)',
        // ], [
        //     'url' => 'monitor-penilaian',
        //     'icon' => 'fas fa-eye',
        //     'parent_id' => $monitoring->id,
        // ]);

        // $menuHasilLomba = Menu::firstOrCreate([
        //     'nama_menu' => 'Data Penilaian / Hasil Lomba',
        // ], [
        //     'url' => 'hasil-lomba',
        //     'icon' => 'fas fa-award',
        //     'parent_id' => $monitoring->id,
        // ]);

        // // Assign seluruh permission yang mungkin diperlukan ke role korlap (optional)
        // // Jika belum ada permission spesifik, cukup buat placeholder jika ingin ditambahkan nanti
        // $permissions = [
        //     // Contoh (tambahkan jika diperlukan nanti):
        //     // 'read_lomba', 'create_lomba', 'assign_juri', 'read_penilaian', dll.
        // ];

        // foreach ($permissions as $perm) {
        //     $permission = Permission::firstOrCreate(['name' => $perm]);
        //     $role->givePermissionTo($permission);
        // }

        // // Jika sistem memiliki relasi Menu <-> Role (pivot menu_role), tambahkan role_id ke menu
        // $roleId = $role->id;
        // foreach (
        //     [
        //         $manajemenTeknis,
        //         $menuLomba,
        //         $menuPenugasan,
        //         $menuKonfigurasi,
        //         $monitoring,
        //         $menuMonitorPenilaian,
        //         $menuHasilLomba
        //     ] as $menu
        // ) {
        //     $menu->roles()->syncWithoutDetaching([$roleId]);
        // }
    }
}
