<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Domain;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use DomainResource\Widgets\DomainOverview;
use App\Filament\Resources\DomainResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\DomainResource\RelationManagers;
use App\Filament\Resources\DomainResource\Widgets\DomaiOverview;
use Filament\Facades\Filament;
use App\Models\User;
use Filament\Tables\Actions\Action;
use Illuminate\Support\HtmlString;


class DomainResource extends Resource
{
    protected static ?string $model = Domain::class;
    protected static ?string $navigationIcon = 'heroicon-s-rectangle-group';
    protected static ?string $modelLabel = 'Domain';
    protected static ?string $pluralModelLabel = 'Domain';
    protected static ?int $navigationSort = 3;
    //protected static ?string $navigationGroup = 'Validator';

    protected static function getTableQuery(): Builder
    {
        return parent::getTableQuery()->with('results.tracer');
    }

    public static function getWidgets(): array
    {
        return [
            DomaiOverview::class,
        ];
    }

    public static function canViewAny(): bool
    {
        $user = Filament::auth()->user();

        // ⛔ Kalau belum login / bukan user valid
        if (! $user instanceof User) {
            return false;
        }

        // 🔐 Batasi berdasarkan role
        return $user->hasAnyRole([
            User::ROLE_ADMIN,
            // User::ROLE_VALIDATOR,
            User::ROLE_TEAM,
        ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->poll('1s')
            ->columns([

                Tables\Columns\TextColumn::make('tracer_domain')
                    ->label('Domain')
                    ->disableClick()
                    ->getStateUsing(
                        fn($record) =>
                        $record->results
                            ->first()?->tracer?->domain
                    )
                    ->searchable(),

                Tables\Columns\TextColumn::make('results.url')
                    ->label('Url')
                    ->disableClick()
                    ->getStateUsing(
                        fn($record) =>
                        $record->results->first()?->url
                    )
                    ->tooltip(fn($state) => $state)
                    ->extraAttributes(fn($record) => [
                        'x-data' => '{}',
                        'x-on:click.stop' => "window.open('{$record->url}', '_blank', 'width=800,height=600')",
                        'style' => 'cursor:pointer;',
                    ])
                    ->searchable(),
                Tables\Columns\TextColumn::make('results.keywords')
                    ->label('Keyword')
                    ->disableClick()
                    //->getStateUsing(fn($record) => $record->results->pluck('keyword')->all()),
                    ->getStateUsing(
                        fn($record) =>
                        $record->results->first()?->keyword
                    )
                    ->searchable(),

                Tables\Columns\TextColumn::make('results.target_account')
                    ->label('Target Akun')
                    ->disableClick()
                    ->getStateUsing(
                        fn($record) =>
                        $record->results->first()?->target_account
                    )
                    ->searchable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->disableClick()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->disableClick()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->disableClick()
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
                Action::make('detail')
                    ->label('Detail')
                    ->icon('heroicon-s-eye')
                    ->modalHeading('')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('')

                    ->modalContent(function ($record) {

                        if ($record->results->isEmpty()) {
                            return new HtmlString('Tidak ada data result');
                        }

                        $rows = '';

                        foreach ($record->results as $r) {

                            $rows .= "
                    <tr class='border-b'>
                        <td class='p-2'>" . ($r->tracer?->domain ?? '-') . "</td>
                        <td class='p-2 break-all'>{$r->url}</td>
                        <td class='p-2'>{$r->keyword}</td>
                        <td class='p-2'>{$r->target_account}</td>
                    </tr>
                ";
                        }

                        return new HtmlString("
                <table class='w-full text-sm border rounded'>
                    <thead class='bg-gray-100'>
                        <tr>
                            <th class='p-2 text-left'>Domain</th>
                            <th class='p-2 text-left'>URL</th>
                            <th class='p-2 text-left'>Keyword</th>
                            <th class='p-2 text-left'>Target Akun</th>
                        </tr>
                    </thead>
                    <tbody>
                        {$rows}
                    </tbody>
                </table>
            ");
                    }),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListDomains::route('/'),
            'create' => Pages\CreateDomain::route('/create'),
            'edit' => Pages\EditDomain::route('/{record}/edit'),
        ];
    }
}
