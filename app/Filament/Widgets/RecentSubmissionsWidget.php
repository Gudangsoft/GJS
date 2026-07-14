<?php

namespace App\Filament\Widgets;

use App\Models\Submission;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentSubmissionsWidget extends BaseWidget
{
    protected static ?int $sort = 2;
    protected static bool $isLazy = true;
    protected int|string|array $columnSpan = 'full';
    protected static ?string $heading = 'Naskah Terbaru';
    protected ?string $pollingInterval = null;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Submission::with(['submitter', 'journal', 'section'])
                    ->whereNotIn('status', ['draft'])
                    ->latest('submitted_at')
                    ->limit(10)
            )
            ->columns([
                TextColumn::make('id')
                    ->label('#')
                    ->prefix('#')
                    ->width('60px')
                    ->color('gray'),

                TextColumn::make('title')
                    ->label('Judul Naskah')
                    ->limit(60)
                    ->searchable()
                    ->description(fn ($record) => $record->submitter?->full_name ?? '—'),

                TextColumn::make('journal.name_abbrev')
                    ->label('Jurnal')
                    ->badge()
                    ->color('primary'),

                TextColumn::make('section.title')
                    ->label('Seksi')
                    ->badge()
                    ->color('gray')
                    ->placeholder('—'),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn ($state) => match($state) {
                        'submitted', 'queued'               => 'info',
                        'accepted_for_review'                => 'success',
                        'assigned'                          => 'primary',
                        'review'                            => 'warning',
                        'revision_required', 'resubmit'     => 'danger',
                        'accepted', 'published'             => 'success',
                        'declined'                          => 'gray',
                        default                             => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => match($state) {
                        'submitted'         => 'Submitted',
                        'queued'            => 'Antrian',
                        'accepted_for_review' => 'Diterima',
                        'assigned'          => 'Ditugaskan',
                        'review'            => 'Dalam Review',
                        'revision_required' => 'Revisi Diminta',
                        'resubmit'          => 'Resubmit',
                        'accepted'          => 'Disetujui',
                        'published'         => 'Terbit',
                        'declined'          => 'Ditolak',
                        default             => $state,
                    }),

                TextColumn::make('submitted_at')
                    ->label('Tanggal Submit')
                    ->date('d M Y')
                    ->placeholder('—')
                    ->sortable(),
            ])
            ->actions([
                Action::make('review')
                    ->label('Kelola')
                    ->url(fn ($record) => route('editor.submissions.review', $record))
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->openUrlInNewTab()
                    ->size('sm'),
            ])
            ->paginated(false)
            ->striped();
    }
}
