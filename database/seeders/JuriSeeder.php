<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Menu;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class JuriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buat Role Juri
        $role = Role::firstOrCreate(['name' => 'juri', 'guard_name' => 'web']);

        // // Menu Induk: Menu Juri
        // $menuManajemen = Menu::firstOrCreate([
        //     'nama_menu' => 'Menu Juri',
        // ], [
        //     'url' => '#',
        //     'icon' => null,
        //     'parent_id' => null,
        // ]);

        // // Menu: Penilaian Saya
        // $penilaianSaya = Menu::firstOrCreate([
        //     'nama_menu' => 'Penilaian Saya',
        // ], [
        //     'url' => '',
        //     'icon' => 'fas fa-clipboard-check',
        //     'parent_id' => $menuManajemen->id,
        // ]);

        // // Submenu: Tahap Ajuan
        // $tahapAjuan = Menu::firstOrCreate([
        //     'nama_menu' => 'Tahap Ajuan',
        // ], [
        //     'url' => 'penilaian-ajuan',
        //     'icon' => null,
        //     'parent_id' => $penilaianSaya->id,
        // ]);

        // // Submenu: Tahap Koncer
        // $tahapKoncer = Menu::firstOrCreate([
        //     'nama_menu' => 'Tahap Koncer',
        // ], [
        //     'url' => 'penilaian-koncer',
        //     'icon' => null,
        //     'parent_id' => $penilaianSaya->id,
        // ]);

        // // Menu: Riwayat Penilaian
        // $riwayat = Menu::firstOrCreate([
        //     'nama_menu' => 'Riwayat Penilaian',
        // ], [
        //     'url' => 'riwayat-penilaian',
        //     'icon' => 'fas fa-history',
        //     'parent_id' => $menuManajemen->id,
        // ]);

        // // Assign role ke semua menu
        // foreach (
        //     [
        //         $menuManajemen,
        //         $penilaianSaya,
        //         $tahapAjuan,
        //         $tahapKoncer,
        //         $riwayat
        //     ] as $menu
        // ) {
        //     $menu->roles()->syncWithoutDetaching([$role->id]);
        // }

        // // Tambahkan permission jika diperlukan nanti
        // $permissions = [
        //     // Contoh: 'akses_tahap_ajuan', 'akses_tahap_koncer'
        // ];

        // foreach ($permissions as $perm) {
        //     $permission = Permission::firstOrCreate(['name' => $perm]);
        //     $role->givePermissionTo($permission);
        // }
    }
}
