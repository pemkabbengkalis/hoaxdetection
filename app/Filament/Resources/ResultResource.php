<?php

namespace App\Filament\Resources;

use Closure;
use Filament\Forms;
use Filament\Tables;
use Filament\Actions;
use App\Models\Result;
use App\Models\Tracer;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\Storage;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\FileUpload;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\ResultResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Eloquent\Factories\Relationship;
use App\Filament\Resources\ResultResource\RelationManagers;
use App\Filament\Resources\ResultResource\Widgets\ResultOverview;
use Filament\Facades\Filament;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Schema\Blueprint;





class ResultResource extends Resource
{

    public static string $resource = ResultResource::class;
    protected static ?string $model = Result::class;
    protected static ?string $navigationIcon = 'heroicon-s-rectangle-stack';
    protected static ?int $navigationSort = 2;

    //---------------adrian---------------------//

    public static function canViewAny(): bool
    {
        $user = Filament::auth()->user();

        // ⛔ Kalau belum login / bukan user valid
        if (! $user instanceof User) {
            return false;
        }

        // 🔐 Batasi berdasarkan role
        return $user->hasAnyRole([
            User::ROLE_ADMIN,
            // User::ROLE_KADIS,
            User::ROLE_VALIDATOR,
            User::ROLE_TEAM,
        ]);
    }

    public static function canGloballySearch(): bool
    {
        // 🔥 Global Search dimatikan supaya tidak 500
        return false;
    }

    public static function getHeaderWidgets(): array
    {
        return [
            ResultOverview::class,
        ];
    }


    //------------end of adrian------------------------------------//

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('tracer_id')
                    ->label('Tracer')
                    ->required(fn($operation) => $operation === 'create')
                    ->options(
                        Tracer::query()
                            ->pluck('domain', 'id')
                            ->toArray()
                    )
                    ->searchable()
                    ->validationMessages([
                        'required' => 'Trace wajib dipilih',
                    ]),

                Forms\Components\TextInput::make('keyword')
                    ->required(fn($operation) => $operation === 'create')
                    ->maxLength(255)
                    ->validationMessages([
                        'required' => 'keyword wajib diisi',
                    ]),
                Forms\Components\TextInput::make('url')
                    ->url()
                    ->required(fn($operation) => $operation === 'create')
                    ->maxLength(255)
                    ->validationMessages([
                        'required' => 'Url wajib diisi',
                    ]),
                Forms\Components\Textarea::make('description')
                    ->required(fn($operation) => $operation === 'create')
                    ->columnSpanFull()
                    ->validationMessages([
                        'required' => 'Deskripsi wajib diisi',
                    ]),
                Forms\Components\TextInput::make('target_account')
                    ->required(fn($operation) => $operation === 'create')
                    ->maxLength(255)
                    ->validationMessages([
                        'required' => 'Target akun wajib diisi',
                    ]),

                // Forms\Components\Select::make('category')
                //     ->label('Kategori Berita')
                //     ->required(fn($operation) => $operation === 'create')
                //     ->options([
                //         'hoax' => 'Hoax',

                //     ])
                //     ->validationMessages([
                //         'required' => 'category wajib dipilih',
                //     ]),

                Forms\Components\Select::make('category')
                    ->label('Kategori Berita')
                    ->options([
                        'hoax' => 'Hoax',
                    ])
                    ->default('hoax')
                    ->disabled()
                    ->dehydrated(),
                FileUpload::make('capture')
                    ->label('📎 Upload gambar jpeg/png')
                    ->image() // validasi image
                    ->disk('public')
                    ->directory('capture')
                    //->visibility('private')
                    ->acceptedFileTypes(['image/jpg', 'image/jpeg', 'image/png'])
                    ->maxSize(2048) // 2MB
                    ->required(fn($operation) => $operation === 'create')
                    ->validationMessages([
                        'required' => 'gambar wajib diisi',
                    ]),

                Forms\Components\DateTimePicker::make('published_at'),

                Forms\Components\Select::make('status')
                    ->label('Status Berita')
                    ->required(fn($operation) => $operation === 'create')
                    ->options([
                        'new' => 'New',
                        'validated' => 'Validated',
                        'unvalidated' => 'Unvalidated',
                    ])

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->poll('1s')
            ->columns([

                // ImageColumn::make('capture')
                //     ->disk('public')
                //     ->label('Gambar')
                //     ->tooltip('Klik untuk memperbesar')
                //     ->extraAttributes(fn($record) => [
                //         'onclick' => "window.open(" . json_encode(Storage::url($record->capture)) . ", '_blank')",
                //         'style' => 'cursor:pointer;',
                //     ]),
                ImageColumn::make('capture')
                    ->disk('public')
                    ->url(fn($record) => Storage::url($record->capture))
                    ->openUrlInNewTab(),



                // Tables\Columns\TextColumn::make('type')
                //     ->disableClick()
                //     ->searchable(['type'])
                //     ->searchable(),
                Tables\Columns\TextColumn::make('keyword')
                    ->disableClick()
                    ->searchable(['keyword']),

                TextColumn::make('url')
                    ->label('URL')
                    ->searchable()
                    ->disableClick()
                    ->formatStateUsing(fn($state) => Str::limit($state, 40))
                    ->tooltip(fn($state) => $state)
                    ->extraAttributes(fn($record) => [
                        'x-data' => '{}',
                        'x-on:click.stop' => "window.open('{$record->url}', '_blank', 'width=800,height=600')",
                        'style' => 'cursor:pointer;',
                    ]),


                Tables\Columns\TextColumn::make('target_account')
                    ->disableClick()
                    ->searchable(['target_account']),

                Tables\Columns\TextColumn::make('domain.name')
                    ->disableClick()
                    ->numeric()
                    ->sortable(['domain.name']),

                Tables\Columns\TextColumn::make('validator.name')
                    ->label('Validator'),

                // Tables\Columns\TextColumn::make('team.name')
                //     ->disableClick()
                //     ->numeric()
                //     ->sortable(),
                Tables\Columns\TextColumn::make('category')
                    ->disableClick()
                    ->sortable(),
                Tables\Columns\TextColumn::make('keterangan')
                    ->disableClick()
                    ->sortable(),

                Tables\Columns\TextColumn::make('validated_at')
                    //->disableClick()
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('published_at')
                    ->disableClick()
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->disableClick(),
                Tables\Columns\TextColumn::make('hits')
                    ->disableClick()
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListResults::route('/'),
            'create' => Pages\CreateResult::route('/create'),
            'edit' => Pages\EditResult::route('/{record}/edit'),

        ];
    }
}
