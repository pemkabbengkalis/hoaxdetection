<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

/**
 * SecurityWafMiddleware (Web Application Firewall)
 *
 * Menerapkan proteksi aktif terhadap serangan siber umum:
 * 1. Scanner & Reconnaissance Bot Probing (Nikto, SQLMap, Nuclei, Gobuster, dll.)
 * 2. Sensitive File & Directory Probing (.env, .git, backup.sql, phpinfo, dll.)
 * 3. SQL Injection (SQLi)
 * 4. Cross-Site Scripting (XSS)
 * 5. Path Traversal & LFI / RFI (Local/Remote File Inclusion)
 * 6. Remote Code Execution (RCE) & Command Injection
 * 7. PHP Stream Wrapper & Null-Byte Injections
 */
class SecurityWafMiddleware
{
    /**
     * Pola User-Agent scanner atau alat penetrasi otomatis.
     */
    protected const SCANNER_USER_AGENTS = [
        'sqlmap',
        'nikto',
        'acunetix',
        'nessus',
        'masscan',
        'zgrab',
        'nmap',
        'gobuster',
        'dirbuster',
        'wpscan',
        'nuclei',
        'havij',
        'pangolin',
        'openvas',
        'burpcollaborator',
        'fuzzdb',
        'arachni',
    ];

    /**
     * Pola URI path berbahaya yang sering dicari oleh bot / penyerang.
     */
    protected const DANGEROUS_PATHS = [
        '#\.(env|git|svn|htaccess|htpasswd|ds_store|aws|ssh)#i',
        '#(wp-login|wp-admin|xmlrpc\.php|adminer\.php|setup\.php|install\.php|eval-stdin\.php|alfa\.php|c99\.php|r57\.php|shell\.php|cmd\.php)#i',
        '#(backup|dump|db|database|schema)\.(sql|tar|gz|zip|bak|old)#i',
        '#(phpunit/phpunit|vendor/phpunit|solr|actuator/gateway|cgi-bin)#i',
        '#\b(phpinfo|php_info|test|info)\.php$#i',
    ];

    /**
     * Pola payload eksploitasi pada request parameter dan body.
     */
    protected const EXPLOIT_PATTERNS = [
        'SQL_INJECTION' => [
            '#\b(union\s+(all\s+)?select\b)#i',
            '#\b(select\s+.+\s+from\s+information_schema\b)#i',
            '#\b(sleep\s*\(\s*\d+\s*\)|benchmark\s*\(\s*\d+\s*,|waitfor\s+delay\b|pg_sleep\s*\()#i',
            '#\b(into\s+(out|dump)file\b)#i',
            '#\b(extractvalue\s*\(|updatexml\s*\()#i',
            '#(?:[\'"\`])\s*or\s+(?:[\'"\`])?\d+(?:[\'"\`])?\s*=\s*(?:[\'"\`])?\d+#i',
            '#(?:[\'"\`])\s*or\s+true\b#i',
        ],
        'PATH_TRAVERSAL_LFI' => [
            '#(?:\.\.[\\\\/]){2,}#i',
            '#(?:%2e%2e[%2f\\\\/]|%252e%252e[%2f\\\\/])#i',
            '#/etc/(?:passwd|shadow|hosts)#i',
            '#c:(?:\\\\|/)windows(?:\\\\|/)win\.ini#i',
            '#proc/self/(?:environ|status|cmdline)#i',
        ],
        'COMMAND_INJECTION_RCE' => [
            '#(?:;\s*|\|\s*|`\s*|\$\(\s*)(?:cat\s+|ls\s+|rm\s+|wget\s+|curl\s+|whoami|id\b|uname\s+|chmod\s+|chown\s+|powershell|cmd\.exe)#i',
        ],
        'PHP_WRAPPER_EXPLOIT' => [
            '#php://(?:input|filter|memory)#i',
            '#data:text/(?:html|plain)#i',
            '#expect://#i',
        ],
        'MALICIOUS_SCRIPT_XSS' => [
            '#<script\b[^>]*>#i',
            '#javascript:\s*[\w\.\(]#i',
            '#vbscript:\s*[\w\.\(]#i',
            '#<iframe\b[^>]*>#i',
            '#<object\b[^>]*>#i',
            '#<embed\b[^>]*>#i',
            '#\b(onerror|onload|onclick|onmouseover|onfocus)\s*=\s*["\']?[^"\'>\s]+#i',
        ],
        'NULL_BYTE' => [
            '#(?:%00|\\x00)#i',
        ],
    ];

    /**
     * Field yang dikecualikan dari scanning SQLi / XSS untuk mencegah false positive
     * (misal: password akun pengguna yang sah bisa mengandung karakter unik).
     */
    protected const EXCLUDED_INPUT_FIELDS = [
        'password',
        'password_confirmation',
        'current_password',
        'token',
        '_token',
    ];

