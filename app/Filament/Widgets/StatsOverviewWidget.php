<?php

namespace App\Filament\Widgets;

use App\Models\Article;
use App\Models\Issue;
use App\Models\Journal;
use App\Models\LetterOfAcceptance;
use App\Models\ReviewAssignment;
use App\Models\Submission;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class StatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort   = 1;
    protected static bool $isLazy = true;
    protected ?string $pollingInterval = null;

    protected function getStats(): array
    {
        $statusCounts = Submission::query()
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $pending   = ($statusCounts['submitted'] ?? 0) + ($statusCounts['queued'] ?? 0);
        $inReview  = ($statusCounts['accepted_for_review'] ?? 0) + ($statusCounts['assigned'] ?? 0) + ($statusCounts['review'] ?? 0);
        $inProd    = ($statusCounts['accepted'] ?? 0)
                   + ($statusCounts['copyediting'] ?? 0)
                   + ($statusCounts['production'] ?? 0)
                   + ($statusCounts['scheduled'] ?? 0);

        $awaitingRev = ReviewAssignment::where('status', 'awaiting_response')->count();
        $loaDraft    = LetterOfAcceptance::where('status', 'draft')->count();
        $journals    = Journal::where('status', 'active')->where('enabled', true)->count();
        $users       = User::count();
        $articles    = Article::count();

        return [
            Stat::make('Antrian Naskah', $pending)
                ->description($pending > 0 ? 'Submitted, perlu di-assign editor' : 'Antrian kosong')
                ->descriptionIcon($pending > 0 ? 'heroicon-o-exclamation-circle' : 'heroicon-o-check-circle')
                ->color($pending > 0 ? 'warning' : 'success')
                ->icon('heroicon-o-inbox-arrow-down'),

            Stat::make('Dalam Review', $inReview)
                ->description('Diproses oleh reviewer')
                ->descriptionIcon('heroicon-o-eye')
                ->color('primary')
                ->icon('heroicon-o-eye'),

            Stat::make('Pra-Produksi', $inProd)
                ->description('Accepted, copyediting, production')
                ->descriptionIcon('heroicon-o-cog-6-tooth')
                ->color($inProd > 0 ? 'info' : 'gray')
                ->icon('heroicon-o-cog-6-tooth'),

            Stat::make('Review Menunggu', $awaitingRev)
                ->description($awaitingRev > 0 ? 'Belum konfirmasi kesediaan' : 'Semua reviewer sudah konfirmasi')
                ->descriptionIcon($awaitingRev > 0 ? 'heroicon-o-clock' : 'heroicon-o-check-circle')
                ->color($awaitingRev > 0 ? 'danger' : 'success')
                ->icon('heroicon-o-clipboard-document-check'),

            Stat::make('LOA Draft', $loaDraft)
                ->description($loaDraft > 0 ? 'Belum diterbitkan ke penulis' : 'Tidak ada LOA tertunda')
                ->descriptionIcon($loaDraft > 0 ? 'heroicon-o-exclamation-triangle' : 'heroicon-o-check-circle')
                ->color($loaDraft > 0 ? 'warning' : 'success')
                ->icon('heroicon-o-document-check'),

            Stat::make('Artikel Terbit', number_format($articles))
                ->description($journals . ' jurnal aktif · ' . $users . ' pengguna')
                ->descriptionIcon('heroicon-o-book-open')
                ->color('success')
                ->icon('heroicon-o-document-text'),
        ];
    }
}
