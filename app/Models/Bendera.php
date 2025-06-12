<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bendera extends Model
{
    use SoftDeletes;

    protected $table = 'benderas';

    protected $fillable = [
        'nama',
        'point',
        'created_by',
        'updated_by',
    ];
}
