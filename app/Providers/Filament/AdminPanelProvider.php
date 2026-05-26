<?php

namespace App\Providers\Filament;

use Filament\Pages;
use Filament\Panel;
use App\Models\User;
use Filament\Widgets;
use Filament\PanelProvider;
use Filament\Support\Assets\Css;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Filament\Navigation\UserMenuItem;
use Filament\Navigation\NavigationItem;
use Filament\Http\Middleware\Authenticate;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Facades\FilamentColor;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Filament\Http\Middleware\AuthenticateSession;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Filament\Enums\ThemeMode;
use App\Filament\Pages\Auth\Login;

class AdminPanelProvider extends PanelProvider
{
    public function boot(): void

    {

        FilamentAsset::register([
            Css::make('login-bg', asset('login.css')),
        ]);
    }

    public function panel(Panel $panel): Panel
    {
        return $panel

            ->default()
            //->spa()
            ->id('admin')
            ->login(\App\Filament\Pages\Auth\Login::class)
            ->path('admin')
            ->brandName('Hoaxs Tracer')
            ->brandLogo(asset('storage/bengkalis.png'))
            ->brandLogoHeight('3rem')
            ->darkMode(false)
            ->defaultThemeMode(ThemeMode::Light)
            ->navigationItems([
                NavigationItem::make('WA Login')
                    ->icon('heroicon-o-qr-code')
                    ->group('Integrasi')
                    ->sort(99) // biar di bawah
                    ->url(url('/wa-login'), shouldOpenInNewTab: true)
                    ->visible(fn() => auth()->check() && auth()->user()->role === 'admin'),

            ])

            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn(): string => '<link rel="stylesheet" href="' . asset('css/custom.css') . '">'
            )

    //         ->renderHook(
    //             PanelsRenderHook::HEAD_END,
    //             fn() => '
    //     <link rel="manifest" href="/manifest.json">
    //     <meta name="theme-color" content="#0f172a">
    // '
    //         )
    //         ->navigationItems([
    //             NavigationItem::make('Contoh Keyword')
    //                 ->icon('heroicon-s-book-open')
    //                 ->url("javascript:window.open(
    //         'https://docs.google.com/spreadsheets/d/10affbAEggTnIyGU4LSvNU-Dygaoq6-o9/edit?usp=sharing',
    //         'popupWindow',
    //         'width=1000,height=700,scrollbars=yes,resizable=yes'
    //     )")
    //                 ->sort(999),
    //         ])
            ->renderHook(
                PanelsRenderHook::BODY_END,
                fn() => '
    <script>
        if ("serviceWorker" in navigator) {
            navigator.serviceWorker.register("/sw.js");
        }
    </script>
    '
            )

            ->colors([

                'primary' => Color::Blue,
                'success' => Color::Green,
                'warning' => Color::Yellow,
                'danger'  => Color::Red,
            ])
            // ->navigationItems([
            //     NavigationItem::make('Dashboard')
            //         ->icon('heroicon-s-home')
            //         ->url(fn() => route('filament.admin.pages.dashboard'))
            //         ->isActiveWhen(fn() => request()->routeIs('filament.admin.pages.dashboard'))

            // ])    


            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
