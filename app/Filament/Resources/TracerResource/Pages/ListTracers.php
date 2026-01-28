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
    protected static bool $isLazy = true;
    protected function getHeaderWidgets(): array
    {
        return [
            TracerOverview::class,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            // Action::make('google')
            //     ->label('Google')
            //     ->icon('heroicon-o-globe-alt')
            //     ->color('success')
            //     ->button()
            //     ->action(function () {
            //         // Kosongkan action Livewire agar klik tidak error
            //     })
            //     ->extraAttributes([
            //         'x-data' => '{}',
            //         // Gunakan x-on:click.stop agar Livewire tidak menangkap klik
            //         'x-on:click.stop' => "window.open('https://www.google.com','_blank','width=800,height=600')",
            //         'style' => 'cursor:pointer;',
            //     ]),

            // //->openUrlInNewTab(),

            //     Action::make('popupPreview')
            //         ->label('Preview')
            //         ->icon('heroicon-o-window')
            //         ->button() // penting, agar tidak Livewire
            //         ->extraAttributes([
            //             'onclick' => "window.open(
            //     'https://www.google.com',
            //     'popupWindow',
            //     'width=900,height=600,top=100,left=200,scrollbars=yes,resizable=yes'
            // ); return false;"
            //         ]),



            Actions\Action::make('popupPreview')
                ->label('Preview')
                ->icon('heroicon-o-window')
                ->button()
                ->extraAttributes([
                    'x-on:click.stop' => "
            window.open(
                'https://www.google.com',
                '_blank',
                'width=900,height=600,top=100,left=200,scrollbars=yes,resizable=yes'
            )
        ",
                    'style' => 'cursor:pointer;',
                ])
                ->action(fn() => null),





            Actions\Action::make('google')
                ->label('Google')
                ->icon('heroicon-o-globe-alt')
                ->color('success')
                ->url('https://www.google.com')
                ->openUrlInNewTab(),

            Actions\Action::make('facebook')
                ->label('Facebook')
                ->icon('heroicon-o-globe-alt')
                ->color('info')
                ->url('https://www.facebook.com')
                ->openUrlInNewTab(),

            Actions\Action::make('tiktok')
                ->label('TikTok')
                ->icon('heroicon-o-globe-alt')
                ->color('danger')
                ->url('https://www.tiktok.com')
                ->openUrlInNewTab(),

            Actions\Action::make('instagram')
                ->label('Instagram')
                ->icon('heroicon-o-globe-alt')
                ->color('success')
                ->url('https://www.instagram.com')
                ->openUrlInNewTab(),

            Actions\Action::make('youtube')
                ->label('YouTube')
                ->icon('heroicon-o-globe-alt')
                ->color('danger')
                ->url('https://www.youtube.com')
                ->openUrlInNewTab(),

            Actions\CreateAction::make()
                ->label('Tambah Data'),
        ];
    }
}
