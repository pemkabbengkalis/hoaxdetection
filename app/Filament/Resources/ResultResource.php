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




class ResultResource extends Resource
{

    public static string $resource = ResultResource::class;
    protected static ?string $model = Result::class;
    protected static ?string $navigationIcon = 'heroicon-s-rectangle-stack';
    protected static ?int $navigationSort = 3;

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
            User::ROLE_KADIS,
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
                    ->searchable(),

                Forms\Components\TextInput::make('keyword')
                    ->required(fn($operation) => $operation === 'create')
                    ->maxLength(255),
                Forms\Components\TextInput::make('url')
                    ->url()
                    ->required(fn($operation) => $operation === 'create')
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->required(fn($operation) => $operation === 'create')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('target_account')
                    ->required(fn($operation) => $operation === 'create')
                    ->maxLength(255),

                FileUpload::make('capture')
                    ->label('📎 Upload gambar jpeg/png')
                    ->image() // validasi image
                    ->disk('public')
                    ->directory('capture')
                    //->visibility('private')
                    ->acceptedFileTypes(['image/jpeg', 'image/png'])
                    ->maxSize(2048) // 2MB
                    ->required(fn($operation) => $operation === 'create'),

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
            ->columns([

                ImageColumn::make('capture')
                    ->disk('public') // storage/app/public
                    ->label('Gambar')
                    ->tooltip('Klik untuk memperbesar')
                    ->disableClick()
                    ->extraAttributes(fn($record) => [
                        'onclick' => "window.open(" . json_encode(Storage::url('/' . $record->capture)) . ",'_blank','width=800,height=600')",
                        'style' => 'cursor:pointer;'
                    ]),

                Tables\Columns\TextColumn::make('type')
                    ->disableClick()
                    ->searchable(['type'])
                    ->searchable(),
                Tables\Columns\TextColumn::make('keyword')
                    ->disableClick()
                    ->searchable(['keyword']),

                TextColumn::make('url')
                    ->label('URL') // Label kolom
                    ->searchable()
                    ->disableClick()
                    ->formatStateUsing(fn($state) => Str::limit($state, 40)) // tampil singkat
                    ->tooltip(fn($state) => $state) // tampil full URL saat hover
                    ->extraAttributes(fn($record) => [
                        'x-data' => '{}',
                        'x-on:click.stop' => "window.open('{$record->url}', '_blank', 'width=800,height=600')",
                        'style' => 'cursor:pointer; color:blue; text-decoration:underline;',
                    ]),


                Tables\Columns\TextColumn::make('target_account')
                    ->disableClick()
                    ->searchable(['target_account']),

                Tables\Columns\TextColumn::make('domain.name')
                    ->disableClick()
                    ->numeric()
                    ->sortable(['domain.name']),
                Tables\Columns\TextColumn::make('validator.name')
                    ->disableClick()
                    ->numeric()
                    ->sortable(),
                // Tables\Columns\TextColumn::make('team.name')
                // ->disableClick()
                //     ->numeric()
                //     ->sortable(),
                // Tables\Columns\TextColumn::make('validated_at')
                //     //->disableClick()
                //     ->dateTime()
                //     ->sortable(),
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
