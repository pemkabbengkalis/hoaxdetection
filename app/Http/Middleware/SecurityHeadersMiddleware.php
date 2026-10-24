<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * SecurityHeadersMiddleware
 *
 * Menerapkan HTTP Response Security Headers standar industri (OWASP Top 10, CWE-1021, BSSN).
 * Memitigasi kerentanan:
 * 1. Clickjacking / UI Redressing (X-Frame-Options: SAMEORIGIN & CSP frame-ancestors 'self')
 * 2. MIME-type Sniffing (X-Content-Type-Options: nosniff)
 * 3. Base URI Hijacking & Plugin Injections (CSP base-uri 'self', object-src 'none')
 * 4. Cross-Domain Policy Exploits (X-Permitted-Cross-Domain-Policies: none)
 * 5. Direct Execution of Downloads (X-Download-Options: noopen)
 * 6. Information Disclosure (Hapus header X-Powered-By & Server)
 * 7. Sensitive Admin Data Caching (Cache-Control: no-store pada area terotentikasi)
 * 8. SSL Stripping & Downgrade Attacks (Strict-Transport-Security HSTS saat HTTPS)
 * 9. Privacy & Referrer Leakage (Referrer-Policy: strict-origin-when-cross-origin)
 * 10. Hardware API Exploitation (Permissions-Policy)
 */
class SecurityHeadersMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // 1. Mitigasi Clickjacking (CWE-1021)
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        // 2. Content-Security-Policy (CSP) - Proteksi framing & embedding liar
        $csp = $response->headers->get('Content-Security-Policy');
        if ($csp) {
            $additions = [];
            if (!str_contains($csp, 'frame-ancestors')) {
                $additions[] = "frame-ancestors 'self'";
            }
            if (!str_contains($csp, 'base-uri')) {
                $additions[] = "base-uri 'self'";
            }
            if (!str_contains($csp, 'object-src')) {
                $additions[] = "object-src 'none'";
            }
            if (!empty($additions)) {
                $response->headers->set('Content-Security-Policy', rtrim($csp, '; ') . '; ' . implode('; ', $additions));
            }
        } else {
            $response->headers->set('Content-Security-Policy', "frame-ancestors 'self'; base-uri 'self'; object-src 'none'");
        }

        // 3. Mitigasi MIME-type Sniffing (CWE-79 / CWE-434)
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // 4. Proteksi XSS Legacy Browser
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // 5. Mitigasi Referrer Leakage
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // 6. Batasi akses fitur peramban dan perangkat keras (Permissions-Policy)
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=(), payment=(), usb=()');

        // 7. Blokir Cross-Domain Policy Exploits
        $response->headers->set('X-Permitted-Cross-Domain-Policies', 'none');

        // 8. Mitigasi Eksekusi Otomatis File Unduhan di IE/Legacy Windows
        $response->headers->set('X-Download-Options', 'noopen');

        // 9. Cegah Caching Data Sensitif pada sesi login/admin
        if ($request->is('admin*') || $request->user()) {
            $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0, post-check=0, pre-check=0');
            $response->headers->set('Pragma', 'no-cache');
        }

        // 10. HSTS (Strict-Transport-Security) saat koneksi HTTPS aktif
        if ($request->isSecure() || env('APP_ENV') === 'production') {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        // 11. Hapus Informasi Versi Server / PHP (Information Disclosure)
        $response->headers->remove('X-Powered-By');
        $response->headers->remove('Server');
        if (function_exists('header_remove')) {
            @header_remove('X-Powered-By');
            @header_remove('Server');
        }

        return $response;
    }
}
