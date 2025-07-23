<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PemesananTransaksi extends Model
{
    use SoftDeletes;

    protected $table = 'pemesanan_transaksis';

    protected $fillable = [
        'transaksi_id',
        'pemesanan_id',
        'created_by',
        'updated_by',
    ];

    // Relasi ke Transaksi
    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class);
    }

    // Relasi ke Pemesanan
    public function pemesanan()
    {
        return $this->belongsTo(Pemesanan::class);
    }

    // Relasi ke User Pembuat
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Relasi ke User Pengubah
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
