<?php

namespace App\Filament\Resources\NewsResource\Pages;

use App\Filament\Resources\NewsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;


class EditNews extends EditRecord
{
    protected static string $resource = NewsResource::class;

    //-----------hapus notifikasi save-----------------
    protected function getSavedNotification(): ?Notification
    {
        return null;
    }
    //-----------end of hapus notifikasi save-----------------

    protected function getHeaderActions(): array
    {
        return [
            // Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function afterSave(): void
    {
        Notification::make()
            ->title('Data berhasil diperbarui!')
            ->success()
            ->send();
    }
}
