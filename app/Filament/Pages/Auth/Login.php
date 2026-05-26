<?php

namespace App\Filament\Pages\Auth;

use Filament\Pages\Auth\Login as BaseLogin;
use Filament\Forms\Form;
use Filament\Pages\Auth\Login as BaseAuth;
use Coderflex\FilamentTurnstile\Forms\Components\Turnstile;

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

    protected function getForms(): array
    {
        return [
            'form' => $this->form(
                $this->makeForm()
                    ->schema([
                        $this->getEmailFormComponent(),
                        $this->getPasswordFormComponent(),
                        $this->getRememberFormComponent(),

                        Turnstile::make('captcha')
                            ->theme('light')
                            ->language('id')
                            ->size('normal')
                            ->required(),
                    ])
                    ->statePath('data'),
            ),
        ];
    }
    //-----------------------cloudflare captcha--------------------------------
}
