<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Burung extends Model
{
    use SoftDeletes;

    protected $table = 'burungs';

    protected $fillable = ['jenis_burung_id', 'kelas_id', 'created_by', 'updated_by'];

    public $timestamps = true;

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    public function jenisBurung()
    {
        return $this->belongsTo(JenisBurung::class);
    }

    public function bloks()
    {
        return $this->hasMany(Blok::class);
    }
}
