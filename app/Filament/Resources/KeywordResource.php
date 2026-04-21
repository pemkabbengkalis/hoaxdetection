<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Keyword;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ToggleColumn;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\KeywordResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\KeywordResource\RelationManagers;

class KeywordResource extends Resource
{
    protected static ?string $model = Keyword::class;

    protected static ?string $navigationIcon = 'heroicon-s-rectangle-stack';
     protected static ?string $modelLabel = 'Keyword';
    protected static ?string $pluralModelLabel = 'Keyword';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('term')
                    ->label('Keyword / Frase Pencarian')
                    ->required()
                    ->placeholder('bupati bengkalis'),
                Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('term')->searchable()->sortable(),
                ToggleColumn::make('is_active'),
                TextColumn::make('news_count')
                    ->counts('news')
                    ->label('Jumlah Berita'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListKeywords::route('/'),
            'create' => Pages\CreateKeyword::route('/create'),
            'edit' => Pages\EditKeyword::route('/{record}/edit'),
        ];
    }
}
