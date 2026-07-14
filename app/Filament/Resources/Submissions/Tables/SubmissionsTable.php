<?php

namespace App\Filament\Resources\Submissions\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class SubmissionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Judul Naskah')
                    ->searchable()
                    ->sortable()
                    ->limit(60)
                    ->tooltip(fn ($record) => $record->title)
                    ->description(fn ($record) => $record->subtitle),
                TextColumn::make('submitter.full_name')
                    ->label('Penulis Pengirim')
                    ->searchable(['users.first_name', 'users.last_name'])
                    ->description(fn ($record) => $record->submitter?->email),
                TextColumn::make('journal.name')
                    ->label('Jurnal')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('gray'),
                TextColumn::make('section.title')
                    ->label('Seksi')
                    ->searchable()
                    ->placeholder('—')
                    ->toggleable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->colors([
                        'gray'    => 'draft',
                        'info'    => fn ($state) => in_array($state, ['submitted', 'queued', 'assigned']),
                        'warning' => fn ($state) => in_array($state, ['review', 'revision_required', 'resubmit']),
                        'success' => fn ($state) => in_array($state, ['accepted_for_review', 'accepted', 'copyediting', 'production', 'scheduled', 'published']),
                        'danger'  => fn ($state) => in_array($state, ['declined', 'archived']),
                    ])
                    ->formatStateUsing(fn ($state) => match($state) {
                        'draft'             => 'Draft',
                        'submitted'         => 'Submitted',
                        'queued'            => 'Antrian',
                        'accepted_for_review' => 'Diterima',
                        'assigned'          => 'Ditugaskan',
                        'review'            => 'Dalam Review',
                        'revision_required' => 'Revisi',
                        'resubmit'          => 'Resubmit',
                        'accepted'          => 'Disetujui',
                        'copyediting'       => 'Copyediting',
                        'production'        => 'Produksi',
                        'scheduled'         => 'Dijadwalkan',
                        'published'         => 'Terbit',
                        'declined'          => 'Ditolak',
                        'archived'          => 'Diarsipkan',
                        default             => $state,
                    }),
                TextColumn::make('submission_type')
                    ->label('Tipe')
                    ->badge()
                    ->color('gray')
                    ->formatStateUsing(fn ($state) => match($state) {
                        'article'      => 'Artikel',
                        'review'       => 'Review',
                        'short_report' => 'Laporan',
                        'editorial'    => 'Editorial',
                        default        => $state,
                    })
                    ->toggleable(),
                TextColumn::make('submitted_at')
                    ->label('Tgl Submit')
                    ->date('d M Y')
                    ->sortable()
                    ->placeholder('—'),
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('journal_id')
                    ->label('Jurnal')
                    ->relationship('journal', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'draft'             => 'Draft',
                        'submitted'         => 'Submitted',
                        'queued'            => 'Antrian',
                        'accepted_for_review' => 'Diterima',
                        'assigned'          => 'Ditugaskan',
                        'review'            => 'Dalam Review',
                        'revision_required' => 'Revisi',
                        'resubmit'          => 'Resubmit',
                        'accepted'          => 'Disetujui',
                        'copyediting'       => 'Copyediting',
                        'production'        => 'Produksi',
                        'scheduled'         => 'Dijadwalkan',
                        'published'         => 'Terbit',
                        'declined'          => 'Ditolak',
                        'archived'          => 'Diarsipkan',
                    ]),
                SelectFilter::make('submission_type')
                    ->label('Tipe Naskah')
                    ->options([
                        'article'      => 'Artikel Penelitian',
                        'review'       => 'Artikel Review',
                        'short_report' => 'Laporan Singkat',
                        'editorial'    => 'Editorial',
                    ]),
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
