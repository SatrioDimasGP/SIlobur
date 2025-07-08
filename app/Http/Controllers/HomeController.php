<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Pemesanan;
use App\Models\Role;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Psy\Util\Json;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user = Auth::user();
        $roleId = DB::table('model_has_roles')
            ->where('model_id', $user->id)
            ->first('role_id');

        $roleId = $roleId->role_id;

        $role = Role::find($roleId);

        if ($role->name == 'superadmin') {
            // return view('dashboard.superadmin');
            return $this->dashboardSuperadmin();
        }

        if ($role->name == 'admin') {
            // return view('dashboard.admin');
            return $this->dashboardAdmin();
        }

        if ($role->name == 'bendahara') {
            // return view('dashboard.bendahara');
            return $this->dashboardBendahara();
        }

        if ($role->name == 'korlap') {
            // return view('dashboard.korlap');
            return $this->dashboardKorlap();
        }

        if ($role->name == 'juri') {
            // return view('dashboard.juri');
            return $this->dashboardJuri();
        }

        if ($role->name == 'user') {
            $lombaTerbuka = \App\Models\Lomba::where('status_lomba_id', 1)
                ->whereNull('deleted_at')
                ->orderBy('tanggal')
                ->get();
            // dd($lombaTerbuka);

            return view('home', compact('lombaTerbuka'));
        }

        return view('home');
    }

    protected function dashboardSuperadmin()
    {
        $totalUser = DB::table('model_has_roles')
            ->where('role_id', 6) // user (peserta)
            ->count();

        $totalJuri = DB::table('model_has_roles')
            ->where('role_id', 5)
            ->count();

        $totalLombaAktif = DB::table('lombas')
            ->where('status_lomba_id', 1)
            ->whereNull('deleted_at')
            ->count();

        $pesertaHariIni = DB::table('pemesanans')
            ->whereDate('created_at', Carbon::today())
            ->whereNull('deleted_at')
            ->count();

        return view('dashboard.superadmin', compact(
            'totalUser',
            'totalJuri',
            'totalLombaAktif',
            'pesertaHariIni'
        ));
    }

    protected function dashboardAdmin()
    {
        // Hitung jumlah hasil penilaian yang masuk (penilaian yang sudah dinilai)
        $totalPenilaianMasuk = DB::table('penilaians')
            ->where('status_penilaian_id', 2) // misalnya 2 = "sudah dinilai"
            ->whereNull('deleted_at')
            ->count();

        return view('dashboard.admin', compact('totalPenilaianMasuk'));
    }

    protected function dashboardJuri()
    {
        // Kamu bisa isi sesuai kebutuhan dashboard juri
        return view('dashboard.juri');
    }

    protected function dashboardBendahara()
    {
        // Jumlah peserta terdaftar keseluruhan
        $totalPeserta = DB::table('pemesanans')
            ->whereNull('deleted_at')
            ->count();

        // Jumlah peserta yang daftar hari ini
        $pesertaHariIni = DB::table('pemesanans')
            ->whereDate('created_at', Carbon::today())
            ->whereNull('deleted_at')
            ->count();

        return view('dashboard.bendahara', compact(
            'totalPeserta',
            'pesertaHariIni'
        ));
    }

    protected function dashboardKorlap()
    {
        // Ambil semua lomba dengan status, jumlah blok dan jumlah juri yang ditugaskan
        $lombas = DB::table('lombas')
            ->select(
                'lombas.id',
                'lombas.nama',
                'status_lombas.nama as status',
                DB::raw('(SELECT COUNT(*) FROM bloks WHERE bloks.lomba_id = lombas.id AND bloks.deleted_at IS NULL) as total_blok'),
                DB::raw('(SELECT COUNT(*) FROM juri_tugas WHERE juri_tugas.lomba_id = lombas.id AND juri_tugas.deleted_at IS NULL) as total_juri')
            )
            ->leftJoin('status_lombas', 'status_lombas.id', '=', 'lombas.status_lomba_id')
            ->whereNull('lombas.deleted_at')
            ->get();

        return view('dashboard.korlap', compact('lombas'));
    }
}
