<?php

namespace App\Providers\Filament;

use App\Filament\Resources\PermissionResource\Widgets\PermissionOverview;
use App\Filament\Widgets\AppOverview;
use App\Filament\Widgets\WelcomeWidget;
use App\Http\Middleware\DosenScope;
use App\Models\User;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Navigation\NavigationBuilder;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Joaopaulolndev\FilamentEditProfile\FilamentEditProfilePlugin;
use Swis\Filament\Backgrounds\FilamentBackgroundsPlugin;
use Swis\Filament\Backgrounds\ImageProviders\MyImages;
use TomatoPHP\FilamentPWA\Filament\Pages\PWASettingsPage;
use TomatoPHP\FilamentPWA\FilamentPWAPlugin;

class DosenPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('dosen')
            ->path('dosen')
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                WelcomeWidget::class,
                AppOverview::class,
                PermissionOverview::class,
                // Widgets\AccountWidget::class,
                // Widgets\FilamentInfoWidget::class,
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
                DosenScope::class,
            ])
            ->spaUrlExceptions([
                '*/admin/attendance-locations/*',
            ])
            ->topNavigation(true)
            ->plugins([

                FilamentEditProfilePlugin::make()
                    ->slug('my-profile')
                    ->setTitle('Profile Saya')
                    ->setNavigationLabel('Profile Saya')
                    ->setIcon('heroicon-o-user')
                    ->shouldRegisterNavigation(function () {
                        return \App\Models\User::isAdmin();
                    })
                    ->setSort(10),
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->favicon('/favicon.png')
            ->brandLogo(function () {
                return view('logo');
            })
            ->spa()
            ->databaseNotifications()
            ->userMenuItems([
                'profile' => MenuItem::make()
                    ->label('Edit profile')
                    ->url('/dosen/my-profile'),
                // ...
            ]);
    }
}
