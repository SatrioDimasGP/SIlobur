<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DBBackupController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LombaController;
use App\Http\Controllers\PendaftaranController;
use App\Http\Controllers\JuriController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ManajemenLombaController;
use App\Http\Controllers\PenjadwalanJuriController;
use App\Http\Controllers\KonfigurasiBlokController;
use App\Http\Controllers\BurungController;
use App\Http\Controllers\HargaBurungController;
use App\Http\Controllers\GantanganController;
use App\Http\Controllers\KelolaPemesananController;
use App\Http\Controllers\ScanQrController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('landing-page');
});

// Route::permanentRedirect('/', '/login');

// Auth::routes();
Auth::routes(['verify' => true]);

Route::get('/email/verify', function () {
    return view('auth.verify'); // atau view lain sesuai struktur kamu
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill(); // Verifikasi email user
    return redirect('/home'); // Atau arahkan ke halaman sukses lainnya
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('resent', true);
})->middleware(['auth', 'throttle:6,1'])->name('verification.resend');

Route::get('auth/google', [LoginController::class, 'redirectToGoogle']);
Route::get('auth/google/callback', [LoginController::class, 'handleGoogleCallback']);

Route::get('/export-penilaian', [App\Http\Controllers\ExportPenilaianController::class, 'export'])->name('export.penilaian');
Route::get('/export-hasil-lomba', [App\Http\Controllers\ExportHasilLombaController::class, 'export'])->name('export.hasil_lomba');
Route::get('/data-pendaftaran/export', [\App\Http\Controllers\PendaftaranExportController::class, 'export'])->name('data-pendaftaran.export');


Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::resource('profil', ProfilController::class)->except('destroy');

Route::resource('manage-user', UserController::class);
Route::resource('manage-role', RoleController::class);
Route::resource('manage-menu', MenuController::class);
Route::resource('manage-permission', PermissionController::class)->only('store', 'destroy');

Route::middleware(['auth'])->group(function () {
    Route::middleware(['role:superadmin|bendahara'])->group(function () {
        // Route untuk daftar pemesanan
        Route::get('/data-pendaftaran', [KelolaPemesananController::class, 'index'])->name('data-pendaftaran.index');

        // Route untuk detail pemesanan
        Route::get('/data-pendaftaran/{pemesanan}', [KelolaPemesananController::class, 'show'])->name('data-pendaftaran.show');

        // Route untuk update status pemesanan
        Route::patch('/data-pendaftaran/{pemesanan}/update-status', [KelolaPemesananController::class, 'updateStatus'])->name('data-pendaftaran.update-status');

        Route::get('scan-qr', [ScanQrController::class, 'index'])->name('scan.index');
        Route::get('scan-qr/{id}', [ScanQrController::class, 'show']);
        Route::delete('/data-pendaftaran/{pemesanan}', [KelolaPemesananController::class, 'destroy'])->name('data-pendaftaran.destroy');
    });
});



Route::middleware(['auth', 'role:admin|superadmin|korlap|'])->group(function () {

    // Monitor Penilaian
    Route::get('/monitor-penilaian', [AdminController::class, 'monitorPenilaian'])->name('admin.monitor.index');
    Route::get('/monitor-penilaian/{id}', [AdminController::class, 'monitorShow'])->name('admin.monitor.show');

    // Hasil Lomba
    Route::get('/hasil-lomba', [AdminController::class, 'hasilLomba'])->name('admin.hasil.index');
    Route::get('/hasil-lomba/{nomor}', [AdminController::class, 'hasilShow'])->name('admin.hasil_lomba.show');

    // Pantau Lomba Realtime
    Route::get('/pantau-lomba', [AdminController::class, 'pantauLomba'])->name('admin.pantau_lomba');
    Route::get('/admin/pantau-lomba/data', [AdminController::class, 'pantauLombaData'])->name('admin.pantau_lomba.data');
});


