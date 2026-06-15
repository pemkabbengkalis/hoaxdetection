<?php

namespace App\Filament\Resources\ResultResource\Pages;

use Filament\Actions;
use App\Models\Domain;
use App\Filament\Resources\ResultResource;
use Filament\Resources\Pages\CreateRecord;
use App\Models\User;
use App\Jobs\SendWhatsAppMessage;
use Carbon\Carbon;
use Filament\Notifications\Notification;





class CreateResult extends CreateRecord
{
    protected static string $resource = ResultResource::class;


    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $domainName = $this->extractDomain($data['url']);

        $domain = Domain::firstOrCreate(
            [
                'name' => $domainName,
                'description' => '-',
                'type' => 'media_online',
                'extension' => $this->getDomainExtension($domainName)
            ]

        );

        $data['domain_id'] = $domain->id;

        return $data;
    }

    protected function afterCreate(): void
    {
        $record = $this->record;
        $nokadis = User::whereRole('kadis')->first()?->no_hp;

        if (empty($nokadis)) {
            \Illuminate\Support\Facades\Log::warning('WhatsApp: Nomor HP Kadis tidak ditemukan.');
            return;
        }

        $message = "🔔 *Informasi Ada Berita Hoax!*\n\n"
            . "📌 *Keyword:* {$record->keyword}\n"
            . "🌐 *URL:* {$record->url}\n"
            . "👤 *Target Akun:* {$record->target_account}\n"
            . "📂 *Kategori:* {$record->category}\n"
            . "📊 *Status:* {$record->status}\n"
            . "📝 *Keterangan:* {$record->keterangan}\n"
            . "🕐 Waktu: " . Carbon::now('Asia/Jakarta')->format('d-m-Y H:i:s');

        // Kirim via Job — retry otomatis jika gagal, tanpa duplikasi
        SendWhatsAppMessage::dispatch($nokadis, $message);
    }




    private function getDomainExtension(string $url): string
    {
        $host = parse_url($url, PHP_URL_HOST);

        if (!$host) {
            return '';
        }

        // hilangkan www

        $parts = explode('.', $host);

        // ambil 1 bagian terakhir
        return end($parts);
    }

    private function extractDomain(string $url): string
    {
        $host = parse_url($url, PHP_URL_HOST);

        if (!$host) {
            return '';
        }

        return $host;
    }

    //---------------adrian---------------------//
    //redirect to list after create
    public function getHeading(): string
    {
        return '';
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    //end of redirect to list after create
    //-------------end of adrian---------------//

    protected function afterSave(): void
    {
        Notification::make()
            ->title('Data berhasil disimpan!')
            ->success()
            ->send();
        //$this->redirect($this->getResource()::getUrl('index')); //untuk memberikan redirect setelah penyimpanan
    }
}
