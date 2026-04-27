<?php

namespace App\Filament\Resources\KontenResource\Pages;

use App\Filament\Resources\KontenResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;



class EditKonten extends EditRecord
{
    protected static string $resource = KontenResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    //-----------hapus notifikasi save-----------------
    protected function getSavedNotification(): ?Notification
    {
        return null;
    }
    //-----------end of hapus notifikasi save-----------------


    //redirect to list after create
    public function getHeading(): string
    {
        return '';
    }
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    //end of redirect to list after create


    protected function afterSave(): void
    {
        Notification::make()
            ->title('Data berhasil diperbarui!')
            ->success()
            ->send();
    }

    protected function getFormActions(): array
    {
        return [
            Actions\Action::make('save')
                ->label('Simpan Perubahan')
                ->submit('save')
                ->color('gray'),

            Actions\Action::make('cancel')
                ->label('Batal') // ini yang mengganti "Cancel"
                ->url($this->getResource()::getUrl())
                ->color('gray'), // kembali ke halaman list
            // ->color('gray') // optional biar mirip default
        ];
    }
}
