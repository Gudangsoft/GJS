<x-filament-panels::page>
<style>
.adm-grid { display:grid; gap:1rem; }
.adm-status-grid { grid-template-columns: repeat(3, 1fr); }
@media(min-width:768px){ .adm-status-grid { grid-template-columns: repeat(6, 1fr); } }
.adm-two-col { grid-template-columns: 1fr; }
@media(min-width:1024px){ .adm-two-col { grid-template-columns: 1fr 1fr; } }
.adm-full { grid-column: 1 / -1; }

.adm-card { background:var(--color-white,#fff); border:1px solid #e2e8f0; border-radius:.75rem; overflow:hidden; box-shadow:0 1px 3px rgba(0,0,0,.06); }
.dark .adm-card { background:#1e293b; border-color:#334155; }
.adm-card-header { padding:.875rem 1.25rem; border-bottom:1px solid #e2e8f0; background:#f8fafc; display:flex; align-items:center; gap:.5rem; }
.dark .adm-card-header { background:#0f172a; border-color:#334155; }
.adm-card-title { font-size:.75rem; font-weight:700; text-transform:uppercase; letter-spacing:.05em; color:#374151; }
.dark .adm-card-title { color:#e2e8f0; }

.adm-stat { background:#fff; border:1px solid #e2e8f0; border-radius:.75rem; padding:.875rem 1rem; display:flex; align-items:center; gap:.75rem; box-shadow:0 1px 2px rgba(0,0,0,.04); }
.dark .adm-stat { background:#1e293b; border-color:#334155; }
.adm-stat-icon { width:2.25rem; height:2.25rem; border-radius:.5rem; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.adm-stat-icon.ok { background:#dcfce7; }
.adm-stat-icon.warn { background:#fee2e2; }
.dark .adm-stat-icon.ok { background:#14532d40; }
.dark .adm-stat-icon.warn { background:#7f1d1d40; }
.adm-stat-label { font-size:.7rem; color:#94a3b8; line-height:1; margin-bottom:.25rem; }
.adm-stat-value { font-size:.8125rem; font-weight:600; color:#0f172a; }
.dark .adm-stat-value { color:#f1f5f9; }

.adm-link { display:flex; align-items:center; justify-content:space-between; padding:.875rem 1.25rem; border-bottom:1px solid #f1f5f9; text-decoration:none; transition:background .15s; cursor:pointer; width:100%; text-align:left; background:transparent; border-left:none; border-right:none; border-top:none; }
.adm-link:last-child { border-bottom:none; }
.adm-link:hover { background:#eff6ff; }
.dark .adm-link { border-color:#1e293b; }
.dark .adm-link:hover { background:#1e3a5f30; }
.adm-link-inner { display:flex; align-items:center; gap:.75rem; }
.adm-link-text { font-size:.875rem; font-weight:500; color:#2563eb; }
.dark .adm-link-text { color:#60a5fa; }
.adm-link-desc { font-size:.7rem; color:#94a3b8; margin-top:.1rem; }
.adm-link-danger .adm-link-text { color:#dc2626; }
.dark .adm-link-danger .adm-link-text { color:#f87171; }
.adm-link-danger:hover { background:#fff1f2 !important; }

.adm-sysinfo { display:grid; grid-template-columns: repeat(2, 1fr); }
@media(min-width:640px){ .adm-sysinfo { grid-template-columns: repeat(5, 1fr); } }
.adm-sysinfo-item { padding:.875rem 1.25rem; border-right:1px solid #f1f5f9; border-bottom:1px solid #f1f5f9; }
.dark .adm-sysinfo-item { border-color:#1e293b; }
.adm-sysinfo-label { font-size:.7rem; color:#94a3b8; margin-bottom:.2rem; }
.adm-sysinfo-value { font-size:.8125rem; font-weight:600; color:#0f172a; }
.dark .adm-sysinfo-value { color:#f1f5f9; }
</style>

@php
$info = $this->getSystemInfo();
$iconOk   = '<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>';
$iconWarn = '<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>';
$chevron  = '<svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 18l6-6-6-6"/></svg>';
@endphp

{{-- Status Bar --}}
<div class="adm-grid adm-status-grid" style="margin-bottom:1.5rem;">
    @foreach([
        ['label'=>'PHP',        'value'=>$info['php'],     'ok'=>true],
        ['label'=>'Laravel',    'value'=>$info['laravel'], 'ok'=>true],
        ['label'=>'Database',   'value'=>$info['db_ver'],  'ok'=>$info['db_ok']],
        ['label'=>'Environment','value'=>$info['env'],     'ok'=>$info['env']==='production'],
        ['label'=>'Debug Mode', 'value'=>$info['debug'],   'ok'=>$info['debug']==='Nonaktif'],
        ['label'=>'Cache',      'value'=>$info['cache'],   'ok'=>true],
    ] as $item)
    <div class="adm-stat">
        <div class="adm-stat-icon {{ $item['ok'] ? 'ok' : 'warn' }}" style="color:{{ $item['ok'] ? '#16a34a' : '#dc2626' }}">
            {!! $item['ok'] ? $iconOk : $iconWarn !!}
        </div>
        <div>
            <div class="adm-stat-label">{{ $item['label'] }}</div>
            <div class="adm-stat-value">{{ $item['value'] }}</div>
        </div>
    </div>
    @endforeach
</div>

{{-- Main Grid --}}
<div class="adm-grid adm-two-col">

    {{-- Site Management --}}
    <div class="adm-card">
        <div class="adm-card-header">
            <svg width="18" height="18" fill="none" stroke="#2563eb" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4z"/></svg>
            <span class="adm-card-title">Site Management</span>
        </div>
        @foreach([
            ['href'=>\App\Filament\Resources\Journals\JournalResource::getUrl('index'),    'icon'=>'📚', 'label'=>'Hosted Journals',  'desc'=>'Kelola semua jurnal yang di-host'],
            ['href'=>\App\Filament\Resources\Users\UserResource::getUrl('index'),          'icon'=>'👥', 'label'=>'Users & Roles',     'desc'=>'Kelola pengguna dan hak akses'],
            ['href'=>\App\Filament\Resources\Sections\SectionResource::getUrl('index'),   'icon'=>'🏷️', 'label'=>'Journal Sections',  'desc'=>'Kelola seksi tiap jurnal'],
            ['href'=>\App\Filament\Resources\Announcements\AnnouncementResource::getUrl('index'), 'icon'=>'📢', 'label'=>'Announcements', 'desc'=>'Pengumuman platform'],
            ['href'=>\App\Filament\Resources\Issues\IssueResource::getUrl('index'),       'icon'=>'📦', 'label'=>'Issues',             'desc'=>'Kelola terbitan jurnal'],
        ] as $link)
        <a href="{{ $link['href'] }}" class="adm-link">
            <div class="adm-link-inner">
                <span style="font-size:1rem;">{{ $link['icon'] }}</span>
                <div>
                    <div class="adm-link-text">{{ $link['label'] }}</div>
                    <div class="adm-link-desc">{{ $link['desc'] }}</div>
                </div>
            </div>
            <span style="color:#cbd5e1;">{!! $chevron !!}</span>
        </a>
        @endforeach
    </div>

    {{-- Administrative Functions --}}
    <div class="adm-card">
        <div class="adm-card-header">
            <svg width="18" height="18" fill="none" stroke="#2563eb" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 11-3.586-3.586l5.653-4.655m7.43-6.983a3 3 0 00-4.243 4.243"/></svg>
            <span class="adm-card-title">Administrative Functions</span>
        </div>

        <button wire:click="clearAppCache" wire:loading.attr="disabled" wire:target="clearAppCache" class="adm-link">
            <div class="adm-link-inner">
                <span style="font-size:1rem;">🗄️</span>
                <div>
                    <div class="adm-link-text">Clear Data Caches</div>
                    <div class="adm-link-desc">php artisan cache:clear</div>
                </div>
            </div>
            <span wire:loading wire:target="clearAppCache" style="color:#f59e0b;font-size:.75rem;">⏳</span>
        </button>

        <button wire:click="clearViewCache" wire:loading.attr="disabled" wire:target="clearViewCache" class="adm-link">
            <div class="adm-link-inner">
                <span style="font-size:1rem;">📄</span>
                <div>
                    <div class="adm-link-text">Clear Template Cache</div>
                    <div class="adm-link-desc">php artisan view:clear</div>
                </div>
            </div>
            <span wire:loading wire:target="clearViewCache" style="color:#f59e0b;font-size:.75rem;">⏳</span>
        </button>

        <button wire:click="clearConfigCache" wire:loading.attr="disabled" wire:target="clearConfigCache" class="adm-link">
            <div class="adm-link-inner">
                <span style="font-size:1rem;">⚙️</span>
                <div>
                    <div class="adm-link-text">Clear Config Cache</div>
                    <div class="adm-link-desc">php artisan config:clear</div>
                </div>
            </div>
            <span wire:loading wire:target="clearConfigCache" style="color:#f59e0b;font-size:.75rem;">⏳</span>
        </button>

        <button wire:click="clearRouteCache" wire:loading.attr="disabled" wire:target="clearRouteCache" class="adm-link">
            <div class="adm-link-inner">
                <span style="font-size:1rem;">🗺️</span>
                <div>
                    <div class="adm-link-text">Clear Route Cache</div>
                    <div class="adm-link-desc">php artisan route:clear</div>
                </div>
            </div>
            <span wire:loading wire:target="clearRouteCache" style="color:#f59e0b;font-size:.75rem;">⏳</span>
        </button>

        <button wire:click="expireSessions"
                wire:loading.attr="disabled"
                wire:target="expireSessions"
                wire:confirm="Ini akan mengakhiri semua sesi login pengguna aktif. Lanjutkan?"
                class="adm-link adm-link-danger">
            <div class="adm-link-inner">
                <span style="font-size:1rem;">🚪</span>
                <div>
                    <div class="adm-link-text">Expire User Sessions</div>
                    <div class="adm-link-desc">Mengakhiri semua sesi aktif pengguna</div>
                </div>
            </div>
            <span wire:loading wire:target="expireSessions" style="color:#dc2626;font-size:.75rem;">⏳</span>
        </button>

        <button wire:click="clearActivityLog"
                wire:loading.attr="disabled"
                wire:target="clearActivityLog"
                wire:confirm="Ini akan menghapus seluruh activity log. Lanjutkan?"
                class="adm-link adm-link-danger">
            <div class="adm-link-inner">
                <span style="font-size:1rem;">🗑️</span>
                <div>
                    <div class="adm-link-text">Clear Activity Logs</div>
                    <div class="adm-link-desc">Menghapus seluruh log aktivitas platform</div>
                </div>
            </div>
            <span wire:loading wire:target="clearActivityLog" style="color:#dc2626;font-size:.75rem;">⏳</span>
        </button>
    </div>

    {{-- System Information --}}
    <div class="adm-card adm-full">
        <div class="adm-card-header">
            <svg width="18" height="18" fill="none" stroke="#2563eb" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/></svg>
            <span class="adm-card-title">System Information</span>
        </div>
        <div class="adm-sysinfo">
            @foreach([
                ['label'=>'Timezone', 'value'=>$info['timezone']],
                ['label'=>'Locale',   'value'=>$info['locale']],
                ['label'=>'Queue',    'value'=>$info['queue']],
                ['label'=>'PHP SAPI', 'value'=>php_sapi_name()],
                ['label'=>'OS',       'value'=>PHP_OS],
            ] as $row)
            <div class="adm-sysinfo-item">
                <div class="adm-sysinfo-label">{{ $row['label'] }}</div>
                <div class="adm-sysinfo-value">{{ $row['value'] }}</div>
            </div>
            @endforeach
        </div>
    </div>

</div>
</x-filament-panels::page>
