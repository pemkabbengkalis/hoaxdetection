<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Navigation\NavigationItem;
use App\Models\User;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Assets\Css;
use Filament\View\PanelsRenderHook;







class AdminPanelProvider extends PanelProvider
{

    //------------adrian-------------------------------------//

    public function boot(): void
    {
        FilamentAsset::register([
            Css::make('login-bg', asset('login.css')),
        ]);
    }
    //------------end of adrian------------------------------//
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            //->spa()
            ->id('admin')
            ->path('admin')
            ->brandLogo(logo: asset('storage/bengkalis.png'))
            ->brandLogoHeight('3rem') // 🔥 SIZE LOGO (rem)
            ->login()
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn() => '
        <link rel="manifest" href="/manifest.json">
        <meta name="theme-color" content="#0f172a">
    '
            )

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


            //-------------------adrian------------------------------------//
            ->colors([

                'primary' => Color::Blue,
                'success' => Color::Green,
                'warning' => Color::Yellow,
                'danger'  => Color::Red,
            ])
            ->navigationItems([
                NavigationItem::make('Dashboard')
                    ->icon('heroicon-s-home')
                    ->url(fn() => route('filament.admin.pages.dashboard'))
                    ->isActiveWhen(fn() => request()->routeIs('filament.admin.pages.dashboard'))

            ])


            //------------------------end of adrian-------------------------//

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
