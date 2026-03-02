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
        // return $this->getResource()::getUrl('index');
        return route('filament.admin.resources.results.create', [
            'tracer_id' => $this->record->id,
        ]);
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
                                ->unique(ignoreRecord: true)
                                ->placeholder('Isi nama platform ex : google')
                                ->validationMessages([
                                    'required' => 'Nama wajib diisi',
                                ]),
                        ]),

                    // Step::make('Domain')
                    //     ->schema([
                    //         TextInput::make('domain')
                    //             ->label('Domain')
                    //             ->unique(ignoreRecord: true)
                    //             ->placeholder('Isi nama domain ex : www.google.com')
                    //             ->required()
                    //             ->validationMessages([
                    //                 'required' => 'Domain wajib diisi',
                    //             ]),
                    //     ]),
                    Step::make('Domain')
                        ->schema([
                            TextInput::make('domain')
                                ->label('Domain')
                                ->placeholder('Isi nama domain ex : www.google.com')
                                ->required()
                                ->unique(ignoreRecord: true)
                                ->reactive()
                                ->afterStateUpdated(function ($state, callable $set) {

                                    $domain = strtolower($state);

                                    $searchEngines = [
                                        'www.google.com',
                                        'www.google.co.id',
                                        'www.bing.com',
                                        'www.yahoo.com',
                                        'www.id.yahoo.com',
                                        'www.duckduckgo.com',
                                        'www.baidu.com',
                                        'www.yandex.com',
                                        'www.ecosia.org',
                                        'www.kompas.com',
                                        'www.detik.com',
                                        'www.tribunnews.com',
                                        'www.liputan6.com',
                                        'www.cnnindonesia.com',
                                        'www.tempo.co',
                                        'www.tempo.com',
                                        'www.republika.co.id',
                                        'www.merdeka.com',
                                        'www.okezone.com',
                                        'www.sindonews.com',
                                        'www.pikiran-rakyat.com',
                                        'www.suara.com',
                                        'mediaindonesia.com',
                                        'www.antaranews.com',
                                        'www.bbc.com/indonesia',

                                        // Riau
                                        'www.daririau.id',
                                        'www.riaunews.com',
                                        'www.seputarriau.co',
                                        'www.lamanriau.com',
                                        'www.riaupedia.com',
                                        'riaupos.jawapos.com',
                                        'www.bertuahpos.com',
                                        'www.cakaplah.com',
                                        'www.goriau.com',
                                        'www.riauonline.co.id',
                                        'www.riauterkini.com',
                                        'www.tribunpekanbaru.com',
                                        'www.riasatu.com',
                                        'www.ekonomipos.com',
                                        'www.utusanriau.com',
                                        'www.potretnews.com',
                                        'www.pantauriau.com',
                                        'www.riauterbit.com',
                                        'www.duriportalriau.com',
                                        'www.homeriau.com',
                                        'www.bekawan.com',
                                        'www.resonansi.co',
                                        'www.riau24.com',
                                        'www.datariau.com',
                                        'www.kanalkini.com',
                                        'www.riaupostingnews.com',
                                        'www.nuansamedianews.com',
                                        'www.ulastinta.com',
                                        'www.detik19.com',
                                        'www.gresriau.com',
                                        'www.kabarinvestigasi.com',
                                        'www.riauvoice.com',
                                        'www.teraskampar.id',
                                        'www.galamedia.co.id',
                                        'www.riaudetil.com',
                                        'www.beritatuan.com',
                                        'www.mediacenter.riau.go.id',
                                    ];

                                    foreach ($searchEngines as $engine) {
                                        if (str_contains($domain, $engine)) {
                                            $set('type_platform', 'search_engine');
                                            return;
                                        }
                                    }

                                    $set('type_platform', 'media_sosial');
                                }),
                        ]),




                    Step::make('Platform')
                        ->schema([
                            TextInput::make('type_platform')
                                ->label('Platform')
                                ->disabled()
                                ->dehydrated(),
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
