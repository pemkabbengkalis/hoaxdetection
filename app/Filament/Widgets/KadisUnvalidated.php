<?php

namespace App\Filament\Widgets;

use App\Models\Result;
use Filament\Forms;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Notifications\Notification;

class KadisUnvalidated extends BaseWidget
{
    protected static ?string $heading = 'Verifikasi Hoax';

    protected static ?int $sort = 5;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->poll('1s')
            ->query(
                Result::query()
                    ->where('status', 'validated')
                    ->where('category', 'hoax')
                    ->latest()
            )
            ->columns([
                TextColumn::make('keyword')
                    ->label('Keyword')
                    ->searchable()
                    ->weight('bold'),

                TextColumn::make('url')
                    ->label('URL Didapatkan')
                    ->limit(50)
                    ->tooltip(fn(Result $record) => $record->url)
                    ->description(
                        fn(Result $record) =>
                        $record->published_at
                            ? 'Publikasi: ' . $record->published_at->format('d F Y')
                            : null
                    ),

                TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->limit(100)
                    ->tooltip(fn(Result $record) => $record->keterangan)
                    ->wrap(),

                TextColumn::make('category')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn(?string $state) => strtoupper($state ?? '-'))
                    ->color(fn(?string $state) => match ($state) {
                        'fakta' => 'success',
                        'hoax' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->actions([

                Action::make('edit_keterangan')
                    ->label('Edit Keterangan')
                    ->icon('heroicon-o-pencil-square')
                    ->color('warning')
                    ->visible(fn(): bool => in_array(
                        auth()->user()?->role,
                        ['admin', 'kadis']
                    ))
                    ->fillForm(fn(Result $record): array => [
                        'keterangan' => $record->keterangan,
                    ])
                    ->form([
                        Forms\Components\Textarea::make('keterangan')
                            ->label('Keterangan')
                            ->rows(5)
                            ->required()
                            ->maxLength(1000),
                    ])
                    ->action(function (Result $record, array $data): void {

                        $record->update([
                            'keterangan' => $data['keterangan'],
                        ]);

                        Notification::make()
                            ->title('Berhasil')
                            ->body('Keterangan berhasil diperbarui.')
                            ->success()
                            ->send();
                    }),

                Action::make('jadikan_fakta')
                    ->label('Jadikan Fakta')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn(): bool => in_array(
                        auth()->user()?->role,
                        ['admin', 'kadis']
                    ))
                    ->requiresConfirmation()
                    ->modalHeading('Verifikasi Fakta')
                    ->modalDescription('Apakah Anda yakin berita ini termasuk FAKTA?')
                    ->action(function (Result $record): void {

                        $record->update([
                            'status'   => 'validated',
                            'category' => 'fakta',
                        ]);

                        Notification::make()
                            ->title('Berhasil')
                            ->body('Berita berhasil dipindahkan ke kategori Fakta.')
                            ->success()
                            ->send();
                    }),

                Action::make('lihat_berita')
                    ->label('Lihat Berita')
                    ->icon('heroicon-o-eye')
                    ->url(fn(Result $record) => $record->url)
                    ->openUrlInNewTab()
                    ->button()
                    ->outlined()
                    ->color('primary'),
            ])
            ->paginated(false);
    }
}
