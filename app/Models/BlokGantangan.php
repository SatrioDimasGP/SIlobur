<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BlokGantangan extends Model
{
    use SoftDeletes;

    protected $table = 'blok_gantangans';

    protected $fillable = ['blok_id', 'gantangan_id', 'created_by', 'updated_by'];

    public $timestamps = true;

    // Ubah nama fungsi relasi menjadi 'blok'
    public function blok()
    {
        return $this->belongsTo(Blok::class);
    }

    public function gantangan()
    {
        return $this->belongsTo(Gantangan::class);
    }

    public function penilaians()
    {
        return $this->hasMany(Penilaian::class);
    }
}
