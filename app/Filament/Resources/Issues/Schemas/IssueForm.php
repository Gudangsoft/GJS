<?php

namespace App\Filament\Resources\Issues\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class IssueForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Identitas Issue')->schema([
                    Grid::make(3)->schema([
                        Select::make('journal_id')
                            ->label('Jurnal')
                            ->relationship('journal', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        TextInput::make('volume')
                            ->label('Volume')
                            ->numeric()
                            ->minValue(1),
                        TextInput::make('number')
                            ->label('Nomor')
                            ->numeric()
                            ->minValue(1),
                        TextInput::make('year')
                            ->label('Tahun')
                            ->numeric()
                            ->minValue(1900)
                            ->maxValue(2100)
                            ->default(now()->year),
                        TextInput::make('title')
                            ->label('Judul Issue (Opsional)')
                            ->maxLength(255)
                            ->placeholder('Misal: Edisi Khusus Kesehatan')
                            ->columnSpan(2),
                    ]),
                    Textarea::make('description')
                        ->label('Deskripsi Issue')
                        ->rows(3)
                        ->columnSpanFull(),
                ]),

                Section::make('Cover & Media')->schema([
                    Grid::make(2)->schema([
                        FileUpload::make('cover_image')
                            ->label('Gambar Cover Issue')
                            ->image()
                            ->disk('public')
                            ->directory('issues/covers')
                            ->visibility('public'),
                        TextInput::make('cover_image_alt_text')
                            ->label('Alt Text Cover')
                            ->maxLength(255)
                            ->helperText('Teks alternatif untuk aksesibilitas'),
                    ]),
                ])->collapsible(),

                Section::make('Pengaturan Tampilan Label')->schema([
                    Grid::make(4)->schema([
                        Toggle::make('show_volume')
                            ->label('Tampilkan Volume')
                            ->default(true)
                            ->inline(false),
                        Toggle::make('show_number')
                            ->label('Tampilkan Nomor')
                            ->default(true)
                            ->inline(false),
                        Toggle::make('show_year')
                            ->label('Tampilkan Tahun')
                            ->default(true)
                            ->inline(false),
                        Toggle::make('show_title')
                            ->label('Tampilkan Judul')
                            ->default(false)
                            ->inline(false),
                    ]),
                ])->collapsible(),

                Section::make('Status Publikasi')->schema([
                    Grid::make(2)->schema([
                        Select::make('access_status')
                            ->label('Hak Akses')
                            ->options([
                                'open'         => 'Open Access',
                                'subscription' => 'Berlangganan',
                            ])
                            ->default('open')
                            ->required(),
                        TextInput::make('doi')
                            ->label('DOI Issue')
                            ->placeholder('10.xxxx/xxxxxx')
                            ->maxLength(255),
                        DateTimePicker::make('date_published')
                            ->label('Tanggal Terbit')
                            ->nullable(),
                        DateTimePicker::make('date_notified')
                            ->label('Tanggal Notifikasi Terbit')
                            ->nullable(),
                    ]),
                    Grid::make(2)->schema([
                        Toggle::make('published')
                            ->label('Terbitkan Issue')
                            ->helperText('Issue akan tampil di halaman publik')
                            ->inline(false),
                        Toggle::make('current')
                            ->label('Issue Terkini')
                            ->helperText('Tampilkan sebagai issue terbaru/aktif')
                            ->inline(false),
                    ]),
                ]),
            ]);
    }
}
