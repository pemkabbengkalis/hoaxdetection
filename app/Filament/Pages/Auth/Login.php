<?php

namespace App\Filament\Pages\Auth;

use Filament\Pages\Auth\Login as BaseLogin;
use Filament\Forms\Form;
use Filament\Pages\Auth\Login as BaseAuth;
use Coderflex\FilamentTurnstile\Forms\Components\Turnstile;
use Coderflex\FilamentTurnstile\Forms\Components\TurnstileField;
use Filament\Forms\Components\ViewField;
use Filament\Forms\Components\Checkbox;



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
        return $form
            ->schema([
                $this->getEmailFormComponent(),
                $this->getPasswordFormComponent(),
                $this->getRememberFormComponent(),

                Turnstile::make('turnstile')
                    ->theme('light')
                    ->language('id')
                    ->required(),


                Checkbox::make('agree')
                    ->label('Saya setuju dan bukan bot')
                    ->accepted()
                    ->required(),
            ]);
    }


    protected function authenticate(): void
    {
        // ❌ BLOCK kalau checkbox belum dicentang
        if (! $this->agree) {
            $this->throwFailureValidationException();
        }

        parent::authenticate();
    }
}
