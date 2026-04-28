<?php

namespace App\Filament\Resources\KontenResource\Pages;

use App\Filament\Resources\KontenResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListKontens extends ListRecords
{
    protected static string $resource = KontenResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
<<<<<<< HEAD
            ->label('Tambah Data'),
=======
                ->label('Tambah Data')
                //->visible(fn() => auth()->user()->role !== 'validator'),
                ->visible(fn() => !in_array(auth()->user()->role, ['team', 'validator'])) // Hanya tampilkan tombol tambah jika bukan validator,
>>>>>>> meldi-adrian
        ];
    }
}
