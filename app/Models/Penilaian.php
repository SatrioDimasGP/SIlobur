<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Penilaian extends Model
{
    use SoftDeletes;

    protected $table = 'penilaians';

    protected $fillable = [
        'user_id',
        'lomba_id',
        'blok_gantangan_id',
        'burung_id', // Tambahkan ini
        'bendera_id',
        'tahap_id',
        'status_penilaian_id',
        'created_by',
        'updated_by',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function lomba()
    {
        return $this->belongsTo(Lomba::class);
    }

    public function blokGantangan()
    {
        return $this->belongsTo(BlokGantangan::class);
    }

    public function burung()
    {
        return $this->belongsTo(Burung::class, 'burung_id');
    }

    public function bendera()
    {
        return $this->belongsTo(Bendera::class);
    }

    public function tahap()
    {
        return $this->belongsTo(Tahap::class);
    }

    public function statusPenilaian()
    {
        return $this->belongsTo(StatusPenilaian::class, 'status_penilaian_id');
    }
}