    /**
     * Handle incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Lewatkan static assets publik
        if ($this->isStaticAsset($request)) {
            return $next($request);
        }

        // 2. Blokir Scanner / Tool User-Agent
        $userAgent = $request->userAgent() ?? '';
        foreach (self::SCANNER_USER_AGENTS as $scanner) {
            if (stripos($userAgent, $scanner) !== false) {
                return $this->blockRequest($request, 'SCANNER_USER_AGENT', "Automated scanner/recon bot detected: {$scanner}");
            }
        }

        // 3. Blokir Sensitive Path & Exploit Probing
        $path = $request->path();
        $uri = $request->getRequestUri();
        foreach (self::DANGEROUS_PATHS as $pattern) {
            if (preg_match($pattern, $path) || preg_match($pattern, $uri)) {
                return $this->blockRequest($request, 'SENSITIVE_PATH_PROBE', "Malicious path probe: {$path}");
            }
        }

        // 4. Periksa Query String & URI Parameters
        if ($violation = $this->scanPayload($request->getQueryString() ?? '')) {
            return $this->blockRequest($request, $violation['type'], "Query parameter exploit attempt: {$violation['pattern']}");
        }

        // 5. Periksa Input Body (POST/PUT/PATCH)
        $inputs = $request->except(self::EXCLUDED_INPUT_FIELDS);
        if ($violation = $this->scanInputRecursive($inputs)) {
            return $this->blockRequest($request, $violation['type'], "Payload exploit attempt in input [{$violation['key']}]: {$violation['pattern']}");
        }

        return $next($request);
    }

    /**
     * Cek apakah request menargetkan file asset statis.
     */
    protected function isStaticAsset(Request $request): bool
    {
        $ext = pathinfo($request->path(), PATHINFO_EXTENSION);
        return in_array(strtolower($ext), [
            'css', 'js', 'jpg', 'jpeg', 'png', 'gif', 'ico', 'svg',
            'woff', 'woff2', 'ttf', 'webp', 'map', 'json'
        ], true);
    }

    /**
     * Scan payload string tunggal terhadap daftar pola serangan.
     */
    protected function scanPayload(string $value): ?array
    {
        if (empty($value)) {
            return null;
        }

        $decodedValue = urldecode($value);

        foreach (self::EXPLOIT_PATTERNS as $category => $patterns) {
            foreach ($patterns as $pattern) {
                if (preg_match($pattern, $value) || preg_match($pattern, $decodedValue)) {
                    return [
                        'type' => $category,
                        'pattern' => $pattern,
                    ];
                }
            }
        }

        return null;
    }

    /**
     * Scan array input secara rekursif.
     */
    protected function scanInputRecursive(array $data, string $prefix = ''): ?array
    {
        foreach ($data as $key => $value) {
            $fullKey = $prefix === '' ? (string)$key : "{$prefix}.{$key}";

            if (is_array($value)) {
                $result = $this->scanInputRecursive($value, $fullKey);
                if ($result !== null) {
                    return $result;
                }
            } elseif (is_string($value)) {
                $result = $this->scanPayload($value);
                if ($result !== null) {
                    $result['key'] = $fullKey;
                    return $result;
                }
            }
        }

        return null;
    }

    /**
     * Catat log insiden keamanan dan kembalikan respon blokir HTTP 403.
     */
    protected function blockRequest(Request $request, string $threatType, string $detail): Response
    {
        $clientIp = $request->ip();
        $method = $request->method();
        $url = $request->fullUrl();
        $userAgent = $request->userAgent() ?? 'Unknown';

        $logMessage = sprintf(
            "[SECURITY WAF BLOCKED] [%s] IP: %s | Method: %s | URL: %s | Threat: %s | Detail: %s | UA: %s",
            now()->toIso8601String(),
            $clientIp,
            $method,
            $url,
            $threatType,
            $detail,
            $userAgent
        );

        // Catat ke file log khusus security
        try {
            if (class_exists(\Illuminate\Support\Facades\Log::class) && app()->has('log')) {
                Log::build([
                    'driver' => 'single',
                    'path' => storage_path('logs/security.log'),
                ])->warning($logMessage);
            } else {
                @file_put_contents(storage_path('logs/security.log'), $logMessage . PHP_EOL, FILE_APPEND | LOCK_EX);
            }
        } catch (\Throwable $e) {
            @file_put_contents(storage_path('logs/security.log'), $logMessage . PHP_EOL, FILE_APPEND | LOCK_EX);
        }

        if ($request->expectsJson() || $request->is('livewire/*')) {
            return new \Illuminate\Http\JsonResponse([
                'status' => 'error',
                'code' => 403,
                'message' => 'Akses ditolak: Aktivitas mencurigakan terdeteksi dan diblokir oleh sistem keamanan.',
                'incident_id' => bin2hex(random_bytes(8)),
            ], 403);
        }

        return new \Illuminate\Http\Response(
            '<!DOCTYPE html>
            <html lang="id">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>403 - Akses Ditolak | Security Shield</title>
                <style>
                    body { font-family: system-ui, -apple-system, sans-serif; background: #0f172a; color: #f8fafc; display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; }
                    .box { background: #1e293b; padding: 2.5rem; border-radius: 12px; border: 1px solid #334155; max-width: 480px; text-align: center; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.5); }
                    h1 { color: #ef4444; margin: 0 0 1rem; font-size: 1.8rem; }
                    p { color: #94a3b8; line-height: 1.6; margin-bottom: 1.5rem; font-size: 0.95rem; }
                    .badge { background: #ef44441a; color: #ef4444; border: 1px solid #ef444440; padding: 0.35rem 0.75rem; border-radius: 6px; font-size: 0.8rem; font-family: monospace; display: inline-block; margin-bottom: 1rem; }
                    .btn { display: inline-block; background: #2563eb; color: white; padding: 0.6rem 1.2rem; border-radius: 6px; text-decoration: none; font-weight: 500; font-size: 0.9rem; }
                    .btn:hover { background: #1d4ed8; }
                </style>
            </head>
            <body>
                <div class="box">
                    <div class="badge">SECURITY SHIELD PROTECTION</div>
                    <h1>403 - Akses Ditolak</h1>
                    <p>Permintaan Anda diblokir karena terdeteksi pola aktivitas berbahaya atau tidak sah. Insiden ini telah dicatat demi keamanan sistem.</p>
                    <a href="/admin" class="btn">Kembali ke Halaman Utama</a>
                </div>
            </body>
            </html>',
            403,
            ['Content-Type' => 'text/html; charset=UTF-8']
        );
    }
}
