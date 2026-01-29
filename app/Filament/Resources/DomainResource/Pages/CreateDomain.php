<?php

namespace App\Filament\Resources\DomainResource\Pages;

use App\Filament\Resources\DomainResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Forms\Form;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;

class CreateDomain extends CreateRecord
{
    protected static string $resource = DomainResource::class;

    // Hilangkan heading
    public function getHeading(): string
    {
        return '';
    }

    // Redirect ke list setelah create
    protected function getRedirectUrl(): string
    {
        return static::$resource::getUrl('index');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make([
                    Step::make('Domain')
                        ->schema([
                            TextInput::make('name')
                                ->label('Domain')
                                ->required()
                                ->validationMessages([
                                    'required' => 'Domain wajib diisi',
                                ]),
                        ]),

                    Step::make('Ekstensi')
                        ->schema([
                            TextInput::make('extension')
                                ->label('Ekstensi (com, net, org, dll)')
                                ->required()
                                ->validationMessages([
                                    'required' => 'Ekstensi wajib diisi',
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

                    Step::make('Platform')
                        ->schema([
                            Select::make('type')
                                ->label('Platform')
                                ->options([
                                    'media_sosial' => 'Media Sosial',
                                    'media_online' => 'Media Online',
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
}
