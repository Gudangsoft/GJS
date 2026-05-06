<?php

namespace App\Filament\Resources\Issues\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class IssuesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('cover_image')
                    ->label('')
                    ->size(50)
                    ->defaultImageUrl(asset('images/no-cover.png')),
                TextColumn::make('journal.name')
                    ->label('Jurnal')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('gray'),
                TextColumn::make('label')
                    ->label('Issue')
                    ->state(fn ($record) => $record->getLabel())
                    ->sortable(query: fn ($query, $direction) => $query->orderBy('year', $direction)->orderBy('volume', $direction)->orderBy('number', $direction))
                    ->description(fn ($record) => $record->title),
                TextColumn::make('date_published')
                    ->label('Tanggal Terbit')
                    ->date('d M Y')
                    ->sortable()
                    ->placeholder('Belum Diterbitkan'),
                TextColumn::make('access_status')
                    ->label('Akses')
                    ->badge()
                    ->colors([
                        'success' => 'open',
                        'warning' => 'subscription',
                    ])
                    ->formatStateUsing(fn ($state) => $state === 'open' ? 'Open Access' : 'Berlangganan'),
                IconColumn::make('published')
                    ->label('Terbit')
                    ->boolean(),
                IconColumn::make('current')
                    ->label('Terkini')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('gray'),
                TextColumn::make('articles_count')
                    ->label('Artikel')
                    ->counts('articles')
                    ->badge()
                    ->color('info'),
                TextColumn::make('doi')
                    ->label('DOI')
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('year', 'desc')
            ->filters([
                SelectFilter::make('journal_id')
                    ->label('Jurnal')
                    ->relationship('journal', 'name')
                    ->searchable()
                    ->preload(),
                TernaryFilter::make('published')
                    ->label('Status Terbit')
                    ->trueLabel('Sudah Terbit')
                    ->falseLabel('Belum Terbit'),
                SelectFilter::make('access_status')
                    ->label('Hak Akses')
                    ->options([
                        'open'         => 'Open Access',
                        'subscription' => 'Berlangganan',
                    ]),
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
