<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kelas extends Model
{
    use SoftDeletes;

    protected $table = 'kelas';

    protected $fillable = [
        'nama',
        'harga',
        'lomba_id',
        'created_by',
        'updated_by',
    ];

    public $timestamps = true;

    // Relasi ke burungs (satu kelas memiliki banyak burung)
    public function burungs()
    {
        return $this->hasMany(Burung::class);
    }

    // Relasi ke lomba (kelas milik satu lomba)
    public function lomba()
    {
        return $this->belongsTo(Lomba::class);
    }
}
