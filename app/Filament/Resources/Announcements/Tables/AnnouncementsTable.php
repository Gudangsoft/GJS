<?php

namespace App\Filament\Resources\Announcements\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AnnouncementsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('journal.name')
                    ->label('Jurnal')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('gray'),
                TextColumn::make('title')
                    ->label('Judul Pengumuman')
                    ->searchable()
                    ->limit(60)
                    ->tooltip(fn ($record) => $record->title)
                    ->description(fn ($record) => $record->description_short
                        ? strip_tags(mb_substr($record->description_short, 0, 80)).'…'
                        : null
                    ),
                TextColumn::make('author.full_name')
                    ->label('Penulis')
                    ->placeholder('—')
                    ->toggleable(),
                TextColumn::make('date_posted')
                    ->label('Tgl Posting')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('date_expire')
                    ->label('Kadaluarsa')
                    ->date('d M Y')
                    ->sortable()
                    ->placeholder('Tidak ada')
                    ->color(fn ($record) => $record->date_expire?->isPast() ? 'danger' : null),
                IconColumn::make('send_email')
                    ->label('Email Terkirim')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('date_posted', 'desc')
            ->filters([
                SelectFilter::make('journal_id')
                    ->label('Jurnal')
                    ->relationship('journal', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
