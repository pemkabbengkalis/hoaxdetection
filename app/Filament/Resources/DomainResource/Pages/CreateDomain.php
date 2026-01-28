<?php

namespace App\Filament\Resources\DomainResource\Pages;

use App\Filament\Resources\DomainResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateDomain extends CreateRecord
{
    protected static string $resource = DomainResource::class;

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


    // //Fungi Untuk Hiden Tombol Tambah Lainnya & Simpan
    // protected function getFormActions(): array
    // {
    //     return collect(parent::getFormActions())
    //         ->reject(fn($action) => $action->getName() === 'createAnother')
    //         ->all();
    // }
    // //End Fungsi Untuk Hiden Tombol Tambah Lainnya & Simpan

}
