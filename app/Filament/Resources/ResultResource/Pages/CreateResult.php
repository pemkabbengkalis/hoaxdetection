<?php

namespace App\Filament\Resources\ResultResource\Pages;

use Filament\Actions;
use App\Models\Domain;
use App\Filament\Resources\ResultResource;
use Filament\Resources\Pages\CreateRecord;


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

    protected function afterCreate(): string
    {
        return redirect('tracers')->with('success', 'Data berhasil disimpan');
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
}