Route::middleware(['auth', 'role:juri'])->group(function () {
    // Menu: Pilih Lomba
    Route::get('/penilaian-ajuan', [JuriController::class, 'pilihLomba'])->name('penilaian-ajuan.pilih-lomba');

    // Ajax data
    Route::get('/ajax/blok', [JuriController::class, 'getBlok'])->name('ajax.blok');
    Route::get('/ajax/kelas', [JuriController::class, 'getKelas'])->name('ajax.kelas');

    // Menu: Penilaian Tahap Ajuan
    Route::get('/penilaian-ajuan/{lombaId}', [JuriController::class, 'ajuanIndex'])->name('penilaian-ajuan.index');
    Route::get('/penilaian-ajuan/{lombaId}/{blokId}', [JuriController::class, 'ajuanShow'])->name('penilaian-ajuan.show');
    Route::post('/penilaian/{lombaId}/{blokId}', [JuriController::class, 'storePenilaian'])->name('penilaian.store');
    // Menambahkan rute untuk penilaian umum jika diperlukan
    Route::get('penilaian/{lombaId}', [JuriController::class, 'ajuanIndex'])->name('penilaian.index');
    // Route::get('/penilaian', [JuriController::class, 'index'])->name('penilaian.index');

    // Menu: Penilaian Tahap Koncer
    // Route::get('lomba/{lombaId}/koncer', [JuriController::class, 'koncerIndex']);

    // Daftar lomba yang bisa dinilai koncer oleh juri
    Route::get('/penilaian-koncer', [JuriController::class, 'koncerIndex'])->name('penilaian-koncer.index');

    // Form penilaian koncer untuk lomba tertentu
    Route::get('penilaian-koncer/{lomba}', [JuriController::class, 'koncerShow'])->name('penilaian-koncer.show');
    Route::get('/ajax/nomor-lolos-koncer', [JuriController::class, 'getNomorLolosKoncer'])->name('ajax.nomor-lolos-koncer');

    // Route::get('/penilaian-koncer/{lomba}', [JuriController::class, 'koncerShow'])->name('penilaian-koncer.show');

    // Proses submit penilaian koncer
    Route::post('/penilaian-koncer/{lomba}', [JuriController::class, 'storekoncer'])->name('penilaian-koncer.store');

    // Route::post('/penilaian-koncer/{lombaId}', [JuriController::class, 'storeKoncer'])->name('penilaian-koncer.store');

    // Menu: Riwayat Penilaian
    Route::get('/riwayat-penilaian', [JuriController::class, 'riwayatIndex'])->name('penilaian-riwayat.index');
    Route::get('/riwayat-penilaian/{id}', [JuriController::class, 'riwayatShow'])->name('penilaian-riwayat.show');
});

// Route::middleware(['auth', 'role:juri'])->group(function () {
//     // Menu: Pilih Lomba
//     Route::get('/penilaian-ajuan', [JuriController::class, 'pilihLomba'])->name('penilaian-ajuan.pilih-lomba');

//     // Mendapatkan gantangan berdasarkan kelas dan jenis burung yang dipilih
//     // Route::get('/ajax/gantangan', [JuriController::class, 'getGantangan'])->name('ajax.gantangan');
//     Route::get('/ajax/blok', [JuriController::class, 'getBlok']);
//     Route::get('/ajax/kelas', [JuriController::class, 'getKelas']);


//     // Route::get('/ajax/blok', [\App\Http\Controllers\JuriController::class, 'getBlok']);

//     // Menu: Penilaian Tahap Ajuan
//     Route::get('/penilaian-ajuan/{lombaId}', [JuriController::class, 'ajuanIndex'])->name('penilaian-ajuan.index');
//     Route::get('/penilaian-ajuan/{lombaId}/{blok}', [JuriController::class, 'ajuanShow'])->name('penilaian-ajuan.show');
//     Route::post('/penilaian/{lombaId}/{blokId}', [JuriController::class, 'storePenilaian'])->name('penilaian.store');
//     // Route::post('/penilaian', [JuriController::class, 'store'])->name('penilaian.store');
//     // Route::post('/penilaian/{blokGantanganId}', [JuriController::class, 'store'])->name('penilaian.store');
//     // Route::post('/penilaian/{lombaId}/{blokId}', [JuriController::class, 'store'])->name('penilaian.store');

