<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Tracer;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\TracerResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\TracerResource\RelationManagers;

class TracerResource extends Resource
{
    protected static ?string $model = Tracer::class;


    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $modelLabel = 'Room Tracer';
    protected static ?string $pluralModelLabel = 'Room Tracer';
    protected static ?string $navigationGroup = 'Validator';

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
                  ->unique( 'tracers',  'name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('domain')
                  ->unique( 'tracers',  'domain')

                    ->required()
                    ->maxLength(255),
                Select::make('type_platform')
                    ->options([
                        'media_sosial'=> 'Media Sosial',
                        'search_engine' => 'Search Engine',
                    ])->required(),
                // Forms\Components\TextInput::make('hits')
                //     ->required()
                //     ->numeric()
                //     ->default(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->description(fn($record)=>$record->domain)
                    ->searchable(),
                Tables\Columns\TextColumn::make('type_platform')->state(
                    fn($record)=> str($record->type_platform)->headline()
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
