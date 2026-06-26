<?php

namespace App\Filament\Resources\Sections\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SectionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Identitas Seksi')->schema([
                    Grid::make(2)->schema([
                        Select::make('journal_id')
                            ->label('Jurnal')
                            ->relationship('journal', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        TextInput::make('abbrev')
                            ->label('Singkatan')
                            ->maxLength(16)
                            ->placeholder('Misal: ART'),
                        TextInput::make('title')
                            ->label('Nama Seksi')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(1),
                        TextInput::make('sequence')
                            ->label('Urutan Tampil')
                            ->numeric()
                            ->default(0)
                            ->minValue(0),
                    ]),
                    Textarea::make('policy')
                        ->label('Kebijakan Seksi')
                        ->rows(4)
                        ->columnSpanFull(),

                    Textarea::make('reviewer_guidelines')
                        ->label('Panduan Reviewer')
                        ->helperText('Petunjuk khusus untuk reviewer yang menilai naskah di seksi ini. Ditampilkan saat reviewer menerima tugas review.')
                        ->rows(5)
                        ->columnSpanFull(),
                ]),

                Section::make('Pengaturan')->schema([
                    Grid::make(3)->schema([
                        Toggle::make('is_inactive')
                            ->label('Seksi Tidak Aktif')
                            ->helperText('Sembunyikan dari pilihan submission')
                            ->inline(false),
                        Toggle::make('editor_restricted')
                            ->label('Hanya Editor yang Bisa Assign')
                            ->inline(false),
                        Toggle::make('submitter_restricted')
                            ->label('Hanya Penulis Terdaftar')
                            ->inline(false),
                        Toggle::make('hide_title')
                            ->label('Sembunyikan Judul Seksi di TOC')
                            ->inline(false),
                        Toggle::make('hide_author')
                            ->label('Sembunyikan Penulis di TOC')
                            ->inline(false),
                        Toggle::make('abstract_word_count')
                            ->label('Batasi Jumlah Kata Abstrak')
                            ->live()
                            ->inline(false),
                    ]),
                    TextInput::make('word_count')
                        ->label('Batas Kata Abstrak')
                        ->numeric()
                        ->minValue(50)
                        ->maxValue(1000)
                        ->visible(fn ($get) => $get('abstract_word_count')),
                ])->collapsible(),
            ]);
    }
}
