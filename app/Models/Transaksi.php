<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaksi extends Model
{
    use SoftDeletes;

    protected $table = 'transaksis';

    protected $fillable = [
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
        return $this->belongsToMany(Pemesanan::class, 'pemesanan_transaksis')
            ->withTimestamps()
            ->withTrashed(); // jika soft delete
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
