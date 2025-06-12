<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StatusTransaksi extends Model
{
    use SoftDeletes;

    protected $table = 'status_transaksis';

    protected $fillable = [
        'nama',
        'created_by',
        'updated_by',
    ];

    public function transaksis()
    {
        return $this->hasMany(Transaksi::class, 'status_transaksi_id');
    }
}
