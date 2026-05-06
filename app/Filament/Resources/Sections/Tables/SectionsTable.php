<?php

namespace App\Filament\Resources\Sections\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class SectionsTable
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
                    ->label('Nama Seksi')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('abbrev')
                    ->label('Singkatan')
                    ->searchable()
                    ->badge()
                    ->color('info'),
                TextColumn::make('sequence')
                    ->label('Urutan')
                    ->numeric()
                    ->sortable(),
                IconColumn::make('is_inactive')
                    ->label('Tidak Aktif')
                    ->boolean()
                    ->trueColor('danger')
                    ->falseColor('success')
                    ->trueIcon('heroicon-o-x-circle')
                    ->falseIcon('heroicon-o-check-circle'),
                IconColumn::make('editor_restricted')
                    ->label('Editor Saja')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('submitter_restricted')
                    ->label('Penulis Terdaftar')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('submissions_count')
                    ->label('Naskah')
                    ->counts('submissions')
                    ->badge()
                    ->color('info'),
            ])
            ->defaultSort('sequence')
            ->filters([
                SelectFilter::make('journal_id')
                    ->label('Jurnal')
                    ->relationship('journal', 'name')
                    ->searchable()
                    ->preload(),
                TernaryFilter::make('is_inactive')
                    ->label('Status Seksi')
                    ->trueLabel('Tidak Aktif')
                    ->falseLabel('Aktif'),
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
