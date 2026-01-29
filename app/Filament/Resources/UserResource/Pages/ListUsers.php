<?php

namespace App\Filament\Resources\UserResource\Pages;

use Filament\Actions;
// use Filament\Tables\Actions\Action;
use Filament\Actions\Action;
use Illuminate\Support\HtmlString;
use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\UserResource\Widgets\UserOverview;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah Data'),


            //     Actions\Action::make('preview')
            //         ->label('Preview')
            //         ->icon('heroicon-o-window')
            //         ->button()
            //         ->action(null)
            //         ->extraAttributes([
            //             'onclick' => "window.open(
            //     'https://google.com',
            //     'popupTracer',
            //     'width=900,height=600,scrollbars=yes,resizable=yes'
            // ); return false;",
            //         ]),




        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            UserOverview::class,
        ];
    }
}
