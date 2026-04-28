<?php

namespace App\Filament\Resources\KontenResource\Pages;

use Filament\Actions;
use Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\KontenResource;

class ListKontens extends ListRecords
{
    protected static string $resource = KontenResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Tambah Data')
                ->label('Tambah Data')
                //->visible(fn() => auth()->user()->role !== 'validator'),
                ->visible(fn() => !in_array(auth()->user()->role, ['team', 'validator'])) // Hanya tampilkan tombol tambah jika bukan validator,
        ];
    }
}
