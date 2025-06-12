<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tahap extends Model
{
    use SoftDeletes;

    protected $table = 'tahaps';

    protected $fillable = [
        'nama',
        'created_by',
        'updated_by',
    ];
}
