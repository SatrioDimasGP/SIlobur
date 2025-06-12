<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaksi extends Model
{
    use SoftDeletes;

    protected $table = 'transaksis';

    protected $fillable = [
        'pemesanan_id', // sesuai nama tabel yang kamu pakai
        'tanggal_transaksi',
        'total',
        'metode_pembayaran',
        'status_transaksi_id',
        'order_id',
        'snap_token',
        'created_by',
        'updated_by',
    ];

    public function pemesanans()
    {
        return $this->belongsTo(Pemesanan::class, 'pemesanan_id');
    }

    public function statusTransaksis()
    {
        return $this->belongsTo(StatusTransaksi::class, 'status_transaksi_id');
    }

    public function qrcode()
    {
        return $this->hasOne(RefQrcode::class, 'transaksi_id');
    }
}
