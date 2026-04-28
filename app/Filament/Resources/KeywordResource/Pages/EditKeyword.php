<?php

namespace App\Filament\Resources\KeywordResource\Pages;

use App\Filament\Resources\KeywordResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;


class EditKeyword extends EditRecord
{
    protected static string $resource = KeywordResource::class;

    protected function getHeaderActions(): array
    {
        return [
           // Actions\DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        Notification::make()
            ->title('Data berhasil disimpan!')
            ->body('Data telah diperbarui dan ditampilkan.')
            ->success()
            ->send();
        $this->redirect($this->getResource()::getUrl('index')); //untuk memberikan redirect setelah penyimpanan
    }
}
