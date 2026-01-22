<?php

namespace App\Filament\Resources\TracerResource\Pages;

use App\Filament\Resources\TracerResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTracer extends EditRecord
{
    protected static string $resource = TracerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
