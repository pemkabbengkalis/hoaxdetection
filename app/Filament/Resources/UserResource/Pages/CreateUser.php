<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Models\User;
use Filament\Actions;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use App\Filament\Resources\UserResource;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard\Step;
use Filament\Resources\Pages\CreateRecord;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Form;


class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;


    //-------------adrian------------------------------------//
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

                    Step::make('Email')
                        ->schema([
                            TextInput::make('email')
                                ->label('Email')
                                ->email()
                                ->required()
                                ->validationMessages([
                                    'required' => 'Email wajib diisi',
                                    'email'    => 'Email harus benar',
                                ]),
                        ]),

                    Step::make('Email Verified')
                        ->schema([
                            DateTimePicker::make('email_verified_at')
                                ->label('Email Verified')
                                ->required()
                                ->validationMessages([
                                    'required' => 'Email verified wajib diisi',
                                    'email'    => 'Email verified harus benar',
                                ]),
                        ]),

                    Step::make('Deskripsi')
                        ->schema([
                            Textarea::make('description')
                                ->label('Deskripsi')
                                ->required()
                                ->validationMessages([
                                    'required' => 'Deskripsi wajib diisi',

                                ]),
                        ]),

                    Step::make('Pilih Role')
                        ->schema([
                            Select::make('role')
                                ->options([
                                    User::ROLE_ADMIN     => 'Admin',
                                    User::ROLE_KADIS     => 'Kadis',
                                    User::ROLE_TEAM      => 'Team',
                                    User::ROLE_VALIDATOR => 'Validator',
                                ])
                                ->placeholder('Pilih Role')
                                ->required()
                                ->validationMessages([
                                    'required' => 'Role wajib diisi',
                                ]),
                        ]),
                ])
                    ->columnSpanFull(),
            ]);
    }


    //--------------end of adrian-------------------------------------//

}
