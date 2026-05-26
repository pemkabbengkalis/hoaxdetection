<?php

namespace App\Filament\Pages\Auth;

use Filament\Pages\Auth\Login as BaseLogin;
use Filament\Forms\Form;
use Filament\Pages\Auth\Login as BaseAuth;
use Coderflex\FilamentTurnstile\Forms\Components\Turnstile;
use Coderflex\FilamentTurnstile\Forms\Components\TurnstileField;
use Filament\Forms\Components\ViewField;


class Login extends BaseLogin
{
    public function getHeading(): string
    {
        return 'Selamat Datang';
    }

    public function getSubheading(): ?string
    {
        return 'Silakan login untuk melanjutkan';
    }

    public function getAuthenticateFormActionLabel(): string
    {
        return 'Masuk';
    }

    public function form(Form $form): Form
    {
        return parent::form($form)
            ->schema([
                ...parent::form($form)->getComponents(),

                Turnstile::make('captcha')
                    ->theme('light')
                    ->language('id'),
            ]);
    }
}
