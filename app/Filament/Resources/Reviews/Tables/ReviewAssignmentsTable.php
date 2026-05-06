<?php

namespace App\Filament\Resources\Reviews\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ReviewAssignmentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('submission.title')
                    ->label('Judul Naskah')
                    ->searchable()
                    ->limit(50)
                    ->tooltip(fn ($state) => $state),

                TextColumn::make('reviewer.full_name')
                    ->label('Reviewer')
                    ->searchable(['users.first_name', 'users.last_name'])
                    ->description(fn ($record) => $record?->reviewer?->affiliation),

                TextColumn::make('editor.full_name')
                    ->label('Editor')
                    ->searchable(['users.first_name', 'users.last_name'])
                    ->toggleable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->colors([
                        'warning' => 'awaiting_response',
                        'info'    => 'accepted',
                        'success' => 'completed',
                        'danger'  => fn ($state) => in_array($state, ['declined', 'cancelled']),
                    ])
                    ->formatStateUsing(fn ($state) => match($state) {
                        'awaiting_response' => 'Menunggu Konfirmasi',
                        'accepted'          => 'Sedang Review',
                        'completed'         => 'Selesai',
                        'declined'          => 'Ditolak Reviewer',
                        'cancelled'         => 'Dibatalkan',
                        default             => $state,
                    }),

                TextColumn::make('review.recommendation')
                    ->label('Rekomendasi')
                    ->badge()
                    ->colors([
                        'success' => 'accept',
                        'warning' => fn ($state) => in_array($state, ['pending_revisions', 'resubmit_here']),
                        'info'    => 'resubmit_elsewhere',
                        'danger'  => 'decline',
                        'gray'    => 'see_comments',
                    ])
                    ->formatStateUsing(fn ($state) => match($state) {
                        'accept'             => 'Terima',
                        'pending_revisions'  => 'Revisi Minor',
                        'resubmit_here'      => 'Revisi Mayor',
                        'resubmit_elsewhere' => 'Jurnal Lain',
                        'decline'            => 'Tolak',
                        'see_comments'       => 'Lihat Komentar',
                        default              => '—',
                    })
                    ->placeholder('—'),

                TextColumn::make('review_method')
                    ->label('Metode')
                    ->badge()
                    ->color('gray')
                    ->formatStateUsing(fn ($state) => match($state) {
                        'double_blind' => 'Double Blind',
                        'single_blind' => 'Single Blind',
                        'open'         => 'Open',
                        default        => $state,
                    })
                    ->toggleable(),

                TextColumn::make('date_due')
                    ->label('Deadline')
                    ->date('d M Y')
                    ->placeholder('—')
                    ->sortable(),

                TextColumn::make('date_completed')
                    ->label('Selesai')
                    ->date('d M Y')
                    ->placeholder('—')
                    ->sortable()
                    ->toggleable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'awaiting_response' => 'Menunggu Konfirmasi',
                        'accepted'          => 'Sedang Review',
                        'completed'         => 'Selesai',
                        'declined'          => 'Ditolak',
                        'cancelled'         => 'Dibatalkan',
                    ]),

                SelectFilter::make('review_method')
                    ->label('Metode')
                    ->options([
                        'double_blind' => 'Double Blind',
                        'single_blind' => 'Single Blind',
                        'open'         => 'Open',
                    ]),
            ]);
    }
}
