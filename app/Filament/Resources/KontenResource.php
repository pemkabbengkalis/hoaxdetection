<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Konten;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Forms\Components\TextInput;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\KontenResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\KontenResource\RelationManagers;
use Filament\Forms\Components\Textarea;


class KontenResource extends Resource
{
    protected static ?string $model = Konten::class;

    protected static ?string $navigationIcon = 'heroicon-s-rectangle-stack';
    protected static ?string $pluralModelLabel = 'Konten Konfirmasi';
    protected static ?int $navigationSort = 3;



    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Forms\Components\TextInput::make('url')
                    ->label('Mohon Isi URL')
                    // ->required(fn($operation) => $operation === 'create')//hanya wajib di isi saat tambah data baru
                    //->disabled(fn(string $operation) => $operation === 'edit')//tidak bisa edit saat sesi edit
                    ->placeholder('https://google.com')
                    ->maxLength(255)
                    //->required()
                    ->rules(['required', 'url'])
                    ->validationMessages([
                        'required' => 'Mohon isi URL yang valid',
                        'url' => 'Format URL tidak valid',
                    ]), //

                Forms\Components\Textarea::make('keterangan')
                    ->label('Keterangan')
                    //->required(fn($operation) => $operation === 'create')
                    ->required()
                    ->validationMessages([
                        'required' => 'Mohon jelaskan data yang dibutuhkan',
                    ]),
                //->disabled(fn(string $operation) => $operation === 'edit')
                //->columnSpanFull(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('url')
                    ->disableClick()
                    ->searchable(['url']),
                Tables\Columns\TextColumn::make('keterangan')
                    ->disableClick()
                    ->searchable(['keterangan']),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListKontens::route('/'),
            'create' => Pages\CreateKonten::route('/create'),
            'edit' => Pages\EditKonten::route('/{record}/edit'),
        ];
    }
}
