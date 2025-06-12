<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JuriTugas extends Model
{
    use SoftDeletes;

    protected $table = 'juri_tugas';

    protected $fillable = [
        'user_id',
        'lomba_id',
        'blok_id',
        'created_by',
        'updated_by',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function lomba()
    {
        return $this->belongsTo(Lomba::class);
    }

    public function blok()
    {
        return $this->belongsTo(Blok::class);
    }
}
