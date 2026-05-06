<?php

namespace App\Filament\Resources\Articles\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ArticlesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('submission.title')
                    ->label('Judul Artikel')
                    ->searchable()
                    ->sortable()
                    ->limit(55)
                    ->tooltip(fn ($record) => $record->submission?->title),

                TextColumn::make('journal.name_abbrev')
                    ->label('Jurnal')
                    ->badge()
                    ->color('info')
                    ->sortable(),

                TextColumn::make('issue.volume')
                    ->label('Terbitan')
                    ->formatStateUsing(fn ($state, $record) =>
                        $record->issue ? "Vol.{$record->issue->volume} No.{$record->issue->number} ({$record->issue->year})" : '—'
                    )
                    ->placeholder('—'),

                TextColumn::make('section.title')
                    ->label('Seksi')
                    ->badge()
                    ->color('gray')
                    ->placeholder('—'),

                TextColumn::make('pages')
                    ->label('Hal.')
                    ->placeholder('—'),

                TextColumn::make('doi')
                    ->label('DOI')
                    ->placeholder('—')
                    ->copyable()
                    ->fontFamily('mono')
                    ->limit(30),

                TextColumn::make('doi_status')
                    ->label('DOI Status')
                    ->badge()
                    ->colors([
                        'gray'    => 'unregistered',
                        'warning' => 'submitted',
                        'success' => 'registered',
                        'danger'  => 'error',
                    ])
                    ->formatStateUsing(fn ($state) => match($state) {
                        'unregistered' => 'Belum',
                        'submitted'    => 'Disubmit',
                        'registered'   => 'Terdaftar',
                        'error'        => 'Error',
                        default        => $state,
                    }),

                TextColumn::make('date_published')
                    ->label('Terbit')
                    ->date('d M Y')
                    ->sortable()
                    ->placeholder('—'),

                TextColumn::make('views')
                    ->label('Views')
                    ->numeric()
                    ->sortable()
                    ->alignRight(),
            ])
            ->defaultSort('date_published', 'desc')
            ->filters([
                SelectFilter::make('journal_id')
                    ->label('Jurnal')
                    ->relationship('journal', 'name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('doi_status')
                    ->label('Status DOI')
                    ->options([
                        'unregistered' => 'Belum Didaftarkan',
                        'submitted'    => 'Disubmit',
                        'registered'   => 'Terdaftar',
                        'error'        => 'Error',
                    ]),

                SelectFilter::make('access_status')
                    ->label('Akses')
                    ->options([
                        'open'         => 'Open Access',
                        'subscription' => 'Berlangganan',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
