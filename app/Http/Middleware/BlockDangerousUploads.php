<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class BlockDangerousUploads
{
    /**
     * Daftar ekstensi berbahaya (executable, script server-side, config)
     */
    public const BLOCKED_EXTENSIONS = [
        // PHP scripts
        'php', 'php3', 'php4', 'php5', 'php7', 'php8', 'phtml', 'phar', 'pht', 'phps', 'inc', 'hphp',
        // Server side scripts
        'cgi', 'pl', 'py', 'jsp', 'jspx', 'asp', 'aspx', 'asa', 'asax', 'cfm',
        // System binaries & scripts
        'sh', 'bash', 'exe', 'bat', 'cmd', 'vbs', 'ps1', 'com', 'scr', 'msi', 'bin',
        // Web server configuration files
        'htaccess', 'htpasswd', 'user.ini', 'web.config',
    ];

    /**
     * Daftar fungsi berbahaya yang sering disisipkan ke dalam webshell / payload backdoor
     */
    public const DANGEROUS_FUNCTIONS = [
        '<?php',
        '<?=',
        '<script language="php"',
        'hex2bin(',
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
        'create_function(',
    ];

    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $hasViolation = false;
        $violationReason = '';

        // 1. Periksa file yang sedang diupload di request
        $files = $request->allFiles();
        array_walk_recursive($files, function ($file) use (&$hasViolation, &$violationReason) {
            if ($file instanceof \Illuminate\Http\UploadedFile) {
                $check = $this->isDangerous(
                    $file->getRealPath(),
                    $file->getClientOriginalExtension(),
                    $file->getClientOriginalName()
                );

                if ($check['dangerous']) {
                    @unlink($file->getRealPath());
                    $hasViolation = true;
                    $violationReason = $check['reason'];
                }
            }
        });

        // 2. Periksa dan bersihkan folder livewire-tmp dari sisa file berbahaya
        $livewireTmpPath = storage_path('app/livewire-tmp');
        if (is_dir($livewireTmpPath)) {
            $tmpFiles = glob($livewireTmpPath . '/*');
            if ($tmpFiles !== false) {
                foreach ($tmpFiles as $tmpFilePath) {
                    if (is_file($tmpFilePath)) {
                        $filename = basename($tmpFilePath);
                        $extension = pathinfo($tmpFilePath, PATHINFO_EXTENSION);
                        $check = $this->isDangerous(
                            $tmpFilePath,
                            $extension,
                            $filename
                        );

                        if ($check['dangerous']) {
                            @unlink($tmpFilePath);
                        }
                    }
                }
            }
        }

        if ($hasViolation) {
            $msg = sprintf(
                "[SECURITY UPLOAD BLOCKED] IP: %s | URL: %s | Reason: %s",
                $request->ip(),
                $request->fullUrl(),
                $violationReason
            );

            try {
                if (class_exists(\Illuminate\Support\Facades\Log::class) && app()->has('log')) {
                    Log::warning($msg);
                } else {
                    @file_put_contents(storage_path('logs/security.log'), $msg . PHP_EOL, FILE_APPEND | LOCK_EX);
                }
            } catch (\Throwable $e) {
                @file_put_contents(storage_path('logs/security.log'), $msg . PHP_EOL, FILE_APPEND | LOCK_EX);
            }

            abort(403, "File upload blocked due to security reasons: {$violationReason}");
        }

        return $next($request);
    }

    /**
     * Validasi mendalam pada nama, ekstensi, dan konten file
     */
    private function isDangerous($filePath, $extension, $originalName = ''): array
    {
        $ext = strtolower((string)$extension);
        $name = strtolower((string)$originalName);

        // 1. Cek null byte injection pada nama file
        if (str_contains($name, "\0") || str_contains($name, '%00')) {
            return ['dangerous' => true, 'reason' => 'Null-byte injection in filename detected.'];
        }

        // 2. Cek ekstensi utama
        if (in_array($ext, self::BLOCKED_EXTENSIONS, true)) {
            return ['dangerous' => true, 'reason' => "Forbidden file extension (.{$ext}) detected."];
        }

        // 3. Cek double extension (misal: payload.php.jpg, backdoor.phtml.png)
        foreach (self::BLOCKED_EXTENSIONS as $badExt) {
            if (preg_match('/\\.' . preg_quote($badExt, '/') . '(\\.|;|$)/i', $name)) {
                return ['dangerous' => true, 'reason' => "Dangerous double extension (.{$badExt}) detected in filename."];
            }
        }

        // 4. Cek konten file
        if ($filePath && file_exists($filePath)) {
            $content = @file_get_contents($filePath, false, null, 0, 1024 * 512); // Baca hingga 512KB awal
            if ($content !== false && $content !== '') {
                // Cek fungsi-fungsi berbahaya PHP
                foreach (self::DANGEROUS_FUNCTIONS as $func) {
                    if (stripos($content, $func) !== false) {
                        return ['dangerous' => true, 'reason' => "Dangerous code pattern ({$func}) detected inside file content."];
                    }
                }

                // Cek khusus file SVG (mencegah Stored XSS via SVG)
                if ($ext === 'svg' || str_ends_with($name, '.svg')) {
                    if (preg_match('/<script\b|javascript:|onerror\s*=|onload\s*=/i', $content)) {
                        return ['dangerous' => true, 'reason' => 'Embedded malicious script/event detected in SVG file.'];
                    }
                }
            }
        }

        return ['dangerous' => false, 'reason' => ''];
    }
}
