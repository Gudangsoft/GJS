<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Actions\Action;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Exceptions\Halt;

class BrandSettings extends Page
{
    protected static string|\BackedEnum|null $navigationIcon  = 'heroicon-o-paint-brush';
    protected static ?string               $navigationLabel = 'Brand & SEO';
    protected static string|\UnitEnum|null $navigationGroup = 'Pengaturan';
    protected static ?int                  $navigationSort  = 10;
    protected static ?string               $title           = 'Brand, Identitas & SEO';

    public ?array $data = [];

    public function mount(): void
    {
        $b = Setting::getGroup('brand');
        $s = Setting::getGroup('seo');

        $this->form->fill([
            // ── brand ──────────────────────────────────────────────────────────
            'site_name'        => $b['site_name']        ?? config('app.name', 'GJS'),
            'tagline'          => $b['tagline']          ?? '',
            'description'      => $b['description']      ?? '',
            'copyright'        => $b['copyright']        ?? '© ' . date('Y') . ' GJS. All rights reserved.',
            'contact_email'    => $b['contact_email']    ?? '',
            'contact_phone'    => $b['contact_phone']    ?? '',
            'contact_address'  => $b['contact_address']  ?? '',
            'logo'             => $b['logo']             ?? null,
            'favicon'          => $b['favicon']          ?? null,
            'og_image'         => $b['og_image']         ?? null,
            'primary_color'    => $b['primary_color']    ?? '#3b82f6',
            'social_twitter'   => $b['social_twitter']   ?? '',
            'social_facebook'  => $b['social_facebook']  ?? '',
            'social_linkedin'  => $b['social_linkedin']  ?? '',
            'social_instagram' => $b['social_instagram'] ?? '',
            'social_youtube'   => $b['social_youtube']   ?? '',
            'social_github'    => $b['social_github']    ?? '',

            // ── seo ────────────────────────────────────────────────────────────
            'meta_keywords'          => $s['meta_keywords']          ?? '',
            'meta_robots'            => $s['meta_robots']            ?? 'index,follow',
            'og_locale'              => $s['og_locale']              ?? 'id_ID',
            'twitter_card'           => $s['twitter_card']           ?? 'summary_large_image',
            'twitter_site'           => $s['twitter_site']           ?? '',
            'google_analytics_id'    => $s['google_analytics_id']    ?? '',
            'google_tag_manager'     => $s['google_tag_manager']     ?? '',
            'google_search_console'  => $s['google_search_console']  ?? '',
            'bing_verification'      => $s['bing_verification']      ?? '',
            'yandex_verification'    => $s['yandex_verification']    ?? '',

            // ── google scholar ─────────────────────────────────────────────────
            'scholar_enabled'                => ($s['scholar_enabled'] ?? '1') === '1',
            'scholar_publisher'              => $s['scholar_publisher']              ?? '',
            'scholar_language'               => $s['scholar_language']               ?? 'id',
            'scholar_repository_institution' => $s['scholar_repository_institution'] ?? '',
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
                            ->label('Simpan Semua Perubahan')
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
                Tabs::make('settings_tabs')->tabs([

                    // ── Tab 1: Identitas & Tampilan ───────────────────────────
                    Tab::make('Identitas & Tampilan')
                        ->icon('heroicon-o-identification')
                        ->schema([

                            Section::make('Identitas Utama')
                                ->description('Nama, tagline, dan deskripsi singkat platform.')
                                ->columns(2)
                                ->schema([
                                    TextInput::make('site_name')
                                        ->label('Nama Situs')
                                        ->required()->maxLength(100)
                                        ->placeholder('Go Journal System'),

                                    TextInput::make('tagline')
                                        ->label('Tagline / Slogan')
                                        ->maxLength(150)
                                        ->placeholder('Platform Manajemen Jurnal Ilmiah'),

                                    Textarea::make('description')
                                        ->label('Deskripsi Singkat')
                                        ->helperText('Digunakan sebagai meta description halaman utama (maks. 160 karakter).')
                                        ->rows(2)->maxLength(300)->columnSpanFull(),

                                    Textarea::make('copyright')
                                        ->label('Teks Copyright (footer)')
                                        ->rows(2)->columnSpanFull()
                                        ->placeholder('© 2026 GJS. All rights reserved.'),
                                ]),

                            Section::make('Logo & Gambar')
                                ->description('Aset visual utama situs.')
                                ->columns(3)
                                ->schema([
                                    FileUpload::make('logo')
                                        ->label('Logo Situs')
                                        ->image()->disk('public')->directory('brand')
                                        ->imagePreviewHeight('80')
                                        ->helperText('PNG transparan, 400×100 px'),

                                    FileUpload::make('favicon')
                                        ->label('Favicon')
                                        ->image()->disk('public')->directory('brand')
                                        ->acceptedFileTypes(['image/x-icon','image/png','image/svg+xml'])
                                        ->imagePreviewHeight('80')
                                        ->helperText('.ico / .png, 32×32 px'),

                                    FileUpload::make('og_image')
                                        ->label('OG / Social Share Image')
                                        ->image()->disk('public')->directory('brand')
                                        ->imagePreviewHeight('80')
                                        ->helperText('1200×630 px — tampil saat link dibagikan'),
                                ]),

                            Section::make('Warna Tema')
                                ->icon('heroicon-o-swatch')
                                ->columns(3)
                                ->schema([
                                    ColorPicker::make('primary_color')
                                        ->label('Warna Utama (Primary)')
                                        ->helperText('Warna tombol, link, dan aksen utama'),
                                ]),

                            Section::make('Kontak')
                                ->columns(2)
                                ->schema([
                                    TextInput::make('contact_email')->label('Email Kontak')
                                        ->email()->placeholder('info@gjs.ac.id'),
                                    TextInput::make('contact_phone')->label('Telepon / WA')
                                        ->tel()->placeholder('+62 811 234 5678'),
                                    Textarea::make('contact_address')->label('Alamat')
                                        ->rows(2)->columnSpanFull()
                                        ->placeholder('Jl. Pendidikan No. 1, Jakarta 10110'),
                                ]),

                            Section::make('Media Sosial')
                                ->columns(2)->collapsible()
                                ->schema([
                                    TextInput::make('social_twitter')->label('Twitter / X')->url(),
                                    TextInput::make('social_facebook')->label('Facebook')->url(),
                                    TextInput::make('social_linkedin')->label('LinkedIn')->url(),
                                    TextInput::make('social_instagram')->label('Instagram')->url(),
                                    TextInput::make('social_youtube')->label('YouTube')->url(),
                                    TextInput::make('social_github')->label('GitHub')->url(),
                                ]),
                        ]),

                    // ── Tab 2: SEO & Meta Tag ─────────────────────────────────
                    Tab::make('SEO & Meta Tag')
                        ->icon('heroicon-o-magnifying-glass')
                        ->schema([

                            Section::make('Meta Tag Dasar')
                                ->description('Mengontrol bagaimana halaman situs diindeks oleh mesin pencari.')
                                ->columns(2)
                                ->schema([
                                    Select::make('meta_robots')
                                        ->label('Robots Meta Tag (default)')
                                        ->options([
                                            'index,follow'     => 'index, follow — Izinkan crawl & indeks (direkomendasikan)',
                                            'noindex,follow'   => 'noindex, follow — Jangan indeks, boleh ikuti tautan',
                                            'index,nofollow'   => 'index, nofollow — Indeks halaman, jangan ikuti tautan',
                                            'noindex,nofollow' => 'noindex, nofollow — Blokir sepenuhnya',
                                        ])
                                        ->default('index,follow')
                                        ->native(false)
                                        ->helperText('Berlaku untuk semua halaman kecuali di-override per halaman'),

                                    Select::make('og_locale')
                                        ->label('OG Locale')
                                        ->options([
                                            'id_ID' => 'id_ID — Bahasa Indonesia',
                                            'en_US' => 'en_US — English (US)',
                                            'en_GB' => 'en_GB — English (UK)',
                                        ])
                                        ->default('id_ID')
                                        ->native(false),

                                    Textarea::make('meta_keywords')
                                        ->label('Meta Keywords (site-wide)')
                                        ->rows(2)
                                        ->columnSpanFull()
                                        ->helperText('Pisahkan dengan koma. Contoh: jurnal ilmiah, open access, peer review')
                                        ->placeholder('jurnal ilmiah, open access, peer review, Indonesia'),
                                ]),

                            Section::make('Twitter / X Card')
                                ->columns(2)
                                ->schema([
                                    Select::make('twitter_card')
                                        ->label('Jenis Twitter Card')
                                        ->options([
                                            'summary'             => 'summary — Thumbnail kecil',
                                            'summary_large_image' => 'summary_large_image — Gambar besar (direkomendasikan)',
                                        ])
                                        ->default('summary_large_image')
                                        ->native(false),

                                    TextInput::make('twitter_site')
                                        ->label('Twitter @handle Situs')
                                        ->placeholder('@gjsjournal')
                                        ->prefix('@'),
                                ]),

                            Section::make('Verifikasi Mesin Pencari')
                                ->description('Kode verifikasi dari masing-masing platform webmaster. Isi dengan nilai atribut content saja.')
                                ->columns(2)
                                ->schema([
                                    TextInput::make('google_search_console')
                                        ->label('Google Search Console')
                                        ->placeholder('xxxxxxxxxxxxxxxxxxxxxx')
                                        ->helperText('Isi content dari: <meta name="google-site-verification" content="...">'),

                                    TextInput::make('bing_verification')
                                        ->label('Bing Webmaster Tools')
                                        ->placeholder('xxxxxxxxxxxxxxxxxxxxxx')
                                        ->helperText('Isi content dari: <meta name="msvalidate.01" content="...">'),

                                    TextInput::make('yandex_verification')
                                        ->label('Yandex Webmaster')
                                        ->placeholder('xxxxxxxxxxxxxxxxxxxxxx')
                                        ->helperText('Isi content dari: <meta name="yandex-verification" content="...">'),
                                ]),

                            Section::make('Google Analytics & Tag Manager')
                                ->columns(2)
                                ->schema([
                                    TextInput::make('google_analytics_id')
                                        ->label('Google Analytics 4 — Measurement ID')
                                        ->placeholder('G-XXXXXXXXXX')
                                        ->helperText('Format: G-XXXXXXXXXX (GA4)'),

                                    TextInput::make('google_tag_manager')
                                        ->label('Google Tag Manager — Container ID')
                                        ->placeholder('GTM-XXXXXXX')
                                        ->helperText('Format: GTM-XXXXXXX (opsional, gunakan GTM atau GA langsung)'),
                                ]),
                        ]),

                    // ── Tab 3: Google Scholar ─────────────────────────────────
                    Tab::make('Google Scholar')
                        ->icon('heroicon-o-academic-cap')
                        ->schema([

                            Section::make('Pengindeksan Google Scholar')
                                ->description('Google Scholar menggunakan meta tag citation_* pada halaman artikel untuk mengindeks jurnal ilmiah. Aktifkan fitur ini agar artikel GJS muncul di Google Scholar.')
                                ->icon('heroicon-o-academic-cap')
                                ->schema([
                                    Toggle::make('scholar_enabled')
                                        ->label('Aktifkan Citation Meta Tag (Google Scholar)')
                                        ->helperText('Akan menambahkan meta tag citation_* pada setiap halaman artikel dan jurnal')
                                        ->default(true)
                                        ->inline(false),
                                ]),

                            Section::make('Informasi Penerbit (Publisher)')
                                ->description('Data institusi penerbit yang akan dimasukkan ke dalam meta tag citation_publisher.')
                                ->columns(2)
                                ->schema([
                                    TextInput::make('scholar_publisher')
                                        ->label('Nama Penerbit (citation_publisher)')
                                        ->placeholder('Universitas XYZ / Lembaga Penelitian ABC')
                                        ->helperText('Tampil di Google Scholar sebagai publisher'),

                                    TextInput::make('scholar_repository_institution')
                                        ->label('Institusi Repository')
                                        ->placeholder('Universitas XYZ')
                                        ->helperText('Untuk tag: <meta name="citation_repository_institution">'),

                                    Select::make('scholar_language')
                                        ->label('Bahasa Default Artikel (citation_language)')
                                        ->options([
                                            'id' => 'id — Bahasa Indonesia',
                                            'en' => 'en — English',
                                            'ar' => 'ar — Arabic',
                                            'fr' => 'fr — French',
                                            'de' => 'de — German',
                                        ])
                                        ->default('id')
                                        ->native(false)
                                        ->helperText('Digunakan jika bahasa spesifik artikel tidak tersedia'),
                                ]),

                            Section::make('Panduan Citation Meta Tag')
                                ->description('Referensi cepat meta tag yang akan di-generate secara otomatis pada halaman artikel.')
                                ->collapsed()
                                ->schema([
                                    \Filament\Forms\Components\Placeholder::make('scholar_guide')
                                        ->label('')
                                        ->content(new \Illuminate\Support\HtmlString('
                                            <div class="text-sm text-gray-600 space-y-1 font-mono bg-gray-50 rounded p-3 border">
                                                <p class="text-gray-500 mb-2">// Meta tag yang di-generate per halaman artikel:</p>
                                                <p>&lt;meta name="<b>citation_title</b>" content="Judul Artikel"&gt;</p>
                                                <p>&lt;meta name="<b>citation_author</b>" content="Nama Penulis"&gt; <span class="text-gray-400">(per penulis)</span></p>
                                                <p>&lt;meta name="<b>citation_journal_title</b>" content="Nama Jurnal"&gt;</p>
                                                <p>&lt;meta name="<b>citation_issn</b>" content="XXXX-XXXX"&gt;</p>
                                                <p>&lt;meta name="<b>citation_volume</b>" content="1"&gt;</p>
                                                <p>&lt;meta name="<b>citation_issue</b>" content="1"&gt;</p>
                                                <p>&lt;meta name="<b>citation_publication_date</b>" content="2026/01/01"&gt;</p>
                                                <p>&lt;meta name="<b>citation_doi</b>" content="10.XXXXX/..."&gt;</p>
                                                <p>&lt;meta name="<b>citation_pdf_url</b>" content="https://..."&gt;</p>
                                                <p>&lt;meta name="<b>citation_abstract_html_url</b>" content="https://..."&gt;</p>
                                                <p>&lt;meta name="<b>citation_publisher</b>" content="Nama Penerbit"&gt;</p>
                                                <p>&lt;meta name="<b>citation_language</b>" content="id"&gt;</p>
                                            </div>
                                        ')),
                                ]),
                        ]),

                ])->columnSpanFull(),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label('Simpan')
                ->icon('heroicon-o-check')
                ->action('save')
                ->keyBindings(['mod+s']),
        ];
    }

    public function save(): void
    {
        try {
            $data = $this->form->getState();
        } catch (Halt) {
            return;
        }

        // Pisahkan ke dua group
        $brandKeys = [
            'site_name','tagline','description','copyright',
            'contact_email','contact_phone','contact_address',
            'logo','favicon','og_image','primary_color',
            'social_twitter','social_facebook','social_linkedin',
            'social_instagram','social_youtube','social_github',
        ];
        $seoKeys = [
            'meta_keywords','meta_robots','og_locale','twitter_card','twitter_site',
            'google_analytics_id','google_tag_manager','google_search_console',
            'bing_verification','yandex_verification',
            'scholar_enabled','scholar_publisher','scholar_language','scholar_repository_institution',
        ];

        $brandData = array_intersect_key($data, array_flip($brandKeys));
        $seoData   = array_intersect_key($data, array_flip($seoKeys));

        $seoData['scholar_enabled'] = $seoData['scholar_enabled'] ? '1' : '0';

        Setting::setGroup('brand', $brandData);
        Setting::setGroup('seo',   $seoData);

        Notification::make()
            ->title('Pengaturan brand & SEO berhasil disimpan')
            ->success()
            ->send();
    }
}
