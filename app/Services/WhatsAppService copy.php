<?php

namespace App\Services;

use App\Models\User;
use App\Jobs\SendWhatsAppMessage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class WhatsAppService
{
    /**
     * Kirim pesan WhatsApp secara langsung (synchronous).
     * Jika dipanggil dari Job, set $dispatchOnFail = false untuk menghindari infinite loop.
     *
     * @param string $phone  Nomor telepon tujuan
     * @param string $message Pesan yang dikirim
     * @param bool   $dispatchOnFail Apakah harus dispatch job jika gagal (default: true, tapi JANGAN true dari dalam Job)
     * @return bool
     */
    public function send(string $phone, string $message, bool $dispatchOnFail = true): bool
    {
        $phone = $this->normalizePhone($phone);

        if (empty($phone)) {
            Log::warning('WhatsApp: Nomor telepon kosong, pesan tidak dikirim.');
            return false;
        }

        $url = config('services.whatsapp.url');
        $token = config('services.whatsapp.token');

        if (empty($url) || empty($token)) {
            Log::error('WhatsApp: Konfigurasi WA_URL atau WA_TOKEN tidak ditemukan di .env');
            return false;
        }

        try {
            $response = Http::withoutVerifying()
                ->timeout(30)           // Timeout 30 detik agar tidak hang
                ->connectTimeout(10)    // Connect timeout 10 detik
                ->retry(2, 3000, function ($exception) {
                    // Hanya retry jika timeout atau server error (5xx)
                    return $exception instanceof \Illuminate\Http\Client\ConnectionException
                        || ($exception instanceof \Illuminate\Http\Client\RequestException
                            && $exception->response?->serverError());
                })
                ->post($url . '/message/send-text', [
                    'session' => $token,
                    'to'      => $phone,
                    'text'    => $message,
                    'is_group' => false,
                ]);

            if ($response->successful()) {
                Log::info('WhatsApp: Pesan berhasil dikirim ke ' . $phone);
                return true;
            }

            // Response tidak successful
            Log::warning('WhatsApp: Gagal kirim ke ' . $phone, [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);

            // Dispatch ke job untuk retry HANYA jika bukan dipanggil dari Job
            if ($dispatchOnFail) {
                Log::info('WhatsApp: Menjadwalkan retry via Job untuk ' . $phone);
                SendWhatsAppMessage::dispatch($phone, $message);
            }

            return false;
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('WhatsApp: Connection timeout ke server', [
                'phone' => $phone,
                'error' => $e->getMessage(),
            ]);

            if ($dispatchOnFail) {
                SendWhatsAppMessage::dispatch($phone, $message);
            }

            return false;
        } catch (\Exception $e) {
            Log::error('WhatsApp: Error saat mengirim pesan', [
                'phone' => $phone,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            if ($dispatchOnFail) {
                SendWhatsAppMessage::dispatch($phone, $message);
            }

            return false;
        }
    }

    /**
     * Cek apakah server WhatsApp API bisa dijangkau.
     *
     * @return bool
     */
    public function isAvailable(): bool
    {
        return Cache::remember('whatsapp_server_available', 60, function () {
            try {
                $url = config('services.whatsapp.url');
                $response = Http::withoutVerifying()
                    ->timeout(10)
                    ->connectTimeout(5)
                    ->get($url);

                return $response->status() < 500;
            } catch (\Exception $e) {
                Log::warning('WhatsApp: Server tidak tersedia - ' . $e->getMessage());
                return false;
            }
        });
    }

    /**
     * Normalisasi nomor: 08xx → 628xx, 8xx → 628xx
     *
     * @param string|null $phone
     * @return string
     */
    public function normalizePhone(?string $phone): string
    {
        if (empty($phone)) {
            return '';
        }

        $phone = preg_replace('/\D/', '', $phone); // hapus non-digit

        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        } elseif (str_starts_with($phone, '8')) {
            $phone = '62' . $phone;
        }

        return $phone;
    }
}
