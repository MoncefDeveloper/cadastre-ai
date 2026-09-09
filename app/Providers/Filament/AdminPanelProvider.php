<?php

declare(strict_types=1);

namespace App\Providers\Filament;

use App\Filament\Pages\Auth\CustomLogin;
use App\Filament\Pages\Dashboard;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Blade;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->login(CustomLogin::class)
            ->sidebarCollapsibleOnDesktop()
            /*
             |----------------------------------------------------------------------
             | Brand Identity & Multi-Theme Vector Assets
             |----------------------------------------------------------------------
             */
            ->brandName('Cadastre AI')
            ->brandLogo(asset('images/cadastre-horizontal-dark.svg'))
            ->darkModeBrandLogo(asset('images/cadastre-horizontal-light.svg'))
            ->brandLogoHeight('1.5rem')
            ->favicon(asset('images/cadastre-mark-favicon-dark.svg'))
            /*
             |----------------------------------------------------------------------
             | Design System: Imperial Carmine, Flame Vermilion & Cold Obsidian
             |----------------------------------------------------------------------
             */
            ->colors([
                // 1. Sovereign Imperial Carmine (Brand Primary Accent)
                'primary' => [
                    50  => '#fff1f3',
                    100 => '#ffe4e8',
                    200 => '#fecdd6',
                    300 => '#fda4b4',
                    400 => '#fb718b',
                    500 => '#be123c', // The Exact Logo Carmine
                    600 => '#9f1239', // Bordeaux Hover
                    700 => '#880e2f',
                    800 => '#70102b',
                    900 => '#5f1127',
                    950 => '#3a0413',
                ],

                // 2. High-Alert Flame Vermilion (System Danger & Errors - 33° Optical Separation)
                'danger' => [
                    50  => '#fff7ed',
                    100 => '#ffedd5',
                    200 => '#fed7aa',
                    300 => '#fdba74',
                    400 => '#fb923c',
                    500 => '#f95428', // Blazing Emergency Vermilion
                    600 => '#ea3c12',
                    700 => '#c22c0b',
                    800 => '#9a240e',
                    900 => '#7c200f',
                    950 => '#4c0519',
                ],

                // 3. Cold Obsidian Gray Ramp (Canvas & Elevated Cards)
                'gray' => [
                    50  => '#f8fafc', // Light Canvas
                    100 => '#f1f5f9',
                    200 => '#e2e8f0', // Light Hairline Borders
                    300 => '#cbd5e1',
                    400 => '#94a3b8', // Subtitles & Icons
                    500 => '#64748b',
                    600 => '#475569',
                    700 => '#334155',
                    800 => '#1e293b',
                    900 => '#0d1117', // Dark Elevated Card Surface
                    950 => '#05070b', // Dark Base Page Canvas
                ],

                'success' => Color::Emerald,
                'warning' => Color::Amber,
                'info'    => Color::Sky,
            ])
            /*
             |----------------------------------------------------------------------
             | Navigation Information Architecture (Clean Text Group Headers)
             |----------------------------------------------------------------------
             */
            ->navigationGroups([
                NavigationGroup::make('Portfolio'),
                NavigationGroup::make('Client CRM'),
                NavigationGroup::make('AI Copilot'),
                NavigationGroup::make('Commercial'),
                NavigationGroup::make('Administration')
                    ->collapsed(true),
            ])
            // 🛡️ Positioned directly AFTER the search bar
            ->renderHook(
                PanelsRenderHook::TOPBAR_LOGO_AFTER,
                fn(): string => Blade::render('@livewire(\App\Livewire\SandboxTopbarWidget::class)')
            )
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([])
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
            ->globalSearchKeyBindings(['command+k', 'ctrl+k'])
            ->plugins([
                FilamentShieldPlugin::make()
                    ->gridColumns([
                        'default' => 1,
                        'sm' => 2,
                        'lg' => 3,
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
            ->authMiddleware([
                Authenticate::class,
            ])
            ->databaseNotifications()
            ->databaseNotificationsPolling('30s');
    }
}
