<?php

namespace App\Filament\Resources\KeywordResource\Pages;

use App\Filament\Resources\KeywordResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;


class CreateKeyword extends CreateRecord
{
    protected static string $resource = KeywordResource::class;

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
            ->title('Data berhasil disimpan!')
            ->success()
            ->send();
    }
}
