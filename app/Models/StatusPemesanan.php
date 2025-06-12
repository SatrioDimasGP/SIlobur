<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StatusPemesanan extends Model
{
    use SoftDeletes;

    protected $table = 'status_pemesanans';

    protected $fillable = [
        'nama',
        'created_by',
        'updated_by',
    ];
}
