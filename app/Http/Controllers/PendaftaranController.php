<?php

namespace App\Http\Controllers;

use App\Models\Lomba;
use App\Models\JenisBurung;
use App\Models\Kelas;
use App\Models\Gantangan;
use App\Models\Pemesanan;
use App\Models\Burung;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PendaftaranController extends Controller
{
    public function index()
    {
        // Tampilkan lomba yang aktif
        $lombas = Lomba::where('status_lomba_id', 1)->get();
        return view('customer.daftar.index', compact('lombas'));
    }

    // Form pendaftaran lomba
    public function create(Lomba $lomba)
    {
        // Ambil kelas yang terkait lomba (lomba_id di tabel kelas)
        $kelas = Kelas::where('lomba_id', $lomba->id)->get();

        // Ambil semua jenis burung yang ada di burungs lomba ini,
        // yaitu jenis burung yang dimiliki oleh burungs pada kelas lomba tersebut
        $jenisBurungs = JenisBurung::whereIn('id', function ($query) use ($kelas) {
            $query->select('jenis_burung_id')
                ->from('burungs')
                ->whereIn('kelas_id', $kelas->pluck('id'))
                ->whereNull('deleted_at');
        })->get();

        return view('customer.daftar.create', compact('lomba', 'jenisBurungs', 'kelas'));
    }

    // AJAX: ambil kelas dan gantangan berdasarkan jenis burung, kelas, dan lomba
    public function getGantangan(Request $request)
    {
        $kelas_id = $request->kelas_id;
        $jenis_burung_id = $request->jenis_burung_id;
        $lomba_id = $request->lomba_id;

        // Ambil kelas yang terkait lomba dan juga memiliki burung jenis tersebut
        $kelas = Kelas::where('lomba_id', $lomba_id)
            ->whereHas('burungs', function ($q) use ($jenis_burung_id) {
                $q->where('jenis_burung_id', $jenis_burung_id)->whereNull('deleted_at');
            })
            ->get();

        // Ambil semua nomor gantangan yang terkait lomba ini lewat blok → blok_gantangan → gantangan
        $gantangans = Gantangan::whereNull('deleted_at')
            ->whereIn('id', function ($query) use ($lomba_id, $jenis_burung_id, $kelas_id) {
                $query->select('gantangan_id')
                    ->from('blok_gantangans')
                    ->whereNull('deleted_at')
                    ->whereIn('blok_id', function ($subQuery) use ($lomba_id, $jenis_burung_id, $kelas_id) {
                        $subQuery->select('bloks.id')
                            ->from('bloks')
                            ->join('burungs', 'bloks.burung_id', '=', 'burungs.id')
                            ->where('bloks.lomba_id', $lomba_id)
                            ->where('burungs.jenis_burung_id', $jenis_burung_id)
                            ->where('burungs.kelas_id', $kelas_id)
                            ->whereNull('bloks.deleted_at')
                            ->whereNull('burungs.deleted_at');
                    });
            })
            ->get()
            ->map(function ($g) use ($lomba_id, $kelas_id, $jenis_burung_id) {
                // Tandai apakah nomor gantangan sudah dipesan untuk lomba ini
                $g->terisi = Pemesanan::where('gantangan_id', $g->id)
                    ->where('lomba_id', $lomba_id)
                    ->whereHas('burung', function ($query) use ($kelas_id, $jenis_burung_id) {
                        $query->where('kelas_id', $kelas_id)
                            ->where('jenis_burung_id', $jenis_burung_id);
                    })
                    ->exists();

                return $g;
            });

        return response()->json([
            'gantangans' => $gantangans,
            'kelas' => $kelas,
        ]);
    }


    // Simpan pendaftaran
    public function store(Request $request, Lomba $lomba)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.gantangan_id' => 'required|exists:gantangans,id',
            'items.*.jenis_id' => 'required|exists:jenis_burungs,id',
            'items.*.kelas_id' => 'required|exists:kelas,id',
        ]);

        DB::beginTransaction();
        try {
            foreach ($request->items as $item) {
                $burung = Burung::where('jenis_burung_id', $item['jenis_id'])
                    ->where('kelas_id', $item['kelas_id'])
                    ->first();

                if (!$burung) {
                    DB::rollBack();
                    return back()->with('error', 'Burung tidak ditemukan untuk jenis dan kelas yang dipilih.');
                }

                $duplikat = Pemesanan::where('gantangan_id', $item['gantangan_id'])
                    ->where('burung_id', $burung->id)
                    ->exists();

                if ($duplikat) {
                    DB::rollBack();
                    return back()->with('error', 'Gantangan ' . $item['gantangan_id'] . ' sudah terdaftar untuk burung ini.');
                }

                Pemesanan::create([
                    'user_id' => Auth::id(),
                    'lomba_id' => $lomba->id,
                    'gantangan_id' => $item['gantangan_id'],
                    'burung_id' => $burung->id,
                    'nama' => $request->nama,
                    'status_pemesanan_id' => 1,
                    'created_by' => Auth::id(),
                    'updated_by' => Auth::id(),
                ]);
            }
            DB::commit();
            return redirect()->route('lomba.saya')->with('success', 'Pendaftaran berhasil.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function show($created_at)
    {
        $userId = Auth::id();
        $createdAt = Carbon::createFromFormat('Y-m-d H:i:s', $created_at);

        $pemesanan = Pemesanan::with([
            'lomba',
            'gantangan',
            'burung.jenisBurung',
            'burung.kelas',
            'status',
            'transaksi.qrcode'
        ])
            ->where('user_id', $userId)
            ->where('created_at', $createdAt)
            ->get();
        // dd($pemesanan);
        // dd([
        //     'transaksi' => $pemesanan->map(fn($p) => $p->transaksi)->all(),
        //     'qrcode' => $pemesanan->map(fn($p) => optional($p->transaksi)->qrcode)->all(),
        // ]);
        // dd($pemesanan);


        if ($pemesanan->isEmpty()) {
            abort(404);
        }

        $allLunas = $pemesanan->every(fn($p) => $p->status_pemesanan_id == 2);

        $transaksiDenganQr = $pemesanan
            ->map(fn($p) => $p->transaksi)
            ->filter(
                fn($t) =>
                $t &&
                    $t->status_transaksi_id == 2 && // transaksi berhasil
                    $t->qrcode &&
                    $t->qrcode->status_qr_id == 1   // qrcode aktif
            )
            ->sortByDesc('created_at')
            ->first();
        // dd($pemesanan->pluck('status'));
        // dd($pemesanan->pluck('transaksi'));
        // dd($pemesanan->pluck('transaksi.qrcode'));
        // dd([
        //     'allLunas' => $allLunas,
        //     'transaksiDenganQr' => $transaksiDenganQr,
        //     'transaksiDenganQr_id' => optional($transaksiDenganQr)->id,
        //     'qrcode' => optional($transaksiDenganQr)->qrcode,
        //     'status_qr_id' => optional(optional($transaksiDenganQr)->qrcode)->status_qr_id,
        //     'pemesanan_status_ids' => $pemesanan->pluck('status_pemesanan_id'),
        //     'pemesanan_status_nama' => $pemesanan->pluck('status.nama'),
        // ]);

        return view('customer.lomba.show', compact('pemesanan', 'allLunas', 'transaksiDenganQr'));
    }
}
