<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('livewire:cleanup-temp', function () {
    $directory = storage_path('app/private/livewire-tmp');

    if (! is_dir($directory)) {
        $this->info('Folder livewire-tmp tidak ditemukan.');
        return self::SUCCESS;
    }

    $files = glob($directory . '/*');
    $deleted = 0;

    foreach ($files as $file) {
        if (is_file($file)) {
            unlink($file);
            $deleted++;
        }
    }

    $this->info("Berhasil menghapus {$deleted} file temporary.");

    return self::SUCCESS;
})->purpose('Clean Livewire temporary upload files');
