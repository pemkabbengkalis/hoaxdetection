<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ActivityResource\Pages;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\KeyValue;
use Spatie\Activitylog\Models\Activity;

class ActivityResource extends Resource
{
    protected static ?string $model = Activity::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';
    protected static ?string $navigationGroup = 'Settings';
    protected static ?string $navigationLabel = 'Log Aktifitas';
    protected static ?string $pluralModelLabel = 'Log Aktifitas';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('log_name')
                    ->label('Log Name')
                    ->disabled(),
                TextInput::make('description')
                    ->label('Description')
                    ->disabled(),
                TextInput::make('event')
                    ->label('Event')
                    ->disabled(),
                TextInput::make('subject_type')
                    ->label('Subject Type')
                    ->disabled(),
                TextInput::make('causer_type')
                    ->label('Causer Type')
                    ->disabled(),
                KeyValue::make('properties')
                    ->label('Properties')
                    ->disabled()
                    ->columnSpanFull(),
                DateTimePicker::make('created_at')
                    ->label('Date')
                    ->disabled(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->sortable(),
                Tables\Columns\TextColumn::make('log_name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('description')
                    ->searchable(),
                Tables\Columns\TextColumn::make('event')
                    ->searchable(),
                Tables\Columns\TextColumn::make('subject_type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('causer.name')
                    ->label('User')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Waktu')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                //
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListActivities::route('/'),
        ];
    }
    
    public static function canCreate(): bool
    {
        return false;
    }
}
