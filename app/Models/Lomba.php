<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lomba extends Model
{
    use SoftDeletes;

    protected $table = 'lombas';

    protected $fillable = [
        'nama',
        'tanggal',
        'lokasi',
        'deskripsi',
        'status_lomba_id',  // Menambahkan kolom status_lomba_id
        'created_by',
        'updated_by'
    ];

    public $timestamps = true;


    /**
     * Relasi dengan model Blok
     */
    public function bloks()
    {
        return $this->hasMany(Blok::class);
    }


    public function kelas()
    {
        return $this->hasMany(Kelas::class);
    }
    /**
     * Relasi dengan model Pemesanan
     */
    public function pemesanans()
    {
        return $this->hasMany(Pemesanan::class);
    }

    /**
     * Relasi dengan model Penilaian
     */
    public function penilaians()
    {
        return $this->hasMany(Penilaian::class);
    }

    public function juriTugas()
    {
        return $this->hasMany(JuriTugas::class, 'lomba_id');
    }


    /**
     * Relasi dengan model StatusLomba
     */
    public function statusLomba()
    {
        return $this->belongsTo(StatusLomba::class, 'status_lomba_id');  // Menambahkan relasi dengan StatusLomba
    }
}
