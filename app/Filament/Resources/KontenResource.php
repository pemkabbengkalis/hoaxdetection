<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Konten;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Forms\Components\TextInput;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\KontenResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\KontenResource\RelationManagers;
use Filament\Forms\Components\Textarea;
use Illuminate\Support\Facades\Auth;

class KontenResource extends Resource
{
    protected static ?string $model = Konten::class;

    protected static ?string $navigationIcon = 'heroicon-s-rectangle-stack';
    protected static ?string $pluralModelLabel = 'Konten Konfirmasi';
    protected static ?int $navigationSort = 3;
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = Auth::id();

        return $data;
    }

    //---------------------------badge notifikasi menu-----------------

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::whereNull('konfirmasi')->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    public static function getNavigationBadgePollingInterval(): ?string
    {
        return '1s';
    }

    //---------------------------badge notifikasi menu hanya divalidator-----------------
    // public static function getNavigationBadge(): ?string
    // {
    //     if (auth()->user()->role !== 'validator') {
    //         return null;
    //     }

    //     return static::getModel()::whereNull('konfirmasi')->count();
    // }

    // public static function getNavigationBadgeColor(): ?string
    // {
    //     if (auth()->user()->role !== 'validator') {
    //         return null;
    //     }

    //     return 'danger';
    // }


    // public static function getNavigationBadgePollingInterval(): ?string
    // {
    //     return '1s';
    // }
    //------------end of badge notifikasi menu divalidator----------------------------

    //---------------------------end of badge notifikasi menu-----------------


//----------------------halaman ini hanya bisa diakses oleh admin dan kadis-----------
    public static function canAccess(): bool
    {
        return in_array(auth()->user()->role, ['admin', 'kadis', 'validator']);
    }
//-------------------------------end of access control---------------------------------

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Forms\Components\TextInput::make('url')
                    ->label('Mohon Isi URL')
                    // ->required(fn($operation) => $operation === 'create')//hanya wajib di isi saat tambah data baru
                    //->disabled(fn(string $operation) => $operation === 'edit')//tidak bisa edit saat sesi edit
                    ->placeholder('https://google.com')
                    ->maxLength(255)
                    ->required()
                    ->rules(['required', 'url'])
                    ->validationMessages([
                        'required' => 'Mohon isi URL yang valid',
                        'url' => 'Format URL tidak valid',
                    ]), //

                Forms\Components\Textarea::make('keterangan')
                    ->label('Keterangan')
                    ->maxLength(255)
                    //->required(fn($operation) => $operation === 'create')
                    ->required()
                    ->validationMessages([
                        'required' => 'Mohon jelaskan data yang dibutuhkan',
                    ]),
                Forms\Components\Textarea::make('konfirmasi')
                    ->label('Konfirmasi Pencarian')
                    ->maxLength(255)
                    //->required(fn($operation) => $operation === 'create'
                    //->required()
                    ->hidden(fn($operation) => $operation === 'create')
                    // ->formatStateUsing(fn($state) => wordwrap($state, 40, "\n", true))
                    ->validationMessages([
                        'required' => 'Mohon jelaskan data yang dibutuhkan',
                    ]),
                //->disabled(fn(string $operation) => $operation === 'edit')
                //->columnSpanFull(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.role')
                    ->label('Pengirim')
                    ->disableClick()
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('url')
                    ->disableClick()
                    ->wrap()
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('keterangan')
                    ->disableClick()
                    ->wrap()
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('konfirmasi')
                    ->label('Konfrimasi Pencarian')
                    ->disableClick()
                    ->wrap()
                    ->searchable()
                    ->toggleable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->button()
                    // ->visible(fn () => auth()->user()->role !== 'validator') // Hanya tampilkan tombol edit jika bukan validator,
                    ->extraAttributes([
                        'style' => 'background-color: #facc15; color: black;'
                    ]),
                Tables\Actions\DeleteAction::make()
                    ->button()
                    // ->visible(fn () => auth()->user()->role !== 'validator') // Hanya tampilkan tombol delete jika bukan validator,
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
            'index' => Pages\ListKontens::route('/'),
            'create' => Pages\CreateKonten::route('/create'),
            'edit' => Pages\EditKonten::route('/{record}/edit'),
        ];
    }
}
