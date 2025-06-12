<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Menu;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buat Role User
        $role = Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);

        // // Menu Induk: Useren
        // $menuManajemen = Menu::firstOrCreate([
        //     'nama_menu' => 'Menu Peserta',
        // ], [
        //     'url' => '#',
        //     'icon' => null,
        //     'parent_id' => null,
        // ]);

        // // Submenu: Lomba & Jadwal
        // $menuLombaJadwal = Menu::firstOrCreate([
        //     'nama_menu' => 'Lomba & Jadwal',
        // ], [
        //     'url' => 'lomba-jadwal',
        //     'icon' => 'fas fa-calendar-alt',
        //     'parent_id' => $menuManajemen->id,
        // ]);

        // // Submenu: Lomba Saya
        // $menuLombaSaya = Menu::firstOrCreate([
        //     'nama_menu' => 'Lomba Saya',
        // ], [
        //     'url' => 'lomba-saya',
        //     'icon' => 'fas fa-dove',
        //     'parent_id' => $menuManajemen->id,
        // ]);

        // // Submenu: Hasil Lomba Saya
        // $menuHasilLomba = Menu::firstOrCreate([
        //     'nama_menu' => 'Hasil Lomba saya',
        // ], [
        //     'url' => 'hasil-lomba-saya',
        //     'icon' => 'fas fa-trophy',
        //     'parent_id' => $menuManajemen->id,
        // ]);

        // // Assign role user ke menu
        // foreach ([$menuManajemen, $menuLombaJadwal, $menuLombaSaya, $menuHasilLomba] as $menu) {
        //     $menu->roles()->syncWithoutDetaching([$role->id]);
        // }

        // // Tambahkan permission jika diperlukan
        // $permissions = [
        //     // Contoh: 'view_lomba', 'view_jadwal'
        // ];

        // foreach ($permissions as $perm) {
        //     $permission = Permission::firstOrCreate(['name' => $perm]);
        //     $role->givePermissionTo($permission);
        // }
    }
}
