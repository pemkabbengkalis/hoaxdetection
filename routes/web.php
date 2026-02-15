<?php

use Illuminate\Support\Facades\Route;


Route::middleware('throttle:3,1')->get('/', function () {
    return redirect('/admin');
});
