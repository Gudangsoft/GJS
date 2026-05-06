<?php

namespace App\Filament\Pages;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class Administration extends Page
{
    protected static string|\BackedEnum|null $navigationIcon  = 'heroicon-o-server-stack';
    protected static ?string               $navigationLabel = 'Administrasi';
    protected static string|\UnitEnum|null $navigationGroup = 'Administrasi Situs';
    protected static ?int                  $navigationSort  = 1;
    protected string                       $view            = 'filament.pages.administration';
    protected static ?string               $title           = 'Administrasi Situs';

    public function getSubheading(): ?string
    {
        return config('app.name') . ' · ' . config('app.url');
    }

    // ── System info exposed to view ───────────────────────────────────────────
    public function getSystemInfo(): array
    {
        $dbOk = false;
        $dbVersion = '—';
        try {
            $dbVersion = DB::selectOne('SELECT VERSION() as v')?->v ?? '—';
            $dbOk = true;
        } catch (\Throwable) {}

        return [
            'php'       => phpversion(),
            'laravel'   => app()->version(),
            'server'    => $_SERVER['SERVER_SOFTWARE'] ?? php_uname('s'),
            'db_ok'     => $dbOk,
            'db_ver'    => $dbVersion,
            'env'       => app()->environment(),
            'debug'     => config('app.debug') ? 'Aktif' : 'Nonaktif',
            'timezone'  => config('app.timezone'),
            'locale'    => config('app.locale'),
            'cache'     => config('cache.default'),
            'queue'     => config('queue.default'),
        ];
    }

    // ── Header actions ────────────────────────────────────────────────────────
    protected function getHeaderActions(): array
    {
        return [
            Action::make('clearAllCache')
                ->label('Bersihkan Semua Cache')
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Bersihkan Semua Cache?')
                ->modalDescription('Ini akan membersihkan config, route, view, dan application cache.')
                ->action(function () {
                    Artisan::call('optimize:clear');
                    Notification::make()
                        ->title('Semua cache berhasil dibersihkan')
                        ->success()
                        ->send();
                }),
        ];
    }

    // ── Page actions called via wire:click ────────────────────────────────────
    public function clearAppCache(): void
    {
        Artisan::call('cache:clear');
        Notification::make()->title('Application cache dibersihkan')->success()->send();
    }

    public function clearViewCache(): void
    {
        Artisan::call('view:clear');
        Notification::make()->title('Template/view cache dibersihkan')->success()->send();
    }

    public function clearConfigCache(): void
    {
        Artisan::call('config:clear');
        Notification::make()->title('Config cache dibersihkan')->success()->send();
    }

    public function clearRouteCache(): void
    {
        Artisan::call('route:clear');
        Notification::make()->title('Route cache dibersihkan')->success()->send();
    }

    public function expireSessions(): void
    {
        DB::table('sessions')->truncate();
        Notification::make()->title('Semua sesi pengguna telah diakhiri')->success()->send();
    }

    public function clearActivityLog(): void
    {
        if (\Schema::hasTable('activity_log')) {
            DB::table('activity_log')->truncate();
            Notification::make()->title('Activity log dibersihkan')->success()->send();
        } else {
            Notification::make()->title('Tabel activity_log tidak ditemukan')->warning()->send();
        }
    }
}
