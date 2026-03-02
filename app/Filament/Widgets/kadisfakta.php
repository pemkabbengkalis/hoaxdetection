<?php

namespace App\Filament\Widgets;

use App\Models\Result;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables\Actions\Action;

class KadisFakta extends BaseWidget
{
    protected static ?string $heading = 'Fakta';
    protected static ?int $sort = 6;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->poll('1s')
            ->query(
                Result::query()
                    // ->where('status', 'fakta')
                    ->where('category', 'fakta')
            )
            ->columns([
                TextColumn::make('tracer.name')
                    ->label('Penelusuran dari')
                    ->description(fn($record) => $record->tracer->domain)
                    ->searchable(),

                TextColumn::make('keyword')
                    ->label('Kata kunci')
                    ->searchable(),

                TextColumn::make('url')
                    ->label('URL didapatkan')
                    ->limit(40)
                    ->tooltip(fn($record) => $record->url)
                    ->description(
                        fn($record) =>
                        'Publikasi pada : ' . $record->published_at->format('d F Y')
                    ),

                TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->searchable(),
            ])
            ->actions([
                Action::make('lihat_berita')
                    ->label('Lihat Berita')
                    ->icon('heroicon-o-eye')
                    ->url(fn($record) => $record->url)
                    ->openUrlInNewTab()
                    ->button()
                    ->outlined()        // style outline
                    ->color('primary')
            ])
            ->paginated(false);
    }
}
