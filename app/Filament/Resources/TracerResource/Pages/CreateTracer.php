<?php

namespace App\Filament\Resources\TracerResource\Pages;

use App\Models\User;
// use Filament\Actions;
use Filament\Forms\Form;
use Filament\Actions\CancelAction;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\TextInput;
use App\Filament\Resources\TracerResource;
use Filament\Forms\Components\Wizard\Step;
use Filament\Resources\Pages\CreateRecord;



class CreateTracer extends CreateRecord
{
    protected static string $resource = TracerResource::class;

    //------------------------adrian---------------------------------------------//
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



    //---------------step wizard------------------------------------//

    public function form(Form $form): Form

    {
        return $form
            ->schema([
                Wizard::make([
                    Step::make('Nama')
                        ->schema([
                            TextInput::make('name')
                                ->label('Nama')
                                ->required()
                                ->validationMessages([
                                    'required' => 'Nama wajib diisi',
                                ]),
                        ]),

                    Step::make('Domain')
                        ->schema([
                            TextInput::make('domain')
                                ->label('Domain')
                                ->required()
                                ->validationMessages([
                                    'required' => 'Domain wajib diisi',
                                ]),
                        ]),


                    Step::make('Platform')
                        ->schema([
                            Select::make('type_platform')
                                ->label('Platform')
                                ->options([
                                    'media_sosial'  => 'Media Sosial',
                                    'search_engine' => 'Search Engine',
                                ])
                                ->placeholder('Pilih platform')
                                ->required()
                                ->validationMessages([
                                    'required' => 'Platform wajib dipilih',
                                ]),
                        ]),

                ])
                    ->columnSpanFull(),

            ]);
    }
    // --------------end of step wizard-----------------------------//



    // //Fungi Untuk Hiden Tombol Tambah Lainnya & Simpan
    // protected function getFormActions(): array
    // {
    //     return collect(parent::getFormActions())
    //         ->reject(fn($action) => $action->getName() === 'createAnother')
    //         ->all();
    // }
    // //End Fungsi Untuk Hiden Tombol Tambah Lainnya & Simpan

    //-----------------------------end of adrian----------------------------------//
}
