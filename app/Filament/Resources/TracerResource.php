<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Tracer;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\ImageColumn;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\TracerResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\TracerResource\RelationManagers;
use Filament\Facades\Filament;
use App\Models\User;


class TracerResource extends Resource
{
    protected static ?string $model = Tracer::class;
    public static function canViewAny(): bool
    {
        /** @var User|null $user */
        $user = Filament::auth()->user();

        return $user?->hasAnyRole([
            User::ROLE_ADMIN,
            User::ROLE_TEAM,
            // User::ROLE_KADIS,
            User::ROLE_VALIDATOR,
        ]) ?? false;
    }

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $modelLabel = 'Room Tracer';
    protected static ?string $pluralModelLabel = 'Room Tracer';
    // protected static ?string $navigationGroup = 'Validator';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withCount('traceds');
    }


    public static function form(Form $form): Form
    {


        return $form
            ->schema([

                Forms\Components\TextInput::make('name')
                    ->label('Nama Platform')
                    ->required(fn($operation) => $operation === 'create')
                    //->disabled(fn(string $operation) => $operation === 'edit')
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),

                Forms\Components\TextInput::make('domain')
                    ->label('Nama Domain')
                    ->required(fn($operation) => $operation === 'create')
                    //->disabled(fn(string $operation) => $operation === 'edit')
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),

                Forms\Components\Select::make('type_platform')
                    ->label('Sumber Platform')
                    ->required(fn($operation) => $operation === 'create')
                    //->unique(ignoreRecord: true)
                    ->options([
                        'media_sosial' => 'Media Sosial',
                        'search_engine' => 'Search Engine',
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table

            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->description(fn($record) => $record->domain)
                    ->label('Nama Platform')
                    ->searchable(),
                Tables\Columns\TextColumn::make('type_platform')->state(
                    fn($record) => str($record->type_platform)->headline()
                ),
                Tables\Columns\TextColumn::make('traceds_count')->label('Jumlah'),
                Tables\Columns\TextColumn::make('hits')
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
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->label('Lihat Data')->url('results?tracer_id=1'),
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
            'index' => Pages\ListTracers::route('/'),
            'create' => Pages\CreateTracer::route('/create'),
            'edit' => Pages\EditTracer::route('/{record}/edit'),
        ];
    }
}
