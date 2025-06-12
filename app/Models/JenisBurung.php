<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JenisBurung extends Model
{
    use SoftDeletes;

    protected $table = 'jenis_burungs';

    protected $fillable = ['nama', 'created_by', 'updated_by'];

    public $timestamps = true;

    public function burungs()
    {
        return $this->hasMany(Burung::class);
    }
}
