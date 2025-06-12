<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatusQr extends Model
{
    use HasFactory;

    protected $table = 'status_qr';

    protected $fillable = [
        'nama'
    ];

    public function refqrcode()
    {
        return $this->hasMany(RefQrcode::class, 'status_qr_id');
    }
}
