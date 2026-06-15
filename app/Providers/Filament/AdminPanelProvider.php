<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
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
use Illuminate\Session\Middleware\AuthenticateSession;
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
            ->brandLogo(new \Illuminate\Support\HtmlString(
                '<div class="sit-brand" style="display:flex;align-items:center;gap:10px;">' .
                '<img src="' . asset('images/logo-sit.png') . '" alt="Logo SIT" style="height:2.5rem;">' .
                '<span style="font-weight:700;font-size:0.9rem;white-space:nowrap;">Finance Bunga Cempaka</span>' .
                '</div>'
            ))
            ->brandLogoHeight('2.5rem')
            ->renderHook(
                \Filament\View\PanelsRenderHook::BODY_START,
                fn() => new \Illuminate\Support\HtmlString('
                    <style>
                        /* Sembunyikan fi-logo bawaan di halaman login */
                        .fi-simple-page .fi-logo { display: none !important; }
                    </style>
                ')
            )
            ->renderHook(
                \Filament\View\PanelsRenderHook::AUTH_LOGIN_FORM_BEFORE,
                fn() => new \Illuminate\Support\HtmlString('
                    <div style="text-align:center; margin-bottom:1.5rem;">
                        <img src="' . asset('images/logo-sit.png') . '" 
                            alt="Logo SIT" 
                            style="height:5rem; display:block; margin:0 auto 0.75rem;">
                        <div style="font-weight:700; font-size:1.2rem; color:#111827;">
                            Finance Bunga Cempaka
                        </div>
                        <div style="font-size:0.8rem; color:#6b7280; margin-top:0.25rem;">
                            Sistem Informasi Keuangan
                        </div>
                    </div>
                ')
            )
            ->login()
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                \App\Filament\Pages\Dashboard::class,
                \App\Filament\Pages\PembayaranSiswaPage::class,
                \App\Filament\Pages\KasHarianPage::class,
                \App\Filament\Pages\RekapBulananPage::class,  
                \App\Filament\Pages\PengeluaranOperasionalPage::class,  
                \App\Filament\Pages\PengeluaranSosialPage::class,       
                \App\Filament\Pages\PengeluaranUpahPage::class,   
                \App\Filament\Pages\AbsenHarianPage::class,
                \App\Filament\Pages\GajiBulananPage::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
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