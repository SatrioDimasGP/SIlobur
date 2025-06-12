<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pemesanan extends Model
{
    use SoftDeletes;

    protected $table = 'pemesanans';

    protected $fillable = [
        'user_id',
        'nama',
        'gantangan_id',
        'burung_id', // Tambahkan kolom baru ini
        'status_pemesanan_id',
        'lomba_id',
        'created_by',
        'updated_by',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function gantangan()
    {
        return $this->belongsTo(Gantangan::class);
    }

    public function lomba()
    {
        return $this->belongsTo(Lomba::class);
    }

    public function status()
    {
        return $this->belongsTo(StatusPemesanan::class, 'status_pemesanan_id');
    }

    public function transaksi()
    {
        return $this->hasOne(Transaksi::class, 'pemesanan_id');
    }

    public function burung()
    {
        return $this->belongsTo(Burung::class, 'burung_id');
    }
}
