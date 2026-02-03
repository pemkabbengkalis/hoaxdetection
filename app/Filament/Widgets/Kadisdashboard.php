<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use App\Models\Result;
use Filament\Tables\Table;
use Illuminate\Support\Collection;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\CheckboxColumn;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Str;


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
                    ->description(fn($record) => $record->tracer->domain)
                    ->searchable(),
                TextColumn::make('keyword')
                    ->label('Kata kunci')
                    ->searchable(),
                // TextColumn::make('url')
                //     ->label('URL didapatkan')
                //     ->description(fn($record) => 'Publikasi pada : ' . $record->published_at->format('d F Y')),

                TextColumn::make('url')
                    ->label('URL didapatkan')
                    ->limit(40) // jumlah karakter
                    ->tooltip(fn($record) => $record->url) // biar full URL muncul saat hover
                    ->description(
                        fn($record) =>
                        'Publikasi pada : ' . $record->published_at->format('d F Y')
                    ),
                SelectColumn::make('category')
                    ->options([

                        'hoax' => 'Hoax',
                        'fakta' => 'Fakta'
                    ])
                    ->hidden(fn() => in_array(auth()->user()->role, ['admin', 'team', 'validator'])),

                CheckboxColumn::make('status')
                    ->label('Status')
                    ->tooltip('Pastikan pilihan sudah sesuai')
                    ->visible(fn() => in_array(auth()->user()->role, ['kadis', 'validator']))
                    ->hidden(fn() => in_array(auth()->user()->role, ['admin', 'team', 'validator']))


                    // DB string → toggle boolean
                    ->getStateUsing(fn($record) => $record?->status === 'validated')

                    // ⛔ STOP update default (boolean)
                    ->updateStateUsing(function ($record, bool $state) {
                        $record->update([
                            'status' => $state ? 'validated' : 'unvalidated',
                            'validator_id' => auth()->user()->id,
                            'validated_at' => now(),
                        ]);
                    }),

            ])
            ->actions([
                Action::make('lihat_dulu')
                    ->icon('heroicon-o-eye')
                    ->url(fn($record) => $record->url)
                    ->hidden(fn() => in_array(auth()->user()->role, ['admin', 'team', 'validator'])),


            ])





            ->paginated(false); // dashboard biasanya tanpa pagination
    }
}
