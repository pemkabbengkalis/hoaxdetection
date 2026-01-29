<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use App\Models\Result;
use Filament\Tables\Table;
use Illuminate\Support\Collection;
use Filament\Tables\Columns\TextColumn;
use Filament\Widgets\TableWidget as BaseWidget;

class Kadisdashboard extends BaseWidget
{
    protected static ?string $heading = 'List Hoax Terbaru';
    protected static ?int $sort = 4;

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
            Result::query()
            )
            ->columns([
                TextColumn::make('tracer.name')
                    ->label('Penelusuran dari')
                    ->description(fn($record)=>$record->tracer->domain)
                    ->searchable(),
                TextColumn::make('keyword')
                    ->label('Kata kunci')
                    ->searchable(),
                TextColumn::make('url')
                    ->label('URL didapatkan')
                    ->url(fn($record) => '#')
                    ->extraAttributes(fn($record) => [
                        'onclick' => "window.open(
            '{$record->url}',
            'popup',
            'width=900,height=600,scrollbars=yes'
        ); return false;"
                    ])
                    ->description(fn($record) => 'Publikasi pada : ' . $record->published_at->format('d F Y')),



            ])
            ->paginated(false); // dashboard biasanya tanpa pagination
    }
}
