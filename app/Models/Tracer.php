<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Tracer extends Model
{
    //

    public function traceds()
    {
        return $this->hasMany(Result::class);
    }

    protected $fillable = [
        'capture',
    ];


    public function domain()
    {
        return $this->belongsTo(\App\Models\Domain::class);
    }
}
