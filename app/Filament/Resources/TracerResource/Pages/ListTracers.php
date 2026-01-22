<?php

namespace App\Filament\Resources\TracerResource\Pages;

use App\Filament\Resources\TracerResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTracers extends ListRecords
{
    protected static string $resource = TracerResource::class;



    protected function getHeaderActions(): array
    {
        return [

            Actions\Action::make('google')
                ->label('Google')
                ->icon('heroicon-o-globe-alt')
                ->color('primary')
                ->url('https://www.google.com')
                ->openUrlInNewTab(),

            Actions\Action::make('facebook')
                ->label('Facebook')
                ->icon('heroicon-o-globe-alt')
                ->color('primary')
                ->url('https://www.facebook.com')
                ->openUrlInNewTab(),

            Actions\Action::make('tiktok')
                ->label('TikTok')
                ->icon('heroicon-o-globe-alt')
                ->color('primary')
                ->url('https://www.tiktok.com')
                ->openUrlInNewTab(),

            Actions\Action::make('instagram')
                ->label('Instagram')
                ->icon('heroicon-o-globe-alt')
                ->color('primary')
                ->url('https://www.instagram.com')
                ->openUrlInNewTab(),

            Actions\Action::make('youtube')
                ->label('YouTube')
                ->icon('heroicon-o-globe-alt')
                ->color('primary')
                ->url('https://www.youtube.com')
                ->openUrlInNewTab(),





            Actions\Action::make('detik')
                ->label('Detikcom')
                ->icon('heroicon-o-newspaper')
                ->color('success')
                ->url('https://www.detik.com')
                ->openUrlInNewTab(),

            Actions\Action::make('kompas')
                ->label('Kompas')
                ->icon('heroicon-o-newspaper')
                ->color('success')
                ->url('https://www.kompas.com')
                ->openUrlInNewTab(),


            Actions\CreateAction::make()
                ->label('Tambah Data'),
        ];
    }
}
