<?php

namespace App\Filament\Pages;

use App\Services\DiajengService;
use Filament\Actions\Action;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\HtmlString;

class DiajengSettings extends Page
{
    protected static string|\BackedEnum|null $navigationIcon  = 'heroicon-o-globe-alt';
    protected static ?string               $navigationLabel = 'DIAJENG LLDIKTI6';
    protected static string|\UnitEnum|null $navigationGroup = 'Integrasi API';
    protected static ?int                  $navigationSort  = 10;
    protected static ?string               $title           = 'Integrasi DIAJENG LLDIKTI6';
    protected string                       $view            = 'filament.pages.diajeng-settings';

    public ?array $data = [];

    public ?string $pingResult   = null;
    public bool    $pingSuccess  = false;
    public ?array  $previewData  = null;

    public function mount(): void
    {
        $this->data = [
            'api_key'  => config('services.diajeng.api_key'),
            'base_url' => config('services.diajeng.base_url'),
            'cache_ttl'=> config('services.diajeng.cache_ttl'),
        ];
        $this->form->fill($this->data);
    }

    public function schema(Schema $schema): Schema
    {
        return $schema->components([
            Form::make([
                Section::make('Konfigurasi API')
                    ->description('Masukkan API Key dari DIAJENG LLDIKTI6 untuk mengaktifkan integrasi.')
                    ->schema([
                        TextInput::make('api_key')
                            ->label('API Key')
                            ->placeholder('Masukkan X-API-Key dari DIAJENG')
                            ->password()
                            ->revealable()
                            ->helperText('Dapatkan API key melalui portal DIAJENG LLDIKTI6.')
                            ->columnSpanFull(),

                        TextInput::make('base_url')
                            ->label('Base URL')
                            ->default('https://diajeng.lldikti6.id/api/v1')
                            ->url()
                            ->columnSpanFull(),

                        TextInput::make('cache_ttl')
                            ->label('Cache TTL (detik)')
                            ->numeric()
                            ->default(3600)
                            ->helperText('Berapa lama data disimpan di cache sebelum di-refresh. Default: 3600 (1 jam).'),
                    ]),

                Section::make('Cara Konfigurasi')
                    ->collapsed()
                    ->schema([
                        Placeholder::make('instructions')
                            ->label('')
                            ->content(new HtmlString('
                                <div class="prose text-sm max-w-none">
                                    <ol class="space-y-2">
                                        <li>Login ke <strong>portal DIAJENG LLDIKTI6</strong> sebagai pengelola jurnal.</li>
                                        <li>Masuk ke menu <strong>Pengaturan → API Keys</strong> lalu buat key baru.</li>
                                        <li>Salin key tersebut dan tempelkan di kolom <strong>API Key</strong> di atas.</li>
                                        <li>Klik <strong>Simpan ke .env</strong>, lalu klik <strong>Test Koneksi</strong> untuk verifikasi.</li>
                                    </ol>
                                    <p class="mt-3 text-slate-500">Alternatif: tambahkan langsung ke file <code>.env</code> project:</p>
                                    <pre class="bg-slate-900 text-green-400 p-3 rounded-lg text-xs">DIAJENG_API_KEY=your-api-key-here
DIAJENG_CACHE_TTL=3600</pre>
                                </div>
                            ')),
                    ]),
            ])->statePath('data'),
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label('Simpan ke .env')
                ->icon('heroicon-o-document-arrow-down')
                ->color('primary')
                ->action('save'),

            Action::make('ping')
                ->label('Test Koneksi')
                ->icon('heroicon-o-signal')
                ->color('success')
                ->action('ping'),

            Action::make('preview_journals')
                ->label('Preview Jurnal')
                ->icon('heroicon-o-eye')
                ->color('info')
                ->action('previewJournals'),

            Action::make('clear_cache')
                ->label('Hapus Cache')
                ->icon('heroicon-o-trash')
                ->color('warning')
                ->requiresConfirmation()
                ->action('clearCache'),
        ];
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $envPath = base_path('.env');
        $env     = file_get_contents($envPath);

        $replacements = [
            'DIAJENG_API_KEY'   => $data['api_key']   ?? '',
            'DIAJENG_BASE_URL'  => $data['base_url']  ?? 'https://diajeng.lldikti6.id/api/v1',
            'DIAJENG_CACHE_TTL' => $data['cache_ttl'] ?? '3600',
        ];

        foreach ($replacements as $key => $value) {
            if (preg_match("/^{$key}=.*/m", $env)) {
                $env = preg_replace("/^{$key}=.*/m", "{$key}={$value}", $env);
            } else {
                $env .= "\n{$key}={$value}";
            }
        }

        file_put_contents($envPath, $env);
        Artisan::call('config:clear');

        Notification::make()
            ->title('Tersimpan')
            ->body('Konfigurasi DIAJENG berhasil disimpan ke .env. Jalankan Test Koneksi untuk verifikasi.')
            ->success()
            ->send();
    }

    public function ping(): void
    {
        $result = app(DiajengService::class)->ping();

        $this->pingSuccess = $result['ok'];
        $this->pingResult  = $result['message'];

        if ($result['ok']) {
            Notification::make()->title('Terhubung')->body($result['message'])->success()->send();
        } else {
            Notification::make()->title('Gagal')->body($result['message'])->danger()->send();
        }
    }

    public function previewJournals(): void
    {
        $service = app(DiajengService::class);

        if (! $service->isConfigured()) {
            Notification::make()->title('API Key belum diisi')->warning()->send();
            return;
        }

        $result = $service->journals(['per_page' => 5]);
        $this->previewData = $result;

        Notification::make()
            ->title('Preview Berhasil')
            ->body('Data 5 jurnal pertama ditampilkan di bawah.')
            ->success()
            ->send();
    }

    public function clearCache(): void
    {
        \Illuminate\Support\Facades\Cache::flush();

        Notification::make()->title('Cache dihapus')->success()->send();
    }
}
