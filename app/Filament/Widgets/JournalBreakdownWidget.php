<?php

namespace App\Filament\Widgets;

use App\Models\Journal;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class JournalBreakdownWidget extends BaseWidget
{
    protected static ?int $sort = 5;
    protected static bool $isLazy = true;
    protected static ?string $heading = 'Ringkasan Per Jurnal';
    protected ?string $pollingInterval = null;
    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Journal::withCount([
                    'submissions as antrian_count' => fn ($q) => $q->whereIn('status', ['submitted', 'queued']),
                    'submissions as review_count'  => fn ($q) => $q->whereIn('status', ['accepted_for_review', 'assigned', 'review', 'revision_required', 'resubmit']),
                    'submissions as prod_count'    => fn ($q) => $q->whereIn('status', ['accepted', 'copyediting', 'production', 'scheduled']),
                    'submissions as pub_count'     => fn ($q) => $q->where('status', 'published'),
                ])
                ->where('status', 'active')
                ->orderBy('name')
            )
            ->columns([
                ImageColumn::make('logo')
                    ->label('')
                    ->disk('public')
                    ->circular()
                    ->size(36)
                    ->defaultImageUrl('https://ui-avatars.com/api/?name=J&background=3b82f6&color=fff&size=36'),

                TextColumn::make('name_abbrev')
                    ->label('Jurnal')
                    ->weight('bold')
                    ->description(fn ($record) => $record->name)
                    ->searchable(false),

                TextColumn::make('antrian_count')
                    ->label('Antrian')
                    ->badge()
                    ->color(fn ($state) => $state > 0 ? 'warning' : 'gray')
                    ->alignCenter()
                    ->tooltip('Submitted & belum di-assign'),

                TextColumn::make('review_count')
                    ->label('Review')
                    ->badge()
                    ->color(fn ($state) => $state > 0 ? 'info' : 'gray')
                    ->alignCenter()
                    ->tooltip('Dalam proses review & revisi'),

                TextColumn::make('prod_count')
                    ->label('Produksi')
                    ->badge()
                    ->color(fn ($state) => $state > 0 ? 'primary' : 'gray')
                    ->alignCenter()
                    ->tooltip('Accepted, copyediting, production, scheduled'),

                TextColumn::make('pub_count')
                    ->label('Terbit')
                    ->badge()
                    ->color('success')
                    ->alignCenter()
                    ->tooltip('Artikel terpublikasi'),

                TextColumn::make('issn_online')
                    ->label('e-ISSN')
                    ->placeholder('—')
                    ->color('gray')
                    ->size('sm'),
            ])
            ->paginated(false)
            ->striped()
            ->recordUrl(fn ($record) => route('journals.home', ['journal' => $record->slug]));
    }
}
