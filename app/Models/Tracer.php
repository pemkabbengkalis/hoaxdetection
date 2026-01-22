<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tracer extends Model
{
    //

    public function traceds(){
        return $this->hasMany(Result::class);
    }
}
