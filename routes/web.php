<?php

use Illuminate\Support\Facades\Route;


Route::middleware('throttle:3,1')->get('/', function () {
    return redirect('/admin');
});

// Route::middleware('throttle:3,1')->group(function () {

//     Route::get('/wa-login', function () {
//         return view('filament.qrlogin');
//     })->name('wa-login');
// });
Route::middleware(['auth', 'throttle:3,1'])->group(function () {

    Route::get('/wa-login', function () {
        if (auth()->user()->role !== 'admin') {
            abort(403);
        }

        return view('filament.qrlogin');
    })->name('wa-login');
});
