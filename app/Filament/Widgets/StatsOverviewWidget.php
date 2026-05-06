<?php

namespace App\Filament\Widgets;

use App\Models\Article;
use App\Models\Issue;
use App\Models\Journal;
use App\Models\ReviewAssignment;
use App\Models\Submission;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;
    protected ?string $pollingInterval = '60s';

    protected function getStats(): array
    {
        $pending     = Submission::whereIn('status', ['submitted', 'queued'])->count();
        $inReview    = Submission::whereIn('status', ['assigned', 'review'])->count();
        $awaitingRev = ReviewAssignment::where('status', 'awaiting_response')->count();
        $articles    = Article::count();
        $journals    = Journal::where('status', 'active')->where('enabled', true)->count();
        $users       = User::count();

        return [
            Stat::make('Antrian Naskah', $pending)
                ->description('Menunggu penanganan editor')
                ->descriptionIcon('heroicon-o-clock')
                ->color($pending > 0 ? 'warning' : 'success')
                ->icon('heroicon-o-inbox-arrow-down'),

            Stat::make('Dalam Review', $inReview)
                ->description('Sedang diproses reviewer')
                ->descriptionIcon('heroicon-o-eye')
                ->color('primary')
                ->icon('heroicon-o-eye'),

            Stat::make('Review Pending', $awaitingRev)
                ->description('Menunggu konfirmasi reviewer')
                ->descriptionIcon('heroicon-o-user-plus')
                ->color($awaitingRev > 0 ? 'danger' : 'success')
                ->icon('heroicon-o-clipboard-document-check'),

            Stat::make('Artikel Terbit', $articles)
                ->description('Total artikel dipublikasikan')
                ->descriptionIcon('heroicon-o-document-text')
                ->color('success')
                ->icon('heroicon-o-document-text'),

            Stat::make('Jurnal Aktif', $journals)
                ->description('Jurnal terpublikasi')
                ->descriptionIcon('heroicon-o-book-open')
                ->color('info')
                ->icon('heroicon-o-book-open'),

            Stat::make('Total Pengguna', $users)
                ->description('Terdaftar di platform')
                ->descriptionIcon('heroicon-o-users')
                ->color('gray')
                ->icon('heroicon-o-users'),
        ];
    }
}
