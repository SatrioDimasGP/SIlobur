<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StatusLomba extends Model
{
    protected $table = 'status_lombas';  // Sesuaikan dengan nama tabel Anda

    protected $fillable = ['nama'];  // Misalnya hanya memiliki nama

    public $timestamps = true;

    /**
     * Relasi dengan model Lomba
     */
    public function lombas()
    {
        return $this->hasMany(Lomba::class, 'status_lomba_id');
    }
}
