<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Menu;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class BendaharaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buat Role Bendahara
        $role = Role::firstOrCreate(['name' => 'bendahara', 'guard_name' => 'web']);

        // // Menu Induk: Menu Bendahara
        // $menuManajemen = Menu::firstOrCreate([
        //     'nama_menu' => 'Menu Bendahara',
        // ], [
        //     'url' => '#',
        //     'icon' => null,
        //     'parent_id' => null,
        // ]);

        // // Menu: Pendaftaran
        // $pendaftaran = Menu::firstOrCreate([
        //     'nama_menu' => 'Pendaftaran',
        // ], [
        //     'url' => '',
        //     'icon' => 'fas fa-clipboard-list',
        //     'parent_id' => $menuManajemen->id,
        // ]);

        // $dataPendaftaran = Menu::firstOrCreate([
        //     'nama_menu' => 'Data Pendaftaran',
        // ], [
        //     'url' => 'data-pendaftaran',
        //     'icon' => 'fas fa-clipboard-list',
        //     'parent_id' => $pendaftaran->id,
        // ]);

        // $scanQR = Menu::firstOrCreate([
        //     'nama_menu' => 'Scan QR',
        // ], [
        //     'url' => 'scan-qr',
        //     'icon' => 'fas fa-qrcode',
        //     'parent_id' => $pendaftaran->id,
        // ]);

        // // Assign Role ke Menu (jika model Menu punya relasi roles())
        // foreach (
        //     [
        //         $menuManajemen,
        //         $pendaftaran,
        //         $dataPendaftaran,
        //         $scanQR
        //     ] as $menu
        // ) {
        //     $menu->roles()->syncWithoutDetaching([$role->id]);
        // }

        // // Tambahkan permission jika dibutuhkan di masa depan
        // $permissions = [
        //     // Contoh: 'read_pendaftaran'
        // ];

        // foreach ($permissions as $perm) {
        //     $permission = Permission::firstOrCreate(['name' => $perm]);
        //     $role->givePermissionTo($permission);
        // }
    }
}
