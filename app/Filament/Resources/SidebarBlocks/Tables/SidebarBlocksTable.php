<?php

namespace App\Filament\Resources\SidebarBlocks\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SidebarBlocksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sort_order')
                    ->label('#')
                    ->sortable()
                    ->width('48px'),

                TextColumn::make('journal.name')
                    ->label('Jurnal')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info'),

                TextColumn::make('type')
                    ->label('Jenis')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'journal_info'     => 'Info Jurnal',
                        'accreditation'    => 'Akreditasi',
                        'submission'       => 'Kirim Naskah',
                        'article_template' => 'Template',
                        'statistics'       => 'Statistik',
                        'focus_scope'      => 'Fokus & Scope',
                        'custom_html'      => 'HTML Bebas',
                        default            => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'journal_info'     => 'primary',
                        'accreditation'    => 'success',
                        'submission'       => 'warning',
                        'article_template' => 'warning',
                        'statistics'       => 'info',
                        'focus_scope'      => 'primary',
                        'custom_html'      => 'gray',
                        default            => 'gray',
                    }),

                TextColumn::make('title')
                    ->label('Judul')
                    ->placeholder('(default)')
                    ->searchable(),

                IconColumn::make('enabled')
                    ->label('Aktif')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('danger'),

                TextColumn::make('updated_at')
                    ->label('Terakhir Diubah')
                    ->since()
                    ->sortable()
                    ->toggleable(),
            ])
            ->defaultSort('sort_order', 'asc')
            ->filters([
                SelectFilter::make('journal_id')
                    ->label('Jurnal')
                    ->relationship('journal', 'name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('type')
                    ->label('Jenis Blok')
                    ->options([
                        'journal_info'     => 'Informasi Jurnal',
                        'accreditation'    => 'Akreditasi & Indeksasi',
                        'submission'       => 'Kirim Naskah',
                        'article_template' => 'Template Artikel',
                        'statistics'       => 'Statistik Jurnal',
                        'focus_scope'      => 'Fokus & Ruang Lingkup',
                        'custom_html'      => 'Konten Bebas',
                    ]),

                SelectFilter::make('enabled')
                    ->label('Status')
                    ->options([
                        '1' => 'Aktif',
                        '0' => 'Nonaktif',
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
