<?php

namespace App\Filament\Resources\ResultResource\Pages;

use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\ResultResource;
use App\Filament\Resources\ResultResource\Widgets\ResultOverview;


class ListResults extends ListRecords
{
    protected static string $resource = ResultResource::class;
    //-----------------adrian----------------------------------//
    // protected static bool $isLazy = true;

    protected function getActions(): array
    {
        return [
            Action::make('connect')
                ->label('Connect WhatsApp')
                ->icon('heroicon-o-chat-bubble-left-right')
                ->action(function () {
                    // logic kamu di sini
                    // contoh: generate session / QR
                }),
        ];
    }
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
