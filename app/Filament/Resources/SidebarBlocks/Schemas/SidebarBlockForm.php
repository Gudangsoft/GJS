<?php

namespace App\Filament\Resources\SidebarBlocks\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Get;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SidebarBlockForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            Grid::make(2)->schema([

                Select::make('journal_id')
                    ->label('Jurnal')
                    ->relationship('journal', 'name')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->columnSpan(1),

                Select::make('type')
                    ->label('Jenis Blok')
                    ->required()
                    ->live()
                    ->options([
                        'journal_info'     => '📋 Informasi Jurnal',
                        'accreditation'    => '🏅 Akreditasi & Indeksasi',
                        'submission'       => '📝 Kirim Naskah',
                        'article_template' => '📄 Template Artikel',
                        'statistics'       => '📊 Statistik Jurnal',
                        'focus_scope'      => '🎯 Fokus & Ruang Lingkup',
                        'custom_html'      => '🔧 Konten Bebas (HTML)',
                    ])
                    ->columnSpan(1),

                TextInput::make('title')
                    ->label('Judul Blok (opsional)')
                    ->placeholder('Biarkan kosong untuk judul default')
                    ->maxLength(100)
                    ->columnSpan(1),

                TextInput::make('sort_order')
                    ->label('Urutan Tampil')
                    ->numeric()
                    ->default(0)
                    ->minValue(0)
                    ->helperText('Angka kecil tampil lebih atas')
                    ->columnSpan(1),

                Toggle::make('enabled')
                    ->label('Aktif / Tampilkan di Sidebar')
                    ->default(true)
                    ->inline(false)
                    ->columnSpan(2),

            ]),

            /* ── accreditation ───────────────────────────────────── */
            Section::make('Pengaturan: Akreditasi & Indeksasi')
                ->description('Data diambil otomatis dari profil jurnal. Centang yang ingin ditampilkan.')
                ->visible(fn (Get $get) => $get('type') === 'accreditation')
                ->schema([
                    Grid::make(2)->schema([
                        Toggle::make('settings.show_sinta')
                            ->label('Tampilkan Badge SINTA')
                            ->default(true)->inline(false),
                        Toggle::make('settings.show_accreditation_no')
                            ->label('Tampilkan No. SK Akreditasi')
                            ->default(true)->inline(false),
                        Toggle::make('settings.show_doaj')
                            ->label('Tampilkan Badge DOAJ')
                            ->default(false)->inline(false),
                        Toggle::make('settings.show_garuda')
                            ->label('Tampilkan Garuda / Portal Ristek')
                            ->default(true)->inline(false),
                        Toggle::make('settings.show_google_scholar')
                            ->label('Tampilkan Google Scholar')
                            ->default(true)->inline(false),
                        Toggle::make('settings.show_sinta_score')
                            ->label('Tampilkan Skor SINTA')
                            ->default(true)->inline(false),
                    ]),
                    TextInput::make('settings.sinta_url_override')
                        ->label('URL SINTA (opsional, override otomatis)')
                        ->url()
                        ->placeholder('https://sinta.kemdikbud.go.id/journals/profile/...')
                        ->helperText('Kosongkan = URL digenerate otomatis dari SINTA ID jurnal'),
                    TextInput::make('settings.custom_indexes')
                        ->label('Indeks Tambahan (pisah koma)')
                        ->placeholder('Scopus, Web of Science, Dimensions')
                        ->helperText('Ditampilkan sebagai daftar chip di bawah badge utama'),
                ]),

            /* ── journal_info ─────────────────────────────────────── */
            Section::make('Pengaturan: Informasi Jurnal')
                ->description('Pilih field yang ditampilkan dari data jurnal.')
                ->visible(fn (Get $get) => $get('type') === 'journal_info')
                ->schema([
                    Grid::make(2)->schema([
                        Toggle::make('settings.show_issn_print')
                            ->label('Tampilkan ISSN Cetak')
                            ->default(true)->inline(false),
                        Toggle::make('settings.show_issn_online')
                            ->label('Tampilkan e-ISSN / ISSN Online')
                            ->default(true)->inline(false),
                        Toggle::make('settings.show_publisher')
                            ->label('Tampilkan Penerbit')
                            ->default(true)->inline(false),
                        Toggle::make('settings.show_doi_prefix')
                            ->label('Tampilkan Prefiks DOI')
                            ->default(false)->inline(false),
                        Toggle::make('settings.show_frequency')
                            ->label('Tampilkan Frekuensi Terbit')
                            ->default(false)->inline(false),
                        Toggle::make('settings.show_review_mode')
                            ->label('Tampilkan Mode Peer Review')
                            ->default(true)->inline(false),
                    ]),
                    TextInput::make('settings.frequency_text')
                        ->label('Teks Frekuensi Terbit')
                        ->placeholder('contoh: 2 kali setahun (Juni & Desember)')
                        ->visible(fn (Get $get) => (bool) $get('settings.show_frequency')),
                ]),

            /* ── submission ───────────────────────────────────────── */
            Section::make('Pengaturan: Kirim Naskah')
                ->visible(fn (Get $get) => $get('type') === 'submission')
                ->schema([
                    Textarea::make('settings.call_text')
                        ->label('Teks Ajakan (Call for Papers)')
                        ->rows(4)
                        ->placeholder('Jurnal kami membuka penerimaan naskah secara berkala...')
                        ->columnSpanFull(),
                    Grid::make(2)->schema([
                        TextInput::make('settings.button_label')
                            ->label('Label Tombol')
                            ->default('Kirim Naskah')
                            ->placeholder('Kirim Naskah'),
                        TextInput::make('settings.button_url')
                            ->label('URL Tujuan (opsional)')
                            ->url()
                            ->placeholder('Kosongkan = otomatis ke halaman kirim'),
                    ]),
                ]),

            /* ── article_template ─────────────────────────────────── */
            Section::make('Pengaturan: Template Artikel')
                ->visible(fn (Get $get) => $get('type') === 'article_template')
                ->schema([
                    Textarea::make('settings.description')
                        ->label('Deskripsi Singkat')
                        ->rows(2)
                        ->placeholder('Gunakan template berikut untuk mempersiapkan naskah Anda.')
                        ->columnSpanFull(),
                    Grid::make(2)->schema([
                        FileUpload::make('settings.file_docx')
                            ->label('File Template (.docx)')
                            ->disk('public')
                            ->directory('journals/templates')
                            ->acceptedFileTypes([
                                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                                'application/msword',
                            ])
                            ->maxSize(10240)
                            ->helperText('Format Word (.doc/.docx), maks. 10 MB'),
                        FileUpload::make('settings.file_pdf')
                            ->label('File Panduan (.pdf, opsional)')
                            ->disk('public')
                            ->directory('journals/templates')
                            ->acceptedFileTypes(['application/pdf'])
                            ->maxSize(10240)
                            ->helperText('Format PDF, maks. 10 MB'),
                    ]),
                    Grid::make(2)->schema([
                        TextInput::make('settings.label_docx')
                            ->label('Label Tombol DOCX')
                            ->default('Unduh Template (DOCX)'),
                        TextInput::make('settings.label_pdf')
                            ->label('Label Tombol PDF')
                            ->default('Unduh Panduan (PDF)'),
                    ]),
                ]),

            /* ── statistics ───────────────────────────────────────── */
            Section::make('Pengaturan: Statistik')
                ->visible(fn (Get $get) => $get('type') === 'statistics')
                ->schema([
                    Grid::make(2)->schema([
                        Toggle::make('settings.show_articles')
                            ->label('Tampilkan Total Artikel')
                            ->default(true)->inline(false),
                        Toggle::make('settings.show_issues')
                            ->label('Tampilkan Total Terbitan')
                            ->default(true)->inline(false),
                        Toggle::make('settings.show_views')
                            ->label('Tampilkan Total Tayangan')
                            ->default(false)->inline(false),
                        Toggle::make('settings.show_downloads')
                            ->label('Tampilkan Total Unduhan')
                            ->default(false)->inline(false),
                        Toggle::make('settings.show_citations')
                            ->label('Tampilkan Total Sitasi')
                            ->default(false)->inline(false),
                    ]),
                ]),

            /* ── focus_scope ──────────────────────────────────────── */
            Section::make('Pengaturan: Fokus & Ruang Lingkup')
                ->description('Menampilkan Fokus & Ruang Lingkup dari data jurnal. Anda dapat menambahkan teks tambahan di bawah.')
                ->visible(fn (Get $get) => $get('type') === 'focus_scope')
                ->schema([
                    RichEditor::make('settings.extra_text')
                        ->label('Teks Tambahan (opsional, muncul di bawah teks jurnal)')
                        ->toolbarButtons(['bold', 'italic', 'bulletList', 'orderedList', 'link'])
                        ->columnSpanFull(),
                ]),

            /* ── custom_html ──────────────────────────────────────── */
            Section::make('Pengaturan: Konten Bebas')
                ->visible(fn (Get $get) => $get('type') === 'custom_html')
                ->schema([
                    RichEditor::make('settings.html')
                        ->label('Konten HTML')
                        ->toolbarButtons([
                            'bold', 'italic', 'underline', 'strike',
                            'bulletList', 'orderedList', 'link',
                            'h2', 'h3', 'blockquote',
                        ])
                        ->columnSpanFull(),
                ]),

        ]);
    }
}
