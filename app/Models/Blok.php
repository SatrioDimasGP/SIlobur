<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

class Blok extends Model
{
    use SoftDeletes;

    protected $table = 'bloks';

    protected $fillable = ['nama', 'lomba_id', 'burung_id', 'created_by', 'updated_by'];

    public $timestamps = true;

    public function lomba()
    {
        return $this->belongsTo(Lomba::class);
    }

    public function gantangans()
    {
        return $this->hasMany(BlokGantangan::class);
    }

    public function juriTugas()
    {
        return $this->hasMany(JuriTugas::class);
    }

    // CUSTOM: Mengambil semua burung dari gantangans
    public function getBurungsAttribute()
    {
        return $this->lomba->kelas->flatMap->burungs->unique('id');
    }

    public function burung()
    {
        return $this->belongsTo(Burung::class);
    }
}
