<?php

namespace App\Filament\Resources\ManualBookResource\Pages;

use App\Filament\Resources\ManualBookResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListManualBooks extends ListRecords
{
    protected static string $resource = ManualBookResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
