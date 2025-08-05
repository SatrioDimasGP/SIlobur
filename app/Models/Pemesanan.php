<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes;

class Pemesanan extends Model
{
    // use SoftDeletes;

    protected $table = 'pemesanans';

    protected $fillable = [
        'user_id',
        'nama',
        'gantangan_id',
        'burung_id', // Tambahkan kolom baru ini
        'status_pemesanan_id',
        'lomba_id',
        'order_group_id', // ⬅️ Tambahkan ini
        'created_by',
        'updated_by',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function gantangan()
    {
        return $this->belongsTo(Gantangan::class)->withTrashed();
    }

    public function lomba()
    {
        return $this->belongsTo(Lomba::class)->withTrashed();
    }

    public function status()
    {
        return $this->belongsTo(StatusPemesanan::class, 'status_pemesanan_id')->withTrashed();
    }

    // Di model Pemesanan:
    public function transaksis()
    {
        return $this->belongsToMany(Transaksi::class, 'pemesanan_transaksis')->withTrashed();
    }


    public function burung()
    {
        return $this->belongsTo(Burung::class, 'burung_id')->withTrashed();
    }
}
