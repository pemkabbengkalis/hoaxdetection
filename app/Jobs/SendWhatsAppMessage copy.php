<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Services\WhatsAppService;

class SendWhatsAppMessage implements ShouldQueue
{
    use Queueable;

    /**
     * Jumlah percobaan maksimum.
     */
    public int $tries = 5;

    /**
     * Delay antar retry (dalam detik): 1 menit, 2 menit, 5 menit, 10 menit.
     */
    public array $backoff = [60, 120, 300, 600];

    /**
     * Timeout per eksekusi job (dalam detik).
     */
    public int $timeout = 60;

    /**
     * Jumlah detik unik lock agar job yang sama tidak di-dispatch berulang.
     */
    public int $uniqueFor = 300; // 5 menit

    public function __construct(
        public string $phone,
        public string $message
    ) {}

    /**
     * Execute the job.
     * PENTING: dispatchOnFail = false agar TIDAK terjadi infinite loop
     */
    public function handle(WhatsAppService $whatsAppService): void
    {
        Log::info('SendWhatsAppMessage: Mencoba mengirim ke ' . $this->phone, [
            'attempt' => $this->attempts(),
        ]);

        $success = $whatsAppService->send(
            $this->phone,
            $this->message,
            false // ← KUNCI: jangan dispatch ulang dari dalam Job, biar mekanisme retry Queue yang handle
        );

        if (!$success) {
            // Release job untuk retry berikutnya sesuai $backoff
            // Jika sudah mencapai $tries, job akan masuk failed_jobs
            $this->release(
                $this->backoff[$this->attempts() - 1] ?? 600
            );
        }
    }

    /**
     * Handle a job failure setelah semua retry habis.
     */
    public function failed(?\Throwable $exception): void
    {
        Log::error('SendWhatsAppMessage: GAGAL setelah semua retry', [
            'phone'   => $this->phone,
            'message' => $this->message,
            'error'   => $exception?->getMessage(),
        ]);

        // Bisa tambahkan notifikasi ke admin di sini jika perlu
        // Notification::route('mail', 'admin@example.com')
        //     ->notify(new WhatsAppFailedNotification($this->phone, $this->message));
    }
}
