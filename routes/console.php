<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Storage;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


Artisan::command('livewire:cleanup-temp', function () {
    $disk = Storage::disk('local');

    $directory = 'livewire-tmp';

    if (! $disk->exists($directory)) {
        $this->info('Folder livewire-tmp tidak ditemukan.');
        return;
    }

    foreach ($disk->allFiles($directory) as $file) {
        $disk->delete($file);
    }

    $this->info('Temporary Livewire files berhasil dibersihkan.');
})->purpose('Clean Livewire temporary upload files');


Schedule::command('livewire:cleanup-temp')
    ->everyMinute()
    ->withoutOverlapping();
