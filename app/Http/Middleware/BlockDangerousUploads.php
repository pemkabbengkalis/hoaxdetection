<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class BlockDangerousUploads
{
    public const DANGEROUS_FUNCTIONS = [
        'hex2bin(',
        'exit(',
        'eval(',
        'phpinfo(',
        'exec(',
        'system(',
        'passthru(',
        'shell_exec(',
        'proc_open(',
        'popen(',
        'assert(',
        'base64_decode(',
        'file_put_contents(',
        'fopen(',
        'unlink(',
        'mkdir(',
        'curl_exec(',
        'create_function(',
        'file_get_contents(',
        'delete(',
        'update('
    ];

    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $hasViolation = false;

        // 1. Periksa file yang sedang diupload di request
        $files = $request->allFiles();
        array_walk_recursive($files, function ($file) use (&$hasViolation) {
            if ($file instanceof \Illuminate\Http\UploadedFile) {
                if ($this->isDangerous($file->getRealPath(), $file->getClientOriginalExtension())) {
                    @unlink($file->getRealPath());
                    $hasViolation = true;
                }
            }
        });

        // 2. Periksa dan hapus file berbahaya yang mungkin sudah masuk ke folder livewire-tmp
        $livewireTmpPath = storage_path('app/livewire-tmp');
        if (is_dir($livewireTmpPath)) {
            $tmpFiles = File::files($livewireTmpPath);
            foreach ($tmpFiles as $tmpFile) {
                if ($this->isDangerous($tmpFile->getRealPath(), $tmpFile->getExtension())) {
                    @unlink($tmpFile->getRealPath());
                }
            }
        }

        if ($hasViolation) {
            abort(403, 'File upload blocked due to security reasons. Dangerous functions or PHP extension detected.');
        }

        return $next($request);
    }

    private function isDangerous($filePath, $extension): bool
    {
        // Cek ekstensi
        $ext = strtolower($extension);
        if (in_array($ext, ['php', 'php3', 'php4', 'php5', 'phtml', 'phar'])) {
            return true;
        }

        // Cek isi file
        if (file_exists($filePath)) {
            $content = @file_get_contents($filePath);
            if ($content !== false) {
                foreach (self::DANGEROUS_FUNCTIONS as $func) {
                    if (stripos($content, $func) !== false) {
                        return true;
                    }
                }
            }
        }

        return false;
    }
}
