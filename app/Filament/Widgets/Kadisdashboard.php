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

class Kadisdashboard extends BaseWidget
{
    protected static ?string $heading = 'List Butuh validasi';
    protected static ?int $sort = 4;

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
            Result::query()->whereStatus('unvalidated')
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
                    
                    ->description(fn($record) => 'Publikasi pada : ' . $record->published_at->format('d F Y')),
                ToggleColumn::make('status')
                    ->label('Status Valid')
                    ->onColor('success')
                    ->visible(fn()=>in_array(auth()->user()->role,['kadis','validator']))
                    ->offColor('danger')

                    // DB string → toggle boolean
                    ->getStateUsing(fn($record) => $record?->status === 'validated')

                    // ⛔ STOP update default (boolean)
                    ->updateStateUsing(function ($record, bool $state) {
                        $record->update([
                            'status' => $state ? 'validated' : 'unvalidated',
                        ]);
                    })

            ])
            ->actions([
                Action::make('lihat_dulu')
                    ->url(fn($record)=>$record->url)
            ])
            ->paginated(false); // dashboard biasanya tanpa pagination
    }
}
