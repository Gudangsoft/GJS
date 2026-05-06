<?php

namespace App\Filament\Resources\Journals\Schemas;

use App\Models\User;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class JournalForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make()->tabs([

                    Tab::make('Pengelola & Editor')->schema([
                        Select::make('managers')
                            ->label('Journal Manager')
                            ->options(
                                User::whereHas('roles', fn ($q) => $q->whereIn('name', ['journal_manager', 'super_admin']))
                                    ->orderBy('first_name')
                                    ->get()
                                    ->mapWithKeys(fn ($u) => [$u->id => $u->first_name . ' ' . $u->last_name . ' — ' . $u->email])
                            )
                            ->multiple()
                            ->searchable()
                            ->helperText('Pengguna dengan role journal_manager yang bertanggung jawab atas jurnal ini'),

                        Select::make('editors')
                            ->label('Editor Jurnal')
                            ->options(
                                User::whereHas('roles', fn ($q) => $q->whereIn('name', ['editor', 'journal_manager', 'super_admin']))
                                    ->orderBy('first_name')
                                    ->get()
                                    ->mapWithKeys(fn ($u) => [$u->id => $u->first_name . ' ' . $u->last_name . ' — ' . $u->email])
                            )
                            ->multiple()
                            ->searchable()
                            ->helperText('Editor yang ditugaskan khusus untuk jurnal ini'),
                    ]),

                    Tab::make('Informasi Dasar')->schema([
                        Grid::make(2)->schema([
                            TextInput::make('name')
                                ->label('Nama Jurnal')
                                ->required()
                                ->maxLength(255)
                                ->columnSpan(2),
                            TextInput::make('name_abbrev')
                                ->label('Singkatan Nama'),
                            TextInput::make('slug')
                                ->label('Slug (URL)')
                                ->required()
                                ->unique(ignoreRecord: true)
                                ->alphaDash()
                                ->helperText('Digunakan sebagai bagian dari URL jurnal'),
                            TextInput::make('issn_print')
                                ->label('ISSN Cetak')
                                ->mask('9999-9999')
                                ->placeholder('xxxx-xxxx'),
                            TextInput::make('issn_online')
                                ->label('ISSN Online (e-ISSN)')
                                ->mask('9999-9999')
                                ->placeholder('xxxx-xxxx'),
                            TextInput::make('publisher')
                                ->label('Penerbit')
                                ->maxLength(255),
                            Select::make('status')
                                ->label('Status')
                                ->options([
                                    'active'   => 'Aktif',
                                    'inactive' => 'Tidak Aktif',
                                    'archived' => 'Diarsipkan',
                                ])
                                ->default('active')
                                ->required(),
                            Toggle::make('enabled')
                                ->label('Jurnal Aktif / Ditampilkan')
                                ->default(true)
                                ->inline(false),
                        ]),

                        Section::make('Media & Gambar')->schema([
                            Grid::make(2)->schema([
                                FileUpload::make('logo')
                                    ->label('Logo Jurnal')
                                    ->image()
                                    ->disk('public')
                                    ->imageResizeMode('contain')
                                    ->imageCropAspectRatio('1:1')
                                    ->imageResizeTargetWidth('300')
                                    ->imageResizeTargetHeight('300')
                                    ->directory('journals/logos')
                                    ->visibility('public'),
                                FileUpload::make('cover_image')
                                    ->label('Gambar Cover')
                                    ->image()
                                    ->disk('public')
                                    ->directory('journals/covers')
                                    ->visibility('public'),
                            ]),
                        ])->collapsible(),
                    ]),

                    Tab::make('Kontak & Lokalisasi')->schema([
                        Grid::make(2)->schema([
                            TextInput::make('email')
                                ->label('Email Jurnal')
                                ->email()
                                ->maxLength(255),
                            TextInput::make('url')
                                ->label('Website Jurnal')
                                ->url()
                                ->maxLength(255),
                            Select::make('country')
                                ->label('Negara')
                                ->searchable()
                                ->options(self::getCountries()),
                            Select::make('timezone')
                                ->label('Zona Waktu')
                                ->searchable()
                                ->default('Asia/Jakarta')
                                ->options(self::getTimezones())
                                ->required(),
                            Select::make('primary_locale')
                                ->label('Bahasa Utama')
                                ->options([
                                    'id' => 'Bahasa Indonesia',
                                    'en' => 'English',
                                    'ar' => 'العربية',
                                ])
                                ->default('id')
                                ->required(),
                            TagsInput::make('supported_locales')
                                ->label('Bahasa yang Didukung')
                                ->suggestions(['id', 'en', 'ar'])
                                ->helperText('Kode bahasa, misal: id, en'),
                        ]),
                    ]),

                    Tab::make('Pengaturan Review')->schema([
                        Grid::make(2)->schema([
                            Select::make('review_mode')
                                ->label('Mode Peer Review')
                                ->options([
                                    'single_blind' => 'Single Blind',
                                    'double_blind' => 'Double Blind',
                                    'triple_blind' => 'Triple Blind',
                                    'open'         => 'Open Review',
                                ])
                                ->default('double_blind')
                                ->required(),
                            TextInput::make('num_weeks_per_review')
                                ->label('Durasi Review (minggu)')
                                ->numeric()
                                ->minValue(1)
                                ->maxValue(52)
                                ->default(4)
                                ->required(),
                            TextInput::make('num_weeks_per_response')
                                ->label('Durasi Konfirmasi Reviewer (minggu)')
                                ->numeric()
                                ->minValue(1)
                                ->maxValue(12)
                                ->default(3)
                                ->required(),
                        ]),
                        Grid::make(2)->schema([
                            Toggle::make('requires_author_competinginterests')
                                ->label('Wajib Deklarasi Konflik Kepentingan (Penulis)')
                                ->inline(false),
                            Toggle::make('requires_reviewer_competinginterests')
                                ->label('Wajib Deklarasi Konflik Kepentingan (Reviewer)')
                                ->inline(false),
                        ]),
                    ]),

                    Tab::make('Panduan & Kebijakan')->schema([
                        RichEditor::make('focus_scope')
                            ->label('Fokus & Ruang Lingkup')
                            ->toolbarButtons(['bold', 'italic', 'bulletList', 'orderedList', 'link', 'h2', 'h3'])
                            ->columnSpanFull(),
                        RichEditor::make('about_journal')
                            ->label('Tentang Jurnal')
                            ->toolbarButtons(['bold', 'italic', 'bulletList', 'orderedList', 'link', 'h2', 'h3'])
                            ->columnSpanFull(),
                        RichEditor::make('ethics_statement')
                            ->label('Pernyataan Etika')
                            ->toolbarButtons(['bold', 'italic', 'bulletList', 'orderedList', 'link'])
                            ->columnSpanFull(),
                        RichEditor::make('author_guidelines')
                            ->label('Panduan Penulis')
                            ->toolbarButtons(['bold', 'italic', 'bulletList', 'orderedList', 'link', 'h2', 'h3'])
                            ->columnSpanFull(),
                        RichEditor::make('reviewer_guidelines')
                            ->label('Panduan Reviewer')
                            ->toolbarButtons(['bold', 'italic', 'bulletList', 'orderedList', 'link'])
                            ->columnSpanFull(),
                        RichEditor::make('privacy_statement')
                            ->label('Pernyataan Privasi')
                            ->toolbarButtons(['bold', 'italic', 'bulletList', 'orderedList', 'link'])
                            ->columnSpanFull(),
                    ]),

                ])->columnSpanFull(),
            ]);
    }

    private static function getCountries(): array
    {
        return [
            'ID' => 'Indonesia', 'MY' => 'Malaysia', 'SG' => 'Singapura',
            'PH' => 'Filipina',  'TH' => 'Thailand',  'VN' => 'Vietnam',
            'US' => 'Amerika Serikat', 'GB' => 'Inggris', 'AU' => 'Australia',
            'SA' => 'Arab Saudi', 'AE' => 'Uni Emirat Arab', 'EG' => 'Mesir',
            'JP' => 'Jepang',    'CN' => 'China',      'IN' => 'India',
        ];
    }

    private static function getTimezones(): array
    {
        return [
            'Asia/Jakarta'    => 'WIB — Asia/Jakarta (UTC+7)',
            'Asia/Makassar'   => 'WITA — Asia/Makassar (UTC+8)',
            'Asia/Jayapura'   => 'WIT — Asia/Jayapura (UTC+9)',
            'Asia/Kuala_Lumpur' => 'MYT — Kuala Lumpur (UTC+8)',
            'Asia/Singapore'  => 'SGT — Singapore (UTC+8)',
            'Asia/Bangkok'    => 'ICT — Bangkok (UTC+7)',
            'UTC'             => 'UTC',
        ];
    }
}
