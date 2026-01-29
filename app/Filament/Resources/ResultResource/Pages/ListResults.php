<?php

namespace App\Filament\Resources\ResultResource\Pages;

use App\Filament\Resources\ResultResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\ResultResource\Widgets\ResultOverview;


class ListResults extends ListRecords
{
    protected static string $resource = ResultResource::class;
    //-----------------adrian----------------------------------//
    // protected static bool $isLazy = true;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah Data'),
        ];
    }

    protected function getTableRecordUrlUsing(): ?\Closure
    {
        return null; // menonaktifkan klik edit baris
    }


    protected function getHeaderWidgets(): array
    {
        return [
            ResultOverview::class,
        ];
    }
    //----------------end of adrian---------------------//
}
