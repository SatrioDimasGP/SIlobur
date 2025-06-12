<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StatusPenilaian extends Model
{
    use SoftDeletes;

    protected $table = 'status_penilaians';

    protected $fillable = ['nama', 'created_by', 'updated_by'];

    public $timestamps = true;

    public function penilaians()
    {
        return $this->hasMany(Penilaian::class);
    }
}
