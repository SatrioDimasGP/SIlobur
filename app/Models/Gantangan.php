<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Gantangan extends Model
{
    use SoftDeletes;

    protected $table = 'gantangans';

    // Sesuaikan fillable dengan kolom tabel
    protected $fillable = [
        'nomor',
        'created_by',
        'updated_by',
    ];

    public $timestamps = true;

    // Relasi pemesanan: satu gantangan bisa dipakai oleh satu pemesanan
    public function pemesanan()
    {
        return $this->hasMany(Pemesanan::class);
    }

    // Relasi blok gantangan: satu gantangan bisa masuk banyak blok gantangan
    public function blokGantangans()
    {
        return $this->hasMany(BlokGantangan::class);
    }
}
