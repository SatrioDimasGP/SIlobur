<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buat Role Admin
        $role = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);

        // // Menu Induk: Menu Admin
        // $menuManajemen = Menu::firstOrCreate([
        //     'nama_menu' => 'Menu Admin',
        // ], [
        //     'url' => '#',
        //     'icon' => null,
        //     'parent_id' => null,
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

        // // Assign Role ke Menu (jika ada relasi Menu <-> Role)
        // $roleId = $role->id;
        // foreach (
        //     [
        //         $monitoring,
        //         $menuMonitorPenilaian,
        //         $menuHasilLomba
        //     ] as $menu
        // ) {
        //     $menu->roles()->syncWithoutDetaching([$roleId]);
        // }

        // // Tambahkan permission jika nanti diperlukan
        // $permissions = [
        //     // 'read_penilaian', 'view_hasil_lomba' (contoh)
        // ];

        // foreach ($permissions as $perm) {
        //     $permission = Permission::firstOrCreate(['name' => $perm]);
        //     $role->givePermissionTo($permission);
        // }
    }
}
