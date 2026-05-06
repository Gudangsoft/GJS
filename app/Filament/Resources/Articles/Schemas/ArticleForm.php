<?php

namespace App\Filament\Resources\Articles\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section as SchemaSection;
use Filament\Schemas\Schema;

class ArticleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            SchemaSection::make('Penerbitan')->schema([
                Grid::make(2)->schema([
                    Select::make('issue_id')
                        ->label('Terbitan (Issue)')
                        ->relationship('issue', 'volume')
                        ->getOptionLabelFromRecordUsing(fn ($record) =>
                            "Vol. {$record->volume} No. {$record->number} ({$record->year})"
                        )
                        ->searchable()
                        ->preload()
                        ->required(),

                    Select::make('section_id')
                        ->label('Seksi')
                        ->relationship('section', 'title')
                        ->searchable()
                        ->preload(),

                    TextInput::make('pages')
                        ->label('Halaman')
                        ->placeholder('1-15 atau 100-120'),

                    TextInput::make('sequence')
                        ->label('Urutan dalam Terbitan')
                        ->numeric()
                        ->default(0),

                    DatePicker::make('date_published')
                        ->label('Tanggal Terbit')
                        ->displayFormat('d M Y'),

                    Select::make('access_status')
                        ->label('Akses')
                        ->options([
                            'open'         => 'Open Access',
                            'subscription' => 'Berlangganan',
                            'embargo'      => 'Embargo',
                        ])
                        ->default('open')
                        ->required(),
                ]),
            ]),

            SchemaSection::make('DOI & Indeks')->schema([
                Grid::make(2)->schema([
                    TextInput::make('doi')
                        ->label('DOI')
                        ->placeholder('10.xxxxx/yyyyy')
                        ->helperText('Format: 10.prefix/suffix'),

                    Select::make('doi_status')
                        ->label('Status DOI')
                        ->options([
                            'unregistered' => 'Belum Didaftarkan',
                            'submitted'    => 'Disubmit ke Crossref',
                            'registered'   => 'Terdaftar',
                            'error'        => 'Error',
                        ])
                        ->default('unregistered'),
                ]),
            ]),

        ]);
    }
}
