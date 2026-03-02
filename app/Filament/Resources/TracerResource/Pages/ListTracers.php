<?php

namespace App\Filament\Resources\TracerResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\TracerResource;
use App\Filament\Resources\TracerResource\Widgets\TracerOverview;
use Filament\Actions\Action;

class ListTracers extends ListRecords
{
    protected static string $resource = TracerResource::class;
    //protected static bool $isLazy = true;
    protected function getHeaderWidgets(): array
    {
        return [
            TracerOverview::class,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [

            Actions\CreateAction::make()
                ->label('Tambah Data'),
        ];
    }
}
