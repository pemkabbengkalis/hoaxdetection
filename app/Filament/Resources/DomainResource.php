<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Domain;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use DomainResource\Widgets\DomainOverview;
use App\Filament\Resources\DomainResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\DomainResource\RelationManagers;
use App\Filament\Resources\DomainResource\Widgets\DomaiOverview;
use Filament\Facades\Filament;
use App\Models\User;


class DomainResource extends Resource
{
    protected static ?string $model = Domain::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-group';
    protected static ?string $modelLabel = 'Domain';
    protected static ?string $pluralModelLabel = 'Domain';
    //protected static ?string $navigationGroup = 'Validator';

    //--------------adrian--------------------//    
    public static function getWidgets(): array
    {
        return [
            DomaiOverview::class,
        ];
    }


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nama Domain')
                    ->required(fn($operation) => $operation === 'create')
                    //->unique(ignoreRecord: true)
                    ->maxLength(255),
                Forms\Components\TextInput::make('extension')
                    ->label('Ekstensi Domain, Ex (.com, .id)')
                    ->required(fn($operation) => $operation === 'create')
                    //->unique(ignoreRecord: true)
                    ->maxLength(255),
                Forms\Components\TextInput::make('description')
                    ->label('Deskripsi')
                    ->required(fn($operation) => $operation === 'create')
                    //->unique(ignoreRecord: true)
                    ->maxLength(255),
                Forms\Components\Select::make('type')
                    ->label('Sumber Platform')
                    ->required(fn($operation) => $operation === 'create')
                    //->unique(ignoreRecord: true)
                    ->options([
                        'media_online' => 'Media Online',
                        'media_sosial' => 'Media Sosial',
                    ]),


            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('extension')
                    ->searchable(),
                Tables\Columns\TextColumn::make('description')
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->formatStateUsing(fn($state) => match ($state) {
                        'media_online' => 'Media Online',
                        'media_sosial' => 'Media Sosial',
                        default => '-',
                    })
                    ->searchable(),
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
            'index' => Pages\ListDomains::route('/'),
            'create' => Pages\CreateDomain::route('/create'),
            'edit' => Pages\EditDomain::route('/{record}/edit'),
        ];
    }
}
