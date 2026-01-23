<?php

namespace App\Filament\Widgets;

use App\Models\Result;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Collection;

class Kadisdashboard extends BaseWidget
{
    protected static ?string $heading = 'List Hoax Terbaru';
    protected static ?int $sort = 1;

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
            Result::query()
            )
            ->columns([
                Tables\Columns\TextColumn::make('url')
                    ->label('NIP')
                    ->searchable(),

                
            ])
            ->paginated(false); // dashboard biasanya tanpa pagination
    }
}
