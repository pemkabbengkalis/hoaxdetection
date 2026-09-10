<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ManualBookResource\Pages;
use App\Filament\Resources\ManualBookResource\RelationManagers;
use App\Models\ManualBook;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use Illuminate\Database\Eloquent\Model;

class ManualBookResource extends Resource
{
    protected static ?string $model = ManualBook::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function canCreate(): bool
    {
        return auth()->user()?->role === \App\Models\User::ROLE_ADMIN;
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->user()?->role === \App\Models\User::ROLE_ADMIN;
    }

    public static function canDelete(Model $record): bool
    {
        return auth()->user()?->role === \App\Models\User::ROLE_ADMIN;
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()?->role === \App\Models\User::ROLE_ADMIN;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->label('Judul')
                    ->required()
                    ->maxLength(255),
                Forms\Components\FileUpload::make('file_path')
                    ->label('File PDF')
                    ->required()
                    ->acceptedFileTypes(['application/pdf'])
                    ->directory('manual-books')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Judul')
                    ->searchable(),
                Tables\Columns\TextColumn::make('file_path')
                    ->label('File')
                    ->formatStateUsing(fn ($state) => 'Download PDF')
                    ->url(fn ($record) => asset('storage/' . $record->file_path))
                    ->openUrlInNewTab(),
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
                Tables\Actions\Action::make('download')
                    ->label('Download PDF')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn (ManualBook $record) => asset('storage/' . $record->file_path))
                    ->openUrlInNewTab(),
                Tables\Actions\EditAction::make()
                    ->visible(fn () => auth()->user()?->role === \App\Models\User::ROLE_ADMIN),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn () => auth()->user()?->role === \App\Models\User::ROLE_ADMIN),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()?->role === \App\Models\User::ROLE_ADMIN),
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
            'index' => Pages\ListManualBooks::route('/'),
            'create' => Pages\CreateManualBook::route('/create'),
            'edit' => Pages\EditManualBook::route('/{record}/edit'),
        ];
    }
}
