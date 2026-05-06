<?php

namespace App\Filament\Resources\Submissions\Schemas;

use App\Models\Journal;
use App\Models\Section;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section as SchemaSection;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class SubmissionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make()->tabs([

                    Tab::make('Metadata Naskah')->schema([
                        Grid::make(2)->schema([
                            Select::make('journal_id')
                                ->label('Jurnal')
                                ->relationship('journal', 'name')
                                ->required()
                                ->searchable()
                                ->preload()
                                ->live()
                                ->afterStateUpdated(fn ($set) => $set('section_id', null)),
                            Select::make('section_id')
                                ->label('Seksi / Rubrik')
                                ->options(fn ($get) => Section::where('journal_id', $get('journal_id'))
                                    ->where('is_inactive', false)
                                    ->pluck('title', 'id'))
                                ->searchable()
                                ->nullable(),
                        ]),

                        TextInput::make('title')
                            ->label('Judul Naskah')
                            ->required()
                            ->maxLength(1000)
                            ->columnSpanFull(),
                        TextInput::make('subtitle')
                            ->label('Sub-Judul')
                            ->maxLength(500)
                            ->columnSpanFull(),
                        RichEditor::make('abstract')
                            ->label('Abstrak')
                            ->toolbarButtons(['bold', 'italic', 'bulletList', 'orderedList', 'subscript', 'superscript'])
                            ->columnSpanFull(),
                        TagsInput::make('keywords')
                            ->label('Kata Kunci')
                            ->placeholder('Tambah kata kunci + Enter')
                            ->separator(',')
                            ->helperText('Maks. 6 kata kunci')
                            ->columnSpanFull(),

                        Grid::make(3)->schema([
                            TagsInput::make('disciplines')
                                ->label('Disiplin Ilmu')
                                ->separator(','),
                            TagsInput::make('subjects')
                                ->label('Subjek')
                                ->separator(','),
                            Select::make('locale')
                                ->label('Bahasa Naskah')
                                ->options(['id' => 'Bahasa Indonesia', 'en' => 'English', 'ar' => 'Arabic'])
                                ->default('id')
                                ->required(),
                        ]),

                        Textarea::make('competing_interests')
                            ->label('Pernyataan Konflik Kepentingan')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),

                    Tab::make('Status & Penugasan')->schema([
                        Grid::make(2)->schema([
                            Select::make('status')
                                ->label('Status Naskah')
                                ->options([
                                    'draft'             => 'Draft',
                                    'submitted'         => 'Submitted',
                                    'queued'            => 'Antrian Editor',
                                    'assigned'          => 'Ditugaskan ke Editor',
                                    'review'            => 'Dalam Review',
                                    'revision_required' => 'Revisi Diperlukan',
                                    'resubmit'          => 'Resubmit',
                                    'accepted'          => 'Diterima',
                                    'copyediting'       => 'Copyediting',
                                    'production'        => 'Produksi',
                                    'scheduled'         => 'Dijadwalkan Terbit',
                                    'published'         => 'Terbit',
                                    'declined'          => 'Ditolak',
                                    'archived'          => 'Diarsipkan',
                                ])
                                ->default('draft')
                                ->required()
                                ->native(false),
                            Select::make('user_id')
                                ->label('Pengirim (Author)')
                                ->relationship('submitter', 'email')
                                ->searchable()
                                ->required(),
                        ]),

                        Grid::make(2)->schema([
                            TextInput::make('doi')
                                ->label('DOI')
                                ->placeholder('10.xxxx/xxxxxx')
                                ->maxLength(255),
                            Select::make('submission_type')
                                ->label('Tipe Naskah')
                                ->options([
                                    'article'       => 'Artikel Penelitian',
                                    'review'        => 'Artikel Review',
                                    'short_report'  => 'Laporan Singkat',
                                    'letter'        => 'Surat ke Editor',
                                    'editorial'     => 'Editorial',
                                    'commentary'    => 'Komentar',
                                ])
                                ->default('article')
                                ->required(),
                            DateTimePicker::make('submitted_at')
                                ->label('Tanggal Submit')
                                ->nullable(),
                            Toggle::make('hide_author')
                                ->label('Sembunyikan Identitas Penulis')
                                ->helperText('Aktifkan untuk blind review')
                                ->inline(false),
                        ]),
                    ]),

                ])->columnSpanFull(),
            ]);
    }
}
