<?php

namespace App\Providers\Filament;

use App\Filament\Widgets\AppOverview;
use App\Filament\Widgets\WelcomeWidget;
use App\Http\Middleware\AdminScope;
use App\Models\User;
use BezhanSalleh\FilamentExceptions\FilamentExceptionsPlugin;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Navigation\NavigationBuilder;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
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
use Joaopaulolndev\FilamentEditProfile\FilamentEditProfilePlugin;
use Swis\Filament\Backgrounds\FilamentBackgroundsPlugin;
use Swis\Filament\Backgrounds\ImageProviders\MyImages;
use TomatoPHP\FilamentPWA\Filament\Pages\PWASettingsPage;
use TomatoPHP\FilamentPWA\FilamentPWAPlugin;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {

        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->loginRouteSlug('login')
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
                PWASettingsPage::class
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                WelcomeWidget::class,
                AppOverview::class,
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
                AdminScope::class,
            ])
            ->spaUrlExceptions([
                '*/admin/attendance-locations/*',
                '*/admin/attendances/create/*',
            ])
            ->navigationItems([

                NavigationItem::make('Pengaturan')
                    ->url('/admin/exceptions')
                    ->icon('heroicon-o-cpu-chip')
                    ->group('Pengaturan')
                    ->label('Log Error')
                    ->visible(function () {
                        return \App\Models\User::isAdmin();
                    })
                    ->isActiveWhen(fn() => request()->routeIs('admin/exceptions/*'))
                    ->sort(5),

                NavigationItem::make('Settings Hub')
                    ->url('/admin/pwa-settings-page')
                    ->icon('heroicon-o-cog')
                    ->group('Pengaturan')
                    ->label('PWA Settings')
                    ->visible(function () {
                        return \App\Models\User::isAdmin();
                    })
                    ->isActiveWhen(fn() => request()->routeIs('admin/pwa-settings-page/*'))
                    ->sort(5),

                NavigationItem::make('Settings Hub')
                    ->url('/admin/settings-hub')
                    ->icon('heroicon-o-cog')
                    ->group('Settings')
                    ->label('Settings Hub')
                    ->visible(function () {
                        return false;
                    })
                    ->isActiveWhen(fn() => request()->routeIs('admin/exceptions/*'))
                    ->sort(5),
            ])
            ->plugins([
                FilamentExceptionsPlugin::make(),
                FilamentBackgroundsPlugin::make()
                    ->imageProvider(
                        MyImages::make()
                            ->directory('bg-images')
                    ),
                FilamentEditProfilePlugin::make()
                    ->slug('my-profile')
                    ->setTitle('Profile Saya')
                    ->setNavigationLabel('Profile Saya')
                    ->setIcon('heroicon-o-user')
                    ->shouldRegisterNavigation(function () {
                        return \App\Models\User::isAdmin();
                    })
                    ->setSort(10),

                // FilamentPWAPlugin::make(),
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->favicon('/favicon.png')
            ->brandLogo(function () {
                return view('logo');
            })
            ->spa()
            ->userMenuItems([
                'profile' => MenuItem::make()
                    ->label('Edit profile')
                    ->url('/admin/my-profile'),
                // ...
            ]);
    }
}