//     // Menu: Penilaian Tahap Koncer
//     Route::get('/penilaian-koncer', [JuriController::class, 'koncerIndex'])->name('penilaian-koncer.index');
//     Route::get('/penilaian-koncer/{nomor}', [JuriController::class, 'koncerShow'])->name('penilaian-koncer.show');

//     // Menu: Riwayat Penilaian
//     Route::get('/riwayat-penilaian', [JuriController::class, 'riwayatIndex'])->name('penilaian-riwayat.index');
// });

Route::middleware(['auth', 'role:korlap|superadmin'])->group(function () {

    // Manajemen Lomba
    Route::get('/manajemen-lomba', [App\Http\Controllers\ManajemenLombaController::class, 'index'])->name('manajemen-lomba.index');
    Route::get('/manajemen-lomba/kelola/{id}', [ManajemenLombaController::class, 'kelola'])->name('manajemen-lomba.kelola');
    Route::get('/manajemen-lomba/create', [App\Http\Controllers\ManajemenLombaController::class, 'create'])->name('manajemen-lomba.create');
    Route::post('/manajemen-lomba', [App\Http\Controllers\ManajemenLombaController::class, 'store'])->name('manajemen-lomba.store');
    Route::get('/manajemen-lomba/{id}/edit', [App\Http\Controllers\ManajemenLombaController::class, 'edit'])->name('manajemen-lomba.edit');
    Route::put('/manajemen-lomba/{id}', [App\Http\Controllers\ManajemenLombaController::class, 'update'])->name('manajemen-lomba.update');
    Route::delete('/manajemen-lomba/{id}', [App\Http\Controllers\ManajemenLombaController::class, 'destroy'])->name('manajemen-lomba.destroy');

    Route::prefix('manajemen-lomba/kelola/{lomba_id}')->name('manajemen-lomba.kelola.')->group(function () {
        Route::get('/create-gabungan', [BurungController::class, 'createGabungan'])->name('burung.create');
        Route::post('/store-gabungan', [BurungController::class, 'storeGabungan'])->name('burung.store');
        Route::get('/{burung_id}/edit-gabungan', [BurungController::class, 'editGabungan'])->name('burung.edit');
        Route::put('/{burung_id}/update-gabungan', [BurungController::class, 'updateGabungan'])->name('burung.update');
        Route::delete('/{burung_id}/hapus-gabungan', [BurungController::class, 'destroyGabungan'])->name('burung.destroy');

        // Rute untuk edit jenis burung & kelas
        Route::get('burung/jenis-burung/{id}/edit', [BurungController::class, 'editJenisBurung'])->name('burung.jenis-burung.edit');
        Route::get('kelas/{id}/edit', [BurungController::class, 'editKelas'])->name('kelas.edit');
        Route::post('burung/jenis-burung', [BurungController::class, 'storeJenisBurung'])->name('burung.jenis-burung.store');
        Route::put('burung/jenis-burung/{id}', [BurungController::class, 'updateJenisBurung'])->name('burung.jenis-burung.update');
        Route::put('kelas/{id}', [BurungController::class, 'updateKelas'])->name('kelas.update');
        Route::post('/jenis-burung', [BurungController::class, 'storeJenisBurung'])->name('jenis-burung.store');
        Route::delete('/jenis-burung/{id}', [BurungController::class, 'destroyJenisBurung'])->name('jenis-burung.destroy');
        Route::post('/kelas', [BurungController::class, 'storeKelas'])->name('kelas.store');
        Route::delete('kelas/{id}', [BurungController::class, 'destroyKelas'])->name('kelas.destroy');
    });
    Route::prefix('manajemen-lomba/kelola/{lomba_id}/gantangan')->name('manajemen-lomba.kelola.gantangan.')->group(function () {
        Route::get('/', [GantanganController::class, 'index'])->name('index');
        Route::post('/store', [GantanganController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [GantanganController::class, 'edit'])->name('edit');
        Route::put('/{id}/update', [GantanganController::class, 'update'])->name('update');
        Route::delete('/{id}/destroy', [GantanganController::class, 'destroy'])->name('destroy');
    });


    // Rute untuk Kelola Jenis Burung dan Kelas
    // Route::prefix('manajemen-lomba/burung')->name('manajemen-lomba.')->group(function () {
    //     // Route::get('/', [BurungController::class, 'index'])->name('burung.index');
    //     Route::get('/create-gabungan', [BurungController::class, 'createGabungan'])->name('burung.create');
    //     Route::post('/store-gabungan', [BurungController::class, 'storeGabungan'])->name('burung.store');
    //     Route::get('/{id}/edit-gabungan', [BurungController::class, 'editGabungan'])->name('burung.edit');
    //     Route::put('/{id}/update-gabungan', [BurungController::class, 'updateGabungan'])->name('burung.update');
    //     Route::delete('/{id}/hapus-gabungan', [BurungController::class, 'destroyGabungan'])->name('burung.destroy');
    //     Route::get('burung/jenis-burung/{id}/edit', [BurungController::class, 'editJenisBurung'])->name('burung.jenis-burung.edit');
    //     Route::get('kelas/{id}/edit', [BurungController::class, 'editKelas'])->name('kelas.edit');
    //     Route::post('burung/jenis-burung', [BurungController::class, 'storeJenisBurung'])->name('burung.jenis-burung.store');
    //     Route::put('burung/jenis-burung/{id}', [BurungController::class, 'updateJenisBurung'])->name('burung.jenis-burung.update');
    //     Route::put('kelas/{id}', [BurungController::class, 'updateKelas'])->name('kelas.update');
    //     Route::post('/jenis-burung', [BurungController::class, 'storeJenisBurung'])->name('jenis-burung.store');
    //     Route::delete('/jenis-burung/{id}', [BurungController::class, 'destroyJenisBurung'])->name('jenis-burung.destroy');
    //     Route::post('/kelas', [BurungController::class, 'storeKelas'])->name('kelas.store');
    //     Route::delete('kelas/{id}', [BurungController::class, 'destroyKelas'])->name('kelas.destroy');
    // });

    // // Manajemen Harga Burung
    // Route::prefix('manajemen-lomba/harga')->name('manajemen-lomba.')->group(function () {
    //     Route::get('/', [HargaBurungController::class, 'index'])->name('harga.index');
    //     Route::post('/store', [HargaBurungController::class, 'store'])->name('harga.store');
    //     Route::get('/{id}/edit', [HargaBurungController::class, 'edit'])->name('harga.edit');
    //     Route::put('/{id}/update', [HargaBurungController::class, 'update'])->name('harga.update');
    //     Route::delete('/{id}/destroy', [HargaBurungController::class, 'destroy'])->name('harga.destroy');
    // });

});

// Penjadwalan Juri
Route::get('/penugasan-juri', [PenjadwalanJuriController::class, 'index'])->name('penjadwalan-juri.index');
Route::post('/penugasan-juri', [PenjadwalanJuriController::class, 'store'])->name('penjadwalan-juri.store');
Route::get('/penjadwalan-juri/{id}/edit', [PenjadwalanJuriController::class, 'edit'])->name('penjadwalan-juri.edit');
Route::put('/penjadwalan-juri/{id}', [PenjadwalanJuriController::class, 'update'])->name('penjadwalan-juri.update');
// Route untuk mendapatkan data harga burung berdasarkan lomba
Route::get('/penjadwalan-juri/get-jenis-burung-kelas/{lombaId}', [PenjadwalanJuriController::class, 'getJenisBurungByLomba']);
// Route::get('/penjadwalan-juri/get-harga-burung/{lombaId}', [PenjadwalanJuriController::class, 'getHargaBurungByLomba']);
// Route::get('/get-blok-by-lomba/{lombaId}', [PenjadwalanJuriController::class, 'getBlokByLomba']);
// Route::get('/get-blok-by-lomba/{lomba_id}', [PenjadwalanJuriController::class, 'getBlokByLomba'])->name('get-blok-by-lomba');
Route::delete('/penjadwalan-juri/{id}', [PenjadwalanJuriController::class, 'destroy'])->name('penjadwalan-juri.destroy');

// Rute untuk konfigurasi blok
Route::get('/konfigurasi-blok', [KonfigurasiBlokController::class, 'index'])->name('konfigurasi-blok.index');
Route::post('/konfigurasi-blok', [App\Http\Controllers\KonfigurasiBlokController::class, 'store'])->name('konfigurasi-blok.store');
Route::get('konfigurasi-blok/{id}/edit', [KonfigurasiBlokController::class, 'edit'])->name('konfigurasi-blok.edit');
Route::put('/konfigurasi-blok/{id}', [App\Http\Controllers\KonfigurasiBlokController::class, 'update'])->name('konfigurasi-blok.update');
Route::delete('/konfigurasi-blok/{id}', [App\Http\Controllers\KonfigurasiBlokController::class, 'destroy'])->name('konfigurasi-blok.destroy');

// Rute untuk konfigurasi blok-gantangan
Route::post('konfigurasi-blok-gantangan', [KonfigurasiBlokController::class, 'storeGantangan'])->name('konfigurasi-blok.gantangan.store');
Route::get('/konfigurasi-blok-gantangan/{id}/edit', [KonfigurasiBlokController::class, 'editGantangan'])->name('konfigurasi-blok.gantangan.edit');
Route::put('/konfigurasi-blok-gantangan/{id}', [KonfigurasiBlokController::class, 'updateGantangan'])->name('konfigurasi-blok.gantangan.update');
Route::delete('/konfigurasi-blok-gantangan/{id}', [KonfigurasiBlokController::class, 'destroyGantangan'])->name('konfigurasi-blok.gantangan.destroy');

// Route Group untuk role 'korlap' (bisa ditambahkan middleware jika perlu)
// Route::middleware(['auth'])->group(function () {

//     // Manajemen Lomba
//     Route::get('/manajemen-lomba', [App\Http\Controllers\ManajemenLombaController::class, 'index'])->name('manajemen-lomba.index');
//     Route::get('/manajemen-lomba/create', [App\Http\Controllers\ManajemenLombaController::class, 'create'])->name('manajemen-lomba.create');
//     Route::post('/manajemen-lomba', [App\Http\Controllers\ManajemenLombaController::class, 'store'])->name('manajemen-lomba.store');
//     Route::get('/manajemen-lomba/{id}/edit', [App\Http\Controllers\ManajemenLombaController::class, 'edit'])->name('manajemen-lomba.edit');
//     Route::put('/manajemen-lomba/{id}', [App\Http\Controllers\ManajemenLombaController::class, 'update'])->name('manajemen-lomba.update');
//     Route::delete('/manajemen-lomba/{id}', [App\Http\Controllers\ManajemenLombaController::class, 'destroy'])->name('manajemen-lomba.destroy');

//     // Rute untuk Kelola Jenis Burung dan Kelas
//     Route::prefix('manajemen-lomba/burung')->name('manajemen-lomba.')->middleware(['auth'])->group(function () {
//         Route::get('/', [BurungController::class, 'index'])->name('burung.index');

//         // Tambahkan ini:
//         Route::get('/create-gabungan', [BurungController::class, 'createGabungan'])->name('burung.create');
//         Route::post('/store-gabungan', [BurungController::class, 'storeGabungan'])->name('burung.store');
//         Route::get('/{id}/edit-gabungan', [BurungController::class, 'editGabungan'])->name('burung.edit');
//         Route::put('/{id}/update-gabungan', [BurungController::class, 'updateGabungan'])->name('burung.update');
//         Route::delete('/{id}/hapus-gabungan', [BurungController::class, 'destroyGabungan'])->name('burung.destroy');

//         // Rute untuk mengedit jenis burung dan kelas
//         Route::get('burung/jenis-burung/{id}/edit', [BurungController::class, 'editJenisBurung'])->name('burung.jenis-burung.edit');

//         // Menggunakan prefix yang benar untuk kelas
//         Route::get('kelas/{id}/edit', [BurungController::class, 'editKelas'])->name('kelas.edit');

//         // Rute lainnya
//         Route::post('burung/jenis-burung', [BurungController::class, 'storeJenisBurung'])->name('burung.jenis-burung.store');
//         Route::put('burung/jenis-burung/{id}', [BurungController::class, 'updateJenisBurung'])->name('burung.jenis-burung.update');

//         // Rute untuk mengupdate jenis burung dan kelas
//         Route::put('kelas/{id}', [BurungController::class, 'updateKelas'])->name('kelas.update');

//         Route::post('/jenis-burung', [BurungController::class, 'storeJenisBurung'])->name('jenis-burung.store');
//         Route::delete('/jenis-burung/{id}', [BurungController::class, 'destroyJenisBurung'])->name('jenis-burung.destroy');

//         Route::post('/kelas', [BurungController::class, 'storeKelas'])->name('kelas.store');
//         Route::delete('kelas/{id}', [BurungController::class, 'destroyKelas'])->name('kelas.destroy');
//     });

//     Route::prefix('manajemen-lomba/harga')->name('manajemen-lomba.')->middleware(['auth'])->group(function () {
//         // Tampilkan daftar harga dan form tambah (digabung di satu halaman)
//         Route::get('/', [HargaBurungController::class, 'index'])->name('harga.index');

//         // Simpan harga baru
//         Route::post('/store', [HargaBurungController::class, 'store'])->name('harga.store');

//         // Tampilkan form edit
//         Route::get('/{id}/edit', [HargaBurungController::class, 'edit'])->name('harga.edit');

//         // Update data harga
//         Route::put('/{id}/update', [HargaBurungController::class, 'update'])->name('harga.update');

//         // Hapus data harga
//         Route::delete('/{id}/destroy', [HargaBurungController::class, 'destroy'])->name('harga.destroy');
//     });

//     Route::prefix('manajemen-lomba/gantangan')->name('manajemen-lomba.')->middleware(['auth'])->group(function () {
//         // Tampilkan daftar gantangan dan form tambah (digabung di satu halaman)
//         Route::get('/', [GantanganController::class, 'index'])->name('gantangan.index');

//         // Simpan gantangan baru
//         Route::post('/store', [GantanganController::class, 'store'])->name('gantangan.store');

//         // Tampilkan form edit
//         Route::get('/{id}/edit', [GantanganController::class, 'edit'])->name('gantangan.edit');

//         // Update data gantangan
//         Route::put('/{id}/update', [GantanganController::class, 'update'])->name('gantangan.update');

//         // Hapus data gantangan
//         Route::delete('/{id}/destroy', [GantanganController::class, 'destroy'])->name('gantangan.destroy');
//     });

//     // Penjadwalan Juri
//     // Route::get('/penjadwalan-juri', [App\Http\Controllers\PenjadwalanJuriController::class, 'index'])->name('penjadwalan-juri.index');
//     // Route::post('/penjadwalan-juri', [App\Http\Controllers\PenjadwalanJuriController::class, 'store'])->name('penjadwalan-juri.store');

//     // Rute untuk konfigurasi blok
//     Route::get('/konfigurasi-blok', [KonfigurasiBlokController::class, 'index'])->name('konfigurasi-blok.index');
//     Route::post('/konfigurasi-blok', [App\Http\Controllers\KonfigurasiBlokController::class, 'store'])->name('konfigurasi-blok.store');
//     Route::get('konfigurasi-blok/{id}/edit', [KonfigurasiBlokController::class, 'edit'])->name('konfigurasi-blok.edit');
//     Route::put('/konfigurasi-blok/{id}', [App\Http\Controllers\KonfigurasiBlokController::class, 'update'])->name('konfigurasi-blok.update');
//     Route::delete('/konfigurasi-blok/{id}', [App\Http\Controllers\KonfigurasiBlokController::class, 'destroy'])->name('konfigurasi-blok.destroy');

//     // Rute untuk konfigurasi blok-gantangan
//     Route::post('konfigurasi-blok-gantangan', [KonfigurasiBlokController::class, 'storeGantangan'])->name('konfigurasi-blok.gantangan.store');
//     Route::get('/konfigurasi-blok-gantangan/{id}/edit', [KonfigurasiBlokController::class, 'editGantangan'])->name('konfigurasi-blok.gantangan.edit');
//     Route::put('/konfigurasi-blok-gantangan/{id}', [KonfigurasiBlokController::class, 'updateGantangan'])->name('konfigurasi-blok.gantangan.update');
//     Route::delete('/konfigurasi-blok-gantangan/{id}', [KonfigurasiBlokController::class, 'destroyGantangan'])->name('konfigurasi-blok.gantangan.destroy');
// });


Route::middleware(['auth', 'role:user'])->group(function () {
    // Menu: Lomba & Jadwal
    Route::get('/lomba-jadwal', [PendaftaranController::class, 'index'])->name('lomba.index');

    // Menampilkan daftar lomba (halaman awal pendaftaran)
    Route::get('/daftar', [PendaftaranController::class, 'index'])->name('daftar.index');

    // Form pendaftaran untuk lomba tertentu
    Route::get('/daftar/{lomba}/create', [PendaftaranController::class, 'create'])->name('daftar.create');
    Route::get('/ajax/gantangan', [PendaftaranController::class, 'getGantangan'])->name('ajax.gantangan');

    // Simpan pendaftaran
    Route::post('/daftar/{lomba}', [PendaftaranController::class, 'store'])->name('daftar.store');
    Route::get('/lomba/{lomba}/daftar', [PendaftaranController::class, 'create'])->name('lomba.daftar');
    Route::post('lomba/{lomba}/daftar', [PendaftaranController::class, 'store'])->name('daftar.store');
    Route::get('/pemesanans/show/{order_group_id}', [PendaftaranController::class, 'show'])
        ->name('pemesanans.show');
    Route::get('/cek-status-qr/{id}', [PaymentController::class, 'cekStatusQr']);
    Route::get('pemesanans/{transaksi}/bukti-pembayaran', [PaymentController::class, 'generateBuktiPembayaranPdf'])->name('pemesanans.bukti-pembayaran');

    // Route::get('/download/bukti-pembayaran/{id}', [PaymentController::class, 'generateBuktiPembayaranPdf'])->name('download.bukti');



    // Menu: Lomba Saya
    Route::get('/lomba-saya', [LombaController::class, 'index'])->name('lomba.saya');

    // Detail Pemesanan
    Route::post('bayar', [PaymentController::class, 'proccess'])->name('pembayaran.store');

    Route::get('bayar/{id}', [PaymentController::class, 'pembayaran'])->name('pembayaran.index');

    Route::get('bayar-sukses', [PaymentController::class, 'pembayaranSukses'])->name('pembayaran.sukses');

    // Rute untuk menampilkan hasil lomba yang dimiliki oleh user
    Route::get('/hasil-lomba-saya', [LombaController::class, 'hasilLombaUser'])->name('hasil_lomba.index');

    // Rute untuk menampilkan detail hasil lomba yang dimiliki oleh user
    Route::get('/hasil-lomba-saya/{blokGantanganId}/burung/{burungId}/tahap/{tahapId}', [LombaController::class, 'hasilLombaShow'])->name('hasil_lomba.show');

    // Route::get('/hasil-lomba-saya/{id}/tahap/{tahapId}', [LombaController::class, 'hasilLombaShow'])->name('hasil_lomba.show');
    // Route::get('/hasil-lomba-saya/{id}', [LombaController::class, 'hasilLombaShow'])->name('hasil_lomba.show');
});

Route::get('dbbackup', [DBBackupController::class, 'DBDataBackup']);
