<?php

namespace App\Filament\Resources\DomainResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\DomainResource;
use App\Filament\Resources\DomainResource\Widgets\DomaiOverview;
use Closure;

class ListDomains extends ListRecords
{
    protected static string $resource = DomainResource::class;
    //protected static bool $isLazy = true;

    // protected function getHeaderActions(): array
    // {
    //     return [
    //         Actions\CreateAction::make()
    //             ->label('Tambah Data'),


    //     ];
    // }


    protected function getHeaderWidgets(): array
    {
        return [
            DomaiOverview::class,
        ];
    }
}
