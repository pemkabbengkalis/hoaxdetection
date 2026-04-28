<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\News;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\NewsResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\NewsResource\RelationManagers;
use App\Filament\Resources\NewsResource\Widgets\StatistikNews;
use Carbon\Carbon;


class NewsResource extends Resource
{
    protected static ?string $model = News::class;

    protected static ?string $navigationIcon = 'heroicon-s-globe-alt';
    protected static ?string $modelLabel = 'Tracer';
    protected static ?string $pluralModelLabel = 'Tracer';

    public static function getWidgets(): array
    {
        return [
            StatistikNews::class,
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('keyword.term')
                    ->label('Keyword')
                    ->toggleable()
                    ->badge(),
                TextColumn::make('title')
                    ->label('Judul Berita')
                    ->searchable()
                    ->toggleable()
                    ->wrap(),
                //->limit(50),
                // TextColumn::make('source')
                //     ->wrap()
                //     ->label('Sumber'),
                TextColumn::make('published_at')
                    ->label('Tanggal Tayang')
                    ->toggleable()
                    ->formatStateUsing(
                        fn($state) =>
                        $state
                            ? Carbon::parse($state)
                            ->timezone('Asia/Jakarta')
                            ->format('d M Y H:i')
                            : '-'
                    )
                    ->sortable(),
                TextColumn::make('url')
                    ->label('Baca (Sumber)')
                    ->toggleable()
                    ->wrap()
                    ->formatStateUsing(fn($state) => wordwrap($state, 50, "\n", true))
                    ->url(fn(News $record) => $record->url)
                    ->openUrlInNewTab()
                    ->icon('heroicon-o-link'),
            ])
            ->defaultSort('published_at', 'desc')
            ->filters([
                SelectFilter::make('keyword_id')
                    ->relationship('keyword', 'term'),

            ])
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\EditAction::make()
                //     ->button()
                //     ->visible(fn() => auth()->user()?->role !== 'kadis')
                //     ->extraAttributes([
                //         'style' => 'background-color: #facc15; color: black;'
                //     ]),
                Tables\Actions\DeleteAction::make()
                    ->button()
                    ->label('Hapus')
                    ->visible(fn() => auth()->user()?->role !== 'kadis')
                    ->extraAttributes([
                        'style' => 'background-color: #dc2626; color: white;'
                    ]),
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
            'index' => Pages\ListNews::route('/'),
            'create' => Pages\CreateNews::route('/create'),
            'edit' => Pages\EditNews::route('/{record}/edit'),
        ];
    }
}
