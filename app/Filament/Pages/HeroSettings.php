<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Actions\Action;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\HtmlString;

class HeroSettings extends Page
{
    protected static string|\BackedEnum|null $navigationIcon  = 'heroicon-o-sparkles';
    protected static ?string               $navigationLabel = 'Hero & Landing Page';
    protected static string|\UnitEnum|null $navigationGroup = 'Pengaturan';
    protected static ?int                  $navigationSort  = 20;
    protected static ?string               $title           = 'Pengaturan Hero & Landing Page';

    public ?array $data = [];

    public function mount(): void
    {
        $h = Setting::getGroup('hero');
        $siteName = Setting::get('brand.site_name', config('app.name'));

        $this->form->fill([
            // ── Hero Utama
            'badge_text'       => $h['badge_text']       ?? 'Open Access · Peer Reviewed · DOI Crossref',
            'title_line1'      => $h['title_line1']      ?? 'Publikasikan Riset Anda',
            'title_line2'      => $h['title_line2']      ?? 'Bersama ' . $siteName,
            'subtitle'         => $h['subtitle']         ?? 'Platform manajemen jurnal ilmiah Indonesia — dari submission, peer review dua arah, hingga penerbitan terindeks Crossref dan Google Scholar.',
            'cta1_text'        => $h['cta1_text']        ?? 'Jelajahi Jurnal',
            'cta2_guest_text'  => $h['cta2_guest_text']  ?? 'Daftar Gratis',
            'cta2_auth_text'   => $h['cta2_auth_text']   ?? 'Kirim Naskah',

            // ── Label Statistik
            'stat_journals_label' => $h['stat_journals_label'] ?? 'Jurnal Aktif',
            'stat_articles_label' => $h['stat_articles_label'] ?? 'Total Artikel',
            'stat_authors_label'  => $h['stat_authors_label']  ?? 'Peneliti',

            // ── Lencana Melayang
            'badge1_title'    => $h['badge1_title']    ?? 'Crossref DOI',
            'badge1_subtitle' => $h['badge1_subtitle'] ?? '10.xxxx/gjs.2026',
            'badge2_title'    => $h['badge2_title']    ?? 'Google Scholar',
            'badge2_subtitle' => $h['badge2_subtitle'] ?? 'Terindeks otomatis',
            'badge3_title'    => $h['badge3_title']    ?? 'Open Access',
            'badge3_subtitle' => $h['badge3_subtitle'] ?? 'Akses gratis selamanya',
            'badge4_title'    => $h['badge4_title']    ?? 'Peer Reviewed',
            'badge4_subtitle' => $h['badge4_subtitle'] ?? 'Double-blind review',
            'badge5_title'    => $h['badge5_title']    ?? 'SINTA',
            'badge5_subtitle' => $h['badge5_subtitle'] ?? 'Kemendikbud',

            // ── Trust Bar
            'trust_bar_label' => $h['trust_bar_label'] ?? 'Terindeks & Terdaftar di',
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
                            ->label('Simpan Pengaturan Hero')
                            ->icon('heroicon-o-check')
                            ->submit('save')
                            ->keyBindings(['mod+s']),
                        Action::make('preview')
                            ->label('Lihat Halaman Utama')
                            ->icon('heroicon-o-arrow-top-right-on-square')
                            ->color('gray')
                            ->url('/')
                            ->openUrlInNewTab(),
                    ]),
                ]),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([

                // ── Section 1: Hero Utama ─────────────────────────────────────
                Section::make('Hero Utama')
                    ->description('Teks utama yang tampil di bagian paling atas halaman beranda.')
                    ->icon('heroicon-o-sparkles')
                    ->schema([
                        TextInput::make('badge_text')
                            ->label('Teks Badge (atas judul)')
                            ->placeholder('Open Access · Peer Reviewed · DOI Crossref')
                            ->helperText('Badge kecil berwarna biru di atas judul hero.')
                            ->maxLength(100),

                        TextInput::make('title_line1')
                            ->label('Judul Baris 1')
                            ->placeholder('Publikasikan Riset Anda')
                            ->helperText('Teks putih di atas judul gradasi.')
                            ->maxLength(80)
                            ->required(),

                        TextInput::make('title_line2')
                            ->label('Judul Baris 2 (gradasi warna)')
                            ->placeholder('Bersama Go Journal System')
                            ->helperText('Teks dengan efek gradasi biru-ungu-hijau.')
                            ->maxLength(80)
                            ->required(),

                        Textarea::make('subtitle')
                            ->label('Deskripsi / Subtitle')
                            ->placeholder('Platform manajemen jurnal ilmiah...')
                            ->helperText('Paragraf singkat di bawah judul utama. Maks 300 karakter.')
                            ->rows(3)
                            ->maxLength(300)
                            ->required(),
                    ]),

                // ── Section 2: Tombol CTA ─────────────────────────────────────
                Section::make('Tombol CTA (Call to Action)')
                    ->description('Dua tombol utama di bagian hero.')
                    ->icon('heroicon-o-cursor-arrow-rays')
                    ->columns(3)
                    ->schema([
                        TextInput::make('cta1_text')
                            ->label('Tombol 1 (biru)')
                            ->placeholder('Jelajahi Jurnal')
                            ->helperText('Tombol primer — mengarah ke daftar jurnal.')
                            ->maxLength(40)
                            ->required(),

                        TextInput::make('cta2_guest_text')
                            ->label('Tombol 2 (tamu/belum login)')
                            ->placeholder('Daftar Gratis')
                            ->helperText('Mengarah ke halaman registrasi.')
                            ->maxLength(40),

                        TextInput::make('cta2_auth_text')
                            ->label('Tombol 2 (sudah login)')
                            ->placeholder('Kirim Naskah')
                            ->helperText('Mengarah ke halaman submission.')
                            ->maxLength(40),
                    ]),

                // ── Section 3: Statistik ──────────────────────────────────────
                Section::make('Label Statistik')
                    ->description('Label teks di bawah angka statistik. Angkanya dihitung otomatis dari database.')
                    ->icon('heroicon-o-chart-bar')
                    ->columns(3)
                    ->schema([
                        TextInput::make('stat_journals_label')
                            ->label('Label Stat 1 (biru)')
                            ->placeholder('Jurnal Aktif')
                            ->maxLength(30)
                            ->required(),

                        TextInput::make('stat_articles_label')
                            ->label('Label Stat 2 (hijau)')
                            ->placeholder('Total Artikel')
                            ->maxLength(30)
                            ->required(),

                        TextInput::make('stat_authors_label')
                            ->label('Label Stat 3 (oranye)')
                            ->placeholder('Peneliti')
                            ->maxLength(30)
                            ->required(),
                    ]),

                // ── Section 4: Lencana Melayang ───────────────────────────────
                Section::make('Lencana Melayang (Floating Badges)')
                    ->description('5 kartu kecil yang melayang di area visual kanan hero.')
                    ->icon('heroicon-o-tag')
                    ->columns(2)
                    ->schema([
                        Placeholder::make('badges_hint')
                            ->label('')
                            ->columnSpanFull()
                            ->content(new HtmlString('
                                <div style="display:flex;flex-wrap:wrap;gap:8px;margin-bottom:4px;">
                                    <span style="padding:3px 10px;border-radius:9999px;font-size:.72rem;font-weight:700;background:#fff0e6;color:#c2410c;">🔗 Badge 1 — kiri atas</span>
                                    <span style="padding:3px 10px;border-radius:9999px;font-size:.72rem;font-weight:700;background:#eff6ff;color:#1d4ed8;">📚 Badge 2 — kanan atas</span>
                                    <span style="padding:3px 10px;border-radius:9999px;font-size:.72rem;font-weight:700;background:#f0fdf4;color:#15803d;">🔓 Badge 3 — kiri bawah</span>
                                    <span style="padding:3px 10px;border-radius:9999px;font-size:.72rem;font-weight:700;background:#faf5ff;color:#7e22ce;">✅ Badge 4 — kanan bawah</span>
                                    <span style="padding:3px 10px;border-radius:9999px;font-size:.72rem;font-weight:700;background:#fff7ed;color:#c2410c;">🏛 Badge 5 — tengah kiri</span>
                                </div>
                            ')),

                        TextInput::make('badge1_title')
                            ->label('Badge 1 — Judul (merah/oranye)')
                            ->placeholder('Crossref DOI')
                            ->maxLength(40),
                        TextInput::make('badge1_subtitle')
                            ->label('Badge 1 — Subjudul')
                            ->placeholder('10.xxxx/gjs.2026')
                            ->maxLength(60),

                        TextInput::make('badge2_title')
                            ->label('Badge 2 — Judul (biru)')
                            ->placeholder('Google Scholar')
                            ->maxLength(40),
                        TextInput::make('badge2_subtitle')
                            ->label('Badge 2 — Subjudul')
                            ->placeholder('Terindeks otomatis')
                            ->maxLength(60),

                        TextInput::make('badge3_title')
                            ->label('Badge 3 — Judul (hijau)')
                            ->placeholder('Open Access')
                            ->maxLength(40),
                        TextInput::make('badge3_subtitle')
                            ->label('Badge 3 — Subjudul')
                            ->placeholder('Akses gratis selamanya')
                            ->maxLength(60),

                        TextInput::make('badge4_title')
                            ->label('Badge 4 — Judul (ungu)')
                            ->placeholder('Peer Reviewed')
                            ->maxLength(40),
                        TextInput::make('badge4_subtitle')
                            ->label('Badge 4 — Subjudul')
                            ->placeholder('Double-blind review')
                            ->maxLength(60),

                        TextInput::make('badge5_title')
                            ->label('Badge 5 — Judul (oranye)')
                            ->placeholder('SINTA')
                            ->maxLength(40),
                        TextInput::make('badge5_subtitle')
                            ->label('Badge 5 — Subjudul')
                            ->placeholder('Kemendikbud')
                            ->maxLength(60),
                    ]),

                // ── Section 5: Trust Bar ──────────────────────────────────────
                Section::make('Trust Bar')
                    ->description('Bar putih di bawah hero yang menampilkan daftar indeksasi.')
                    ->icon('heroicon-o-shield-check')
                    ->schema([
                        TextInput::make('trust_bar_label')
                            ->label('Label Trust Bar')
                            ->placeholder('Terindeks & Terdaftar di')
                            ->helperText('Teks kecil di sebelah kiri logo-logo indeksasi.')
                            ->maxLength(60),
                    ]),
            ]);
    }

    public function save(): void
    {
        $data = $this->form->getState();

        Setting::setGroup('hero', $data);

        // Bust the Setting cache for all hero keys
        foreach (array_keys($data) as $key) {
            Cache::forget("setting.hero.{$key}");
        }

        Notification::make()
            ->title('Hero berhasil diperbarui')
            ->body('Perubahan langsung tampil di halaman beranda.')
            ->success()
            ->send();
    }
}
