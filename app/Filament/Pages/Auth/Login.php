<?php

namespace App\Filament\Pages\Auth;

use Filament\Pages\Auth\Login as BaseLogin;

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
}
