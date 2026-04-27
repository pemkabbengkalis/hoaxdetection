<?php

namespace App\Filament\Resources\KontenResource\Pages;

use App\Filament\Resources\KontenResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;


class CreateKonten extends CreateRecord
{
    protected static string $resource = KontenResource::class;


    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = Auth::id();

        return $data;
    }
    protected function getFormAttributes(): array
    {
        return [
            'novalidate' => 'novalidate',
        ];
    }

    public function getHeading(): string
    {
        return '';
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }


    protected function afterSave(): void
    {
        Notification::make()
            ->title('Data berhasil disimpan!')
            ->success()
            ->send();
        //$this->redirect($this->getResource()::getUrl('index')); //untuk memberikan redirect setelah penyimpanan
    }
}
