<?php

namespace App\Filament\Resources\KontenResource\Pages;

use App\Filament\Resources\KontenResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateKonten extends CreateRecord
{
    protected static string $resource = KontenResource::class;

    protected function getFormAttributes(): array
    {
        return [
            'novalidate' => 'novalidate',
        ];
    }
}
