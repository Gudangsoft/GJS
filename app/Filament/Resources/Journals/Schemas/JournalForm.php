<?php

namespace App\Filament\Resources\Journals\Schemas;

use App\Models\User;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Placeholder;

class JournalForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Tabs::make()->tabs([

                // 1. MASTHEAD
                Tab::make('Masthead')->schema([

                    Section::make('Identitas Jurnal')->schema([
                        Grid::make(2)->schema([
                            TextInput::make('name')
                                ->label('Nama Jurnal')
                                ->required()->maxLength(255)->columnSpan(2),
                            TextInput::make('name_abbrev')
                                ->label('Singkatan / Akronim'),
                            TextInput::make('slug')
                                ->label('Slug (URL)')
                                ->required()->unique(ignoreRecord: true)->alphaDash()
                                ->prefix('journals/')
                                ->helperText('Huruf kecil, angka, strip saja'),
                            TextInput::make('issn_print')
                                ->label('ISSN Cetak')
                                ->mask('9999-9999')->placeholder('xxxx-xxxx'),
                            TextInput::make('issn_online')
                                ->label('e-ISSN (Online)')
                                ->mask('9999-9999')->placeholder('xxxx-xxxx'),
                            TextInput::make('publisher')
                                ->label('Penerbit / Institusi')->maxLength(255),
                            TextInput::make('publication_frequency')
                                ->label('Frekuensi Publikasi')
                                ->placeholder('3 kali per tahun (Maret, Juli, November)'),
                            TextInput::make('oai_identifier')
                                ->label('OAI Identifier')
                                ->placeholder('oai:journals.example.org:jiki')
                                ->helperText('Opsional - untuk protokol OAI-PMH'),
                        ]),
                    ]),

                    Section::make('Ringkasan & Tentang Jurnal')->schema([
                        Textarea::make('description')
                            ->label('Ringkasan Singkat')
                            ->rows(3)->helperText('Tampil di listing jurnal')->columnSpanFull(),
                        RichEditor::make('about_journal')
                            ->label('Tentang Jurnal')
                            ->toolbarButtons(['bold','italic','bulletList','orderedList','link','h2','h3'])
                            ->columnSpanFull(),
                    ])->collapsible(),

                    Section::make('Logo & Gambar')->schema([
                        Grid::make(3)->schema([
                            FileUpload::make('logo')
                                ->label('Logo Jurnal')->image()->disk('public')
                                ->imageResizeMode('contain')->imageCropAspectRatio('1:1')
                                ->imageResizeTargetWidth('300')->imageResizeTargetHeight('300')
                                ->directory('journals/logos')->visibility('public'),
                            FileUpload::make('cover_image')
                                ->label('Gambar Cover')->image()->disk('public')
                                ->directory('journals/covers')->visibility('public'),
                            FileUpload::make('favicon')
                                ->label('Favicon')->image()->disk('public')
                                ->imageResizeTargetWidth('64')->imageResizeTargetHeight('64')
                                ->directory('journals/favicons')->visibility('public'),
                        ]),
                        FileUpload::make('homepage_image')
                            ->label('Banner Halaman Utama')->image()->disk('public')
                            ->directory('journals/banners')->visibility('public')->columnSpanFull(),
                    ])->collapsible(),

                    Section::make('Status & Visibilitas')->schema([
                        Grid::make(3)->schema([
                            Select::make('status')
                                ->label('Status')
                                ->options(['active'=>'Aktif','inactive'=>'Tidak Aktif','archived'=>'Diarsipkan'])
                                ->default('active')->required(),
                            Toggle::make('enabled')
                                ->label('Tampilkan di Daftar Jurnal')
                                ->default(true)->inline(false),
                            Toggle::make('disable_submissions')
                                ->label('Tutup Penerimaan Naskah')
                                ->default(false)->inline(false),
                        ]),
                    ]),

                ]),

                // 2. KONTAK
                Tab::make('Kontak')->schema([

                    Section::make('Kontak Utama')->schema([
                        Grid::make(2)->schema([
                            TextInput::make('contact_name')
                                ->label('Nama Kontak Utama')->placeholder('Dr. Budi Santoso'),
                            TextInput::make('email')
                                ->label('Email Jurnal')->email()->placeholder('editor@jurnal.ac.id'),
                            TextInput::make('contact_phone')
                                ->label('Telepon / WhatsApp')->tel()->placeholder('+62 812 xxxx xxxx'),
                            TextInput::make('url')
                                ->label('Website Jurnal')->url()->placeholder('https://jurnal.univ.ac.id/jiki'),
                        ]),
                        Textarea::make('mailing_address')
                            ->label('Alamat Pos')->rows(3)
                            ->placeholder("Gedung A Lt. 2, Jl. Kampus No. 1\nKota 12345\nIndonesia")
                            ->columnSpanFull(),
                    ]),

                    Section::make('Dukungan Teknis')->schema([
                        Grid::make(2)->schema([
                            TextInput::make('tech_support_name')->label('Nama Petugas Teknis'),
                            TextInput::make('tech_support_email')->label('Email Teknis')->email(),
                        ]),
                    ])->collapsible(),

                    Section::make('Lokalisasi')->schema([
                        Grid::make(3)->schema([
                            Select::make('country')
                                ->label('Negara')->searchable()->options(self::getCountries()),
                            Select::make('timezone')
                                ->label('Zona Waktu')->searchable()
                                ->default('Asia/Jakarta')->options(self::getTimezones())->required(),
                            Select::make('primary_locale')
                                ->label('Bahasa Utama')
                                ->options(['id'=>'Bahasa Indonesia','en'=>'English','ar'=>'العربية'])
                                ->default('id')->required(),
                        ]),
                        TagsInput::make('supported_locales')
                            ->label('Bahasa Tambahan')
                            ->suggestions(['id','en','ar','zh','fr','de'])
                            ->helperText('Kode ISO 639-1, pisah dengan Enter')->columnSpanFull(),
                    ])->collapsible(),

                ]),

                // 3. TIM REDAKSI
                Tab::make('Tim Redaksi')->schema([

                    Section::make('Journal Manager')
                        ->description('Bertanggung jawab penuh atas pengelolaan jurnal.')->schema([
                        Select::make('managers')
                            ->label('Journal Manager')
                            ->options(
                                User::whereHas('roles', fn ($q) => $q->whereIn('name', ['journal_manager','super_admin']))
                                    ->orderBy('first_name')->get()
                                    ->mapWithKeys(fn ($u) => [$u->id => $u->first_name.' '.$u->last_name.' - '.$u->email])
                            )
                            ->multiple()->searchable(),
                    ]),

                    Section::make('Editor')
                        ->description('Mengelola alur kerja editorial dan proses review.')->schema([
                        Select::make('editors')
                            ->label('Editor Jurnal')
                            ->options(
                                User::whereHas('roles', fn ($q) => $q->whereIn('name', ['editor','journal_manager','super_admin']))
                                    ->orderBy('first_name')->get()
                                    ->mapWithKeys(fn ($u) => [$u->id => $u->first_name.' '.$u->last_name.' - '.$u->email])
                            )
                            ->multiple()->searchable(),
                    ]),

                ]),

                // 4. PENGIRIMAN
                Tab::make('Pengiriman')->schema([

                    Section::make('Status Penerimaan')->schema([
                        Toggle::make('disable_submissions')
                            ->label('Tutup Penerimaan Naskah Sementara')
                            ->helperText('Jika aktif, penulis tidak dapat mengajukan naskah baru')
                            ->inline(false),
                    ]),

                    Section::make('Panduan Penulis')->schema([
                        RichEditor::make('author_guidelines')
                            ->label('Panduan Penulisan & Pengiriman')
                            ->toolbarButtons(['bold','italic','underline','bulletList','orderedList','link','h2','h3','table'])
                            ->columnSpanFull(),
                    ])->collapsible(),

                    Section::make('Daftar Periksa Pengiriman')
                        ->description('Penulis wajib mencentang semua item sebelum submit.')->schema([
                        Repeater::make('submission_checklist')
                            ->label('')
                            ->schema([
                                TextInput::make('item')->label('Item')->required()
                                    ->placeholder('Naskah belum pernah diterbitkan di media lain'),
                            ])
                            ->addActionLabel('+ Tambah Item Checklist')
                            ->reorderable()->collapsible()->defaultItems(0)->columnSpanFull(),
                    ])->collapsible(),

                    Section::make('Pernyataan Hak Cipta & Privasi')->schema([
                        RichEditor::make('copyright_notice')
                            ->label('Pemberitahuan Hak Cipta')
                            ->toolbarButtons(['bold','italic','link'])
                            ->helperText('Ditampilkan saat penulis mengajukan naskah')->columnSpanFull(),
                        RichEditor::make('submission_acknowledgement')
                            ->label('Isi Email Konfirmasi Pengiriman')
                            ->toolbarButtons(['bold','italic','bulletList','link'])
                            ->helperText('Dikirim otomatis ke penulis setelah naskah berhasil diajukan')
                            ->columnSpanFull(),
                    ])->collapsible(),

                ]),

                // 5. REVIEW
                Tab::make('Review')->schema([

                    Section::make('Mode & Durasi Review')->schema([
                        Grid::make(3)->schema([
                            Select::make('review_mode')
                                ->label('Mode Peer Review')
                                ->options([
                                    'single_blind' => 'Single Blind',
                                    'double_blind' => 'Double Blind (Rekomendasi)',
                                    'triple_blind' => 'Triple Blind',
                                    'open'         => 'Open Review',
                                ])
                                ->default('double_blind')->required(),
                            TextInput::make('num_weeks_per_review')
                                ->label('Durasi Review (minggu)')
                                ->numeric()->minValue(1)->maxValue(52)->default(4),
                            TextInput::make('num_weeks_per_response')
                                ->label('Batas Konfirmasi Reviewer (minggu)')
                                ->numeric()->minValue(1)->maxValue(12)->default(3),
                        ]),
                    ]),

                    Section::make('Konflik Kepentingan')->schema([
                        Grid::make(2)->schema([
                            Toggle::make('requires_author_competinginterests')
                                ->label('Penulis Wajib Deklarasikan Konflik Kepentingan')->inline(false),
                            Toggle::make('requires_reviewer_competinginterests')
                                ->label('Reviewer Wajib Deklarasikan Konflik Kepentingan')->inline(false),
                        ]),
                    ]),

                    Section::make('Panduan Reviewer')->schema([
                        RichEditor::make('reviewer_guidelines')
                            ->label('Instruksi untuk Reviewer')
                            ->toolbarButtons(['bold','italic','bulletList','orderedList','link','h2','h3'])
                            ->columnSpanFull(),
                    ])->collapsible(),

                ]),

                // 6. DISTRIBUSI & LISENSI
                Tab::make('Distribusi')->schema([

                    Section::make('Lisensi Creative Commons')->schema([
                        Grid::make(2)->schema([
                            Select::make('license_type')
                                ->label('Jenis Lisensi')
                                ->options([
                                    'CC BY 4.0'       => 'CC BY 4.0 - Attribution',
                                    'CC BY-SA 4.0'    => 'CC BY-SA 4.0 - Attribution-ShareAlike',
                                    'CC BY-NC 4.0'    => 'CC BY-NC 4.0 - Attribution-NonCommercial',
                                    'CC BY-NC-SA 4.0' => 'CC BY-NC-SA 4.0 - AttributionNonCommercial-ShareAlike',
                                    'CC BY-ND 4.0'    => 'CC BY-ND 4.0 - Attribution-NoDerivatives',
                                    'CC BY-NC-ND 4.0' => 'CC BY-NC-ND 4.0 - NonCommercial-NoDerivatives',
                                    'Copyright'       => 'All Rights Reserved',
                                    'Public Domain'   => 'Public Domain / CC0',
                                ])
                                ->default('CC BY 4.0')->required(),
                            Select::make('copyright_holder')
                                ->label('Pemegang Hak Cipta')
                                ->options([
                                    'author'  => 'Penulis',
                                    'journal' => 'Jurnal / Penerbit',
                                    'other'   => 'Pihak Lain',
                                ])
                                ->default('author')->required(),
                        ]),
                        RichEditor::make('open_access_statement')
                            ->label('Pernyataan Open Access')
                            ->toolbarButtons(['bold','italic','link'])
                            ->columnSpanFull(),
                    ]),

                    Section::make('DOI (Digital Object Identifier)')->schema([
                        Grid::make(2)->schema([
                            TextInput::make('doi_prefix')
                                ->label('DOI Prefix')->placeholder('10.12345')
                                ->helperText('Diperoleh dari CrossRef'),
                            Select::make('doi_suffix_pattern')
                                ->label('Pola DOI Suffix')
                                ->options([
                                    'default'    => 'Default (id-artikel)',
                                    'manuscript' => 'Kode Naskah',
                                    'page'       => 'Halaman Artikel',
                                    'custom'     => 'Kustom',
                                ])
                                ->default('default'),
                        ]),
                    ])->collapsible(),

                ]),

                // 7. WEBSITE & TAMPILAN
                Tab::make('Website')->schema([

                    Section::make('Pengumuman')->schema([
                        Toggle::make('announcements_enabled')
                            ->label('Aktifkan Fitur Pengumuman')
                            ->default(true)->inline(false),
                        RichEditor::make('announcements_intro')
                            ->label('Pendahuluan Halaman Pengumuman')
                            ->toolbarButtons(['bold','italic','link'])->columnSpanFull(),
                    ]),

                    Section::make('HTML Kustom')->schema([
                        Textarea::make('custom_header_html')
                            ->label('HTML Header Kustom')->rows(4)
                            ->helperText('Disisipkan di <head> - untuk CSS/JS tambahan')->columnSpanFull(),
                        Textarea::make('custom_footer_html')
                            ->label('HTML Footer Kustom')->rows(4)
                            ->helperText('Disisipkan sebelum </body>')->columnSpanFull(),
                    ])->collapsible(),

                ]),

                // 8. AKREDITASI & INDEKSASI
                Tab::make('Akreditasi')->schema([

                    Section::make('SINTA')->schema([
                        Grid::make(2)->schema([
                            TextInput::make('sinta_id')
                                ->label('SINTA ID')->placeholder('23')
                                ->helperText('sinta.kemdikbud.go.id/journals/profile/{id}'),
                            Select::make('sinta_level')
                                ->label('Peringkat SINTA')
                                ->options([
                                    'S1'=>'SINTA 1 (Tertinggi)','S2'=>'SINTA 2',
                                    'S3'=>'SINTA 3','S4'=>'SINTA 4',
                                    'S5'=>'SINTA 5','S6'=>'SINTA 6',
                                ])
                                ->placeholder('- Belum Terakreditasi -')->nullable(),
                            TextInput::make('sinta_score')
                                ->label('Skor SINTA (keseluruhan)')
                                ->numeric()->step(0.01)->placeholder('0.00'),
                            TextInput::make('sinta_score_3yr')
                                ->label('Skor SINTA (3 tahun)')
                                ->numeric()->step(0.01)->placeholder('0.00'),
                        ]),
                    ]),

                    Section::make('Nomor SK Akreditasi')->schema([
                        Grid::make(2)->schema([
                            TextInput::make('accreditation_no')
                                ->label('Nomor SK')->placeholder('158/E/KPT/2021'),
                            TextInput::make('accreditation_period')
                                ->label('Periode Berlaku')->placeholder('2021-2025'),
                        ]),
                    ]),

                    Section::make('Basis Data Indeksasi')->schema([
                        Grid::make(2)->schema([
                            TextInput::make('doaj_id')
                                ->label('DOAJ ID / ISSN')->placeholder('abcdef1234567890')
                                ->helperText('doaj.org/toc/{id}'),
                            TextInput::make('garuda_id')
                                ->label('Garuda ID')->placeholder('IPI1234')
                                ->helperText('garuda.kemdikbud.go.id/journal/{id}'),
                        ]),
                    ])->collapsible(),

                ]),

                // 9. KEBIJAKAN & ETIKA
                Tab::make('Kebijakan & Etika')->schema([

                    Section::make('Fokus & Ruang Lingkup')->schema([
                        RichEditor::make('focus_scope')
                            ->label('Fokus & Ruang Lingkup Jurnal')
                            ->toolbarButtons(['bold','italic','bulletList','orderedList','link','h2','h3'])
                            ->columnSpanFull(),
                    ]),

                    Section::make('Etika Publikasi')->schema([
                        RichEditor::make('ethics_statement')
                            ->label('Pernyataan Etika')
                            ->toolbarButtons(['bold','italic','bulletList','orderedList','link'])
                            ->columnSpanFull(),
                    ])->collapsible(),

                    Section::make('Privasi')->schema([
                        RichEditor::make('privacy_statement')
                            ->label('Pernyataan Privasi')
                            ->toolbarButtons(['bold','italic','bulletList','link'])
                            ->columnSpanFull(),
                    ])->collapsible(),

                ]),

                // 10. BIAYA PUBLIKASI (APC)
                Tab::make('APC & Biaya')->schema([

                    Section::make('Article Processing Charge (APC)')
                        ->description('Biaya yang dikenakan kepada penulis untuk memproses dan menerbitkan artikel.')
                        ->schema([
                            Grid::make(3)->schema([
                                Toggle::make('apc_enabled')
                                    ->label('Aktifkan APC')
                                    ->helperText('Tampilkan info APC kepada penulis')
                                    ->default(false)->inline(false),
                                TextInput::make('apc_amount')
                                    ->label('Besaran APC')
                                    ->numeric()->minValue(0)->placeholder('0'),
                                Select::make('apc_currency')
                                    ->label('Mata Uang')
                                    ->options([
                                        'IDR' => 'IDR — Rupiah',
                                        'USD' => 'USD — US Dollar',
                                        'EUR' => 'EUR — Euro',
                                        'MYR' => 'MYR — Ringgit',
                                    ])
                                    ->default('IDR'),
                            ]),
                            Textarea::make('apc_waiver_policy')
                                ->label('Kebijakan Pengurangan / Waiver APC')
                                ->rows(3)
                                ->placeholder('Penulis dari negara berkembang dapat mengajukan keringanan biaya...')
                                ->columnSpanFull(),
                        ]),

                ]),

                // 11. INTEGRASI & API
                Tab::make('Integrasi')->schema([

                    Section::make('LOA — Letter of Acceptance')
                        ->description('Nama dan jabatan penandatangan dokumen LOA.')
                        ->icon('heroicon-o-document-check')
                        ->schema([
                            Grid::make(2)->schema([
                                TextInput::make('loa_signer_name')
                                    ->label('Nama Penandatangan')
                                    ->placeholder('Prof. Dr. Budi Santoso, M.T.'),
                                TextInput::make('loa_signer_title')
                                    ->label('Jabatan Penandatangan')
                                    ->placeholder('Ketua Editor / Editor-in-Chief'),
                            ]),
                        ]),

                    Section::make('WhatsApp Notification')
                        ->description('Notifikasi otomatis via WhatsApp API ke penulis dan editor.')
                        ->icon('heroicon-o-chat-bubble-left-right')
                        ->collapsible()
                        ->schema([
                            Grid::make(2)->schema([
                                TextInput::make('wa_contact')
                                    ->label('Nomor WA Kontak Jurnal')
                                    ->tel()->placeholder('+628123456789'),
                                TextInput::make('wa_sender_number')
                                    ->label('Nomor WA Pengirim (API)')
                                    ->tel()->placeholder('+628119876543'),
                            ]),
                            TextInput::make('wa_api_token')
                                ->label('WA API Token / API Key')
                                ->password()->revealable()
                                ->placeholder('Token dari Fonnte / WA Business API')
                                ->columnSpanFull(),
                        ]),

                    Section::make('Turnitin Plagiarism Check')
                        ->description('Integrasi cek plagiarisme via Turnitin API.')
                        ->icon('heroicon-o-shield-check')
                        ->collapsible()
                        ->schema([
                            Grid::make(2)->schema([
                                TextInput::make('turnitin_account_id')
                                    ->label('Turnitin Account ID')
                                    ->placeholder('abc123'),
                                TextInput::make('turnitin_api_key')
                                    ->label('Turnitin API Key')
                                    ->password()->revealable()
                                    ->placeholder('xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx'),
                            ]),
                        ]),

                ]),

            ])->columnSpanFull(),
        ]);
    }

    private static function getCountries(): array
    {
        return [
            'ID' => 'Indonesia',       'MY' => 'Malaysia',
            'SG' => 'Singapura',       'PH' => 'Filipina',
            'TH' => 'Thailand',        'VN' => 'Vietnam',
            'US' => 'Amerika Serikat', 'GB' => 'Inggris',
            'AU' => 'Australia',       'SA' => 'Arab Saudi',
            'AE' => 'Uni Emirat Arab', 'EG' => 'Mesir',
            'JP' => 'Jepang',          'CN' => 'China',
            'IN' => 'India',           'NL' => 'Belanda',
            'DE' => 'Jerman',          'FR' => 'Prancis',
        ];
    }

    private static function getTimezones(): array
    {
        return [
            'Asia/Jakarta'      => 'WIB - Asia/Jakarta (UTC+7)',
            'Asia/Makassar'     => 'WITA - Asia/Makassar (UTC+8)',
            'Asia/Jayapura'     => 'WIT - Asia/Jayapura (UTC+9)',
            'Asia/Kuala_Lumpur' => 'MYT - Kuala Lumpur (UTC+8)',
            'Asia/Singapore'    => 'SGT - Singapore (UTC+8)',
            'Asia/Bangkok'      => 'ICT - Bangkok (UTC+7)',
            'UTC'               => 'UTC',
        ];
    }
}
