<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Actions\Action;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\HtmlString;

class LanguageSettings extends Page
{
    protected static string|\BackedEnum|null $navigationIcon  = 'heroicon-o-language';
    protected static ?string               $navigationLabel = 'Pengaturan Bahasa';
    protected static string|\UnitEnum|null $navigationGroup = 'Pengaturan';
    protected static ?int                  $navigationSort  = 30;
    protected static ?string               $title           = 'Pengaturan Multi-Bahasa';

    public ?array $data = [];

    protected static array $supportedLanguages = [
        'id' => ['name' => 'Bahasa Indonesia', 'flag' => '🇮🇩', 'dir' => 'ltr'],
        'en' => ['name' => 'English',           'flag' => '🇬🇧', 'dir' => 'ltr'],
        'ar' => ['name' => 'العربية',           'flag' => '🇸🇦', 'dir' => 'rtl'],
    ];

    public function mount(): void
    {
        $available = json_decode(
            Setting::get('language.available', '["id","en"]'),
            true
        ) ?? ['id', 'en'];

        $this->form->fill([
            'default_locale' => Setting::get('language.default', config('app.locale', 'id')),
            'lang_id'        => in_array('id', $available),
            'lang_en'        => in_array('en', $available),
            'lang_ar'        => in_array('ar', $available),
        ]);
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([
            Form::make([EmbeddedSchema::make('form')])
                ->id('form')
                ->livewireSubmitHandler('save')
                ->footer([
                    Actions::make([
                        Action::make('save')
                            ->label('Simpan Pengaturan Bahasa')
                            ->icon('heroicon-o-check')
                            ->submit('save')
                            ->keyBindings(['mod+s']),
                    ]),
                ]),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([

                Section::make('Bahasa Default Situs')
                    ->description('Bahasa yang digunakan saat pengunjung pertama kali membuka situs tanpa preferensi tersimpan.')
                    ->icon('heroicon-o-globe-alt')
                    ->schema([
                        Select::make('default_locale')
                            ->label('Bahasa Default')
                            ->options([
                                'id' => '🇮🇩 Bahasa Indonesia',
                                'en' => '🇬🇧 English',
                                'ar' => '🇸🇦 العربية (RTL)',
                            ])
                            ->required()
                            ->native(false)
                            ->helperText('Pengunjung dapat mengubah bahasa kapan saja melalui pemilih bahasa di navbar.'),
                    ]),

                Section::make('Bahasa yang Diaktifkan')
                    ->description('Pilih bahasa yang tersedia untuk pengunjung situs. Minimal satu bahasa harus aktif.')
                    ->icon('heroicon-o-check-circle')
                    ->columns(3)
                    ->schema([
                        Toggle::make('lang_id')
                            ->label('🇮🇩 Bahasa Indonesia')
                            ->helperText('Aktifkan bahasa Indonesia')
                            ->default(true),
                        Toggle::make('lang_en')
                            ->label('🇬🇧 English')
                            ->helperText('Enable English language'),
                        Toggle::make('lang_ar')
                            ->label('🇸🇦 العربية')
                            ->helperText('Layout RTL (kanan ke kiri)'),
                    ]),

                Section::make('Status Kelengkapan Terjemahan')
                    ->description('Persentase kunci terjemahan yang telah diisi untuk setiap bahasa.')
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        Placeholder::make('translation_completeness')
                            ->label('')
                            ->content(function (): HtmlString {
                                return self::buildCompletenessHtml();
                            }),
                    ]),

                Section::make('Cara Menggunakan Terjemahan')
                    ->description('Panduan singkat untuk developer.')
                    ->icon('heroicon-o-code-bracket')
                    ->collapsed()
                    ->schema([
                        Placeholder::make('dev_guide')
                            ->label('')
                            ->content(new HtmlString('
                                <div class="text-sm space-y-2 text-gray-600">
                                    <p>Gunakan helper <code class="bg-gray-100 px-1 rounded font-mono">__(&apos;site.key&apos;)</code> di Blade untuk menampilkan teks yang bisa diterjemahkan.</p>
                                    <p>File terjemahan ada di <code class="bg-gray-100 px-1 rounded font-mono">lang/{locale}/site.php</code>.</p>
                                    <p>Contoh: <code class="bg-gray-100 px-1 rounded font-mono">{{ __(\'site.submit_manuscript\') }}</code> → "Kirim Naskah" atau "Submit Manuscript"</p>
                                    <p>Bahasa Arab menggunakan arah RTL — layout otomatis menyesuaikan saat bahasa Arab dipilih.</p>
                                </div>
                            ')),
                    ]),
            ]);
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $available = [];
        if ($data['lang_id'] ?? false) $available[] = 'id';
        if ($data['lang_en'] ?? false) $available[] = 'en';
        if ($data['lang_ar'] ?? false) $available[] = 'ar';

        if (empty($available)) {
            Notification::make()
                ->title('Gagal menyimpan')
                ->body('Minimal satu bahasa harus diaktifkan.')
                ->danger()
                ->send();
            return;
        }

        if (!in_array($data['default_locale'], $available)) {
            Notification::make()
                ->title('Konfigurasi tidak valid')
                ->body('Bahasa default harus termasuk dalam daftar bahasa yang diaktifkan.')
                ->danger()
                ->send();
            return;
        }

        Setting::set('language.default', $data['default_locale']);
        Setting::set('language.available', json_encode($available));

        Cache::forget('setting.language.default');
        Cache::forget('setting.language.available');

        Notification::make()
            ->title('Pengaturan bahasa berhasil disimpan')
            ->body('Perubahan akan diterapkan untuk semua sesi pengunjung baru.')
            ->success()
            ->send();
    }

    private static function buildCompletenessHtml(): HtmlString
    {
        $langs = [
            'id' => ['name' => 'Bahasa Indonesia', 'flag' => '🇮🇩'],
            'en' => ['name' => 'English',           'flag' => '🇬🇧'],
            'ar' => ['name' => 'العربية',           'flag' => '🇸🇦'],
        ];

        $sourceKeys = trans('site', [], 'id');
        $sourceCount = is_array($sourceKeys) ? count($sourceKeys) : 0;

        $html = '<div class="space-y-4">';

        foreach ($langs as $code => $lang) {
            $keys = trans('site', [], $code);
            $count = is_array($keys) ? count($keys) : 0;
            $percent = $sourceCount > 0 ? (int) round($count / $sourceCount * 100) : 0;

            if ($percent >= 90) {
                $barColor = '#22c55e'; $badge = 'Lengkap'; $badgeClass = 'background:#dcfce7;color:#15803d;';
            } elseif ($percent >= 50) {
                $barColor = '#f59e0b'; $badge = 'Sebagian'; $badgeClass = 'background:#fef3c7;color:#b45309;';
            } else {
                $barColor = '#ef4444'; $badge = 'Belum lengkap'; $badgeClass = 'background:#fee2e2;color:#b91c1c;';
            }

            $html .= "
                <div style='display:flex;align-items:center;gap:12px;padding:10px 0;border-bottom:1px solid #f1f5f9;'>
                    <span style='font-size:1.4rem;'>{$lang['flag']}</span>
                    <div style='min-width:140px;'>
                        <div style='font-weight:600;font-size:.875rem;color:#0f172a;'>{$lang['name']}</div>
                        <div style='font-size:.75rem;color:#64748b;'>{$count} / {$sourceCount} kunci</div>
                    </div>
                    <div style='flex:1;height:8px;background:#e2e8f0;border-radius:9999px;overflow:hidden;'>
                        <div style='height:100%;width:{$percent}%;background:{$barColor};border-radius:9999px;transition:width .3s;'></div>
                    </div>
                    <span style='font-size:.8rem;font-weight:700;color:#334155;min-width:40px;text-align:right;'>{$percent}%</span>
                    <span style='padding:2px 8px;border-radius:9999px;font-size:.72rem;font-weight:600;{$badgeClass}'>{$badge}</span>
                </div>";
        }

        $html .= '</div>';

        return new HtmlString($html);
    }
}
