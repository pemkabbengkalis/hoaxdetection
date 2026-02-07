<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\UserResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Filament\Resources\UserResource\Widgets\UserOverview;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?int $navigationSort = 4;

    public static function getWidgets(): array
    {
        return [
            UserOverview::class,
        ];
    }


    public static function canViewAny(): bool
    {
        /** @var User|null $user */
        $user = Filament::auth()->user();

        return $user?->hasAnyRole([
            User::ROLE_ADMIN,
            // User::ROLE_TEAM,
            // User::ROLE_KADIS,
            //User::ROLE_VALIDATOR,
        ]) ?? false;
    }



    protected static ?string $navigationIcon = 'heroicon-s-user-group';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    // ->visible(fn($operation) => in_array($operation, ['edit']))
                    ->required(fn($operation) => $operation === 'create')
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required(fn($operation) => $operation === 'create')
                    ->maxLength(255),
                Forms\Components\DateTimePicker::make('email_verified_at')
                    ->required(fn($operation) => $operation === 'create'),

                // Forms\Components\TextInput::make('password')
                //     ->password()
                //     ->required(fn($operation) => $operation === 'create')
                //     ->maxLength(255),
                Forms\Components\TextInput::make('password')
                    ->label('Password')
                    ->password()
                    ->required(fn($operation) => $operation === 'create') // wajib hanya create
                    ->dehydrated(fn($state) => filled($state))             // simpan hanya kalau diisi
                    ->dehydrateStateUsing(fn($state) => filled($state) ? bcrypt($state) : null)
                    ->helperText('Kosongkan jika tidak ingin mengubah password'),

                Forms\Components\Select::make('role')
                    ->required(fn($operation) => $operation === 'create')

                    ->options([
                        User::ROLE_ADMIN     => 'Admin',
                        User::ROLE_KADIS     => 'Kadis',
                        User::ROLE_TEAM      => 'Team',
                        User::ROLE_VALIDATOR => 'Validator',
                    ])
                    ->default(User::ROLE_TEAM),


            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->poll('1s')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email_verified_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('role'),
            ])
            ->filters([
                //
            ])
            ->actions([
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
