<?php

namespace App\Providers\Filament;

use Filament\Pages;
use Filament\Panel;
use Filament\Widgets;
use Filament\PanelProvider;
use Filament\Enums\ThemeMode;
use Filament\Support\Colors\Color;
use Filament\Http\Middleware\Authenticate;
use App\Http\Middleware\PegawaiRoleRedirect;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Filament\Http\Middleware\AuthenticateSession;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use SolutionForest\FilamentSimpleLightBox\SimpleLightBoxPlugin;
use Joaopaulolndev\FilamentEditProfile\FilamentEditProfilePlugin;

class PegawaiPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('pegawai')
            ->path('pegawai')
            ->login()
            ->colors([
                'primary' => Color::Blue,
            ])
            ->breadcrumbs(false)
            ->maxContentWidth('full')
            ->plugin(SimpleLightBoxPlugin::make())
            ->brandName(env('APP_NAME'))
            ->maxContentWidth('full')
            ->sidebarCollapsibleOnDesktop()
            ->brandLogo(asset('images/login_pegawai.png'))
            ->favicon(asset('favicons/android-chrome-192x192.png'))
            ->brandLogoHeight(fn() => request()->route()->getName() == 'filament.pegawai.auth.login' ? '15rem' : '7rem')
            ->defaultThemeMode(ThemeMode::Light)
            ->discoverResources(in: app_path('Filament/Pegawai/Resources'), for: 'App\\Filament\\Pegawai\\Resources')
            ->discoverPages(in: app_path('Filament/Pegawai/Pages'), for: 'App\\Filament\\Pegawai\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->plugins([
                FilamentEditProfilePlugin::make()
                ->setNavigationLabel(false)
                ->shouldRegisterNavigation(false)
                ->shouldShowDeleteAccountForm(false)
                ->shouldShowAvatarForm(false),
                // ->setIcon('heroicon-o-cog-8-tooth')
                // ->shouldShowAvatarForm(),
                // FilamentApexChartsPlugin::make(),
                FilamentShieldPlugin::make()
                    ->gridColumns([
                        'default' => 1,
                        'sm' => 2,
                        'lg' => 3
                    ])
                    ->sectionColumnSpan(1)
                    ->checkboxListColumns([
                        'default' => 1,
                        'sm' => 2,
                        'lg' => 4,
                    ])
                    ->resourceCheckboxListColumns([
                        'default' => 1,
                        'sm' => 2,
                    ]),
            ])
            ->discoverWidgets(in: app_path('Filament/Pegawai/Widgets'), for: 'App\\Filament\\Pegawai\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
            ])
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
                PegawaiRoleRedirect::class,
            ])
            ->authMiddleware([
                // Authenticate::class,
                \Filament\Http\Middleware\Authenticate::class,
            ]);
    }
}
