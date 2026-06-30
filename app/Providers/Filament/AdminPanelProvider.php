<?php

namespace App\Providers\Filament;

use App\Filament\Widgets\AlertsWidget;
use App\Filament\Widgets\JournalBreakdownWidget;
use App\Filament\Widgets\RecentSubmissionsWidget;
use App\Filament\Widgets\StatsOverviewWidget;
use App\Filament\Widgets\SubmissionTrendWidget;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use LaraZeus\SpatieTranslatable\SpatieTranslatablePlugin;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()

            // ── Branding ─────────────────────────────────────────────────────
            ->brandName('GJS — Go Journal System')
            ->favicon(asset('favicon.ico'))

            // ── Performance ──────────────────────────────────────────────────
            ->spa()                          // SPA mode: navigasi tanpa full reload
            ->sidebarCollapsibleOnDesktop()  // Sidebar bisa diciutkan untuk lebih banyak ruang

            // ── Theme & Colors ───────────────────────────────────────────────
            ->colors([
                'primary' => Color::Blue,
                'gray'    => Color::Slate,
                'info'    => Color::Sky,
                'success' => Color::Emerald,
                'warning' => Color::Amber,
                'danger'  => Color::Rose,
            ])
            ->darkMode(false)

            // ── UX Enhancements ──────────────────────────────────────────────
            ->globalSearch(true)
            ->globalSearchKeyBindings(['command+k', 'ctrl+k'])
            ->databaseNotifications()
            ->databaseNotificationsPolling('30s')
            ->maxContentWidth('7xl')
            ->breadcrumbs(true)
            ->profile()

            // ── Navigation Groups ────────────────────────────────────────────
            ->navigationGroups([
                NavigationGroup::make('Naskah')
                    ->icon('heroicon-o-inbox-arrow-down')
                    ->collapsed(false),
                NavigationGroup::make('Penerbitan')
                    ->icon('heroicon-o-newspaper')
                    ->collapsed(false),
                NavigationGroup::make('Pengguna & Peran')
                    ->icon('heroicon-o-users')
                    ->collapsed(false),
                NavigationGroup::make('Pengaturan')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->collapsed(true),
                NavigationGroup::make('Administrasi Situs')
                    ->icon('heroicon-o-server-stack')
                    ->collapsed(true),
            ])

            // ── Translatable Content Plugin ──────────────────────────────────
            ->plugins([
                SpatieTranslatablePlugin::make()
                    ->defaultLocales(['id', 'en']),
            ])

            // ── Resources, Pages, Widgets ────────────────────────────────────
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([Dashboard::class])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                StatsOverviewWidget::class,
                SubmissionTrendWidget::class,
                RecentSubmissionsWidget::class,
                AlertsWidget::class,
                JournalBreakdownWidget::class,
                AccountWidget::class,
            ])

            // ── Middleware ───────────────────────────────────────────────────
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
