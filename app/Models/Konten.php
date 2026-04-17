<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;


class Konten extends Model
{
    protected $fillable = [
        'name',
        'url',
        'keterangan',

    ];

    // function user()
    // {
    //     return $this->belongsTo(User::class);
    // }
}
