<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use App\Models\Result;
use Filament\Tables\Table;
use Illuminate\Support\Collection;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables\Actions\BulkAction;

use function Symfony\Component\String\s;

class KadisUnvalidated extends BaseWidget
{
    protected static ?string $heading = 'Hoax';
    protected static ?int $sort = 5;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->poll('1s')
            ->query(
                // Result::query()->whereStatus('validated')->Where('category', 'hoax'),

                $results = Result::query()
                    ->where(function ($query) {
                        $query->where('status', 'validated')
                            ->where('category', 'hoax')
                            ->orWhere(function ($q) {
                                $q->where('status', 'fakta')
                                    ->where('category', 'fakta');
                            });
                    })


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
                    ->limit(40) // jumlah karakter
                    ->tooltip(fn($record) => $record->url) // biar full URL muncul saat hover
                    ->description(
                        fn($record) =>
                        'Publikasi pada : ' . $record->published_at->format('d F Y')
                    ),
                TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->searchable(),

                // ->description(fn($record) => 'Publikasi pada : ' . $record->published_at->format('d F Y')),


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
            ->paginated(false); // dashboard biasanya tanpa pagination
    }
}
