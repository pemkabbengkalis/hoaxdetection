<?php

namespace App\Filament\Resources\ResultResource\Pages;

use Filament\Actions;
use App\Models\Domain;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\ResultResource;

class EditResult extends EditRecord
{
    protected static string $resource = ResultResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $domainName = $this->extractDomain($data['url']);

        $domain = Domain::firstOrCreate([
            'name' => $domainName,
            'description' => '-',
            'type' => 'media_online',
            'extension' => 'id'
        ]);

        $data['domain_id'] = $domain->id;

        return $data;
    }
    private function extractDomain(string $url): string
    {
        $host = parse_url($url, PHP_URL_HOST);

        if (!$host) {
            return '';
        }

        return preg_replace('/^www\./', '', $host);
    }
}
