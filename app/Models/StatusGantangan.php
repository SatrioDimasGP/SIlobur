<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StatusGantangan extends Model
{
    use SoftDeletes;

    protected $table = 'status_gantangans';

    protected $fillable = ['nama', 'created_by', 'updated_by'];

    public $timestamps = true;

    public function gantangans()
    {
        return $this->hasMany(Gantangan::class);
    }
}
