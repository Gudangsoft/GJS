<?php

namespace App\Filament\Widgets;

use App\Models\LetterOfAcceptance;
use App\Models\ReviewAssignment;
use App\Models\Submission;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AlertsWidget extends BaseWidget
{
    protected static ?int $sort   = 4;
    protected static bool $isLazy = true;
    protected ?string $pollingInterval = null;

    protected function getHeading(): ?string
    {
        return 'Perlu Perhatian';
    }

    protected function getStats(): array
    {
        // Naskah baru (submitted/diterima) belum di-assign reviewer > 7 hari
        $unassigned = Submission::whereIn('status', ['submitted', 'queued', 'accepted_for_review'])
            ->where('submitted_at', '<', now()->subDays(7))
            ->count();

        // Naskah revision diminta > 14 hari belum revisi
        $staleRevision = Submission::where('status', 'revision_required')
            ->where('updated_at', '<', now()->subDays(14))
            ->count();

        // Undangan review menunggu konfirmasi reviewer > 3 hari
        $pendingInvite = ReviewAssignment::where('status', 'awaiting_response')
            ->where('created_at', '<', now()->subDays(3))
            ->count();

        // LOA draft belum diterbitkan
        $loaDraft = LetterOfAcceptance::where('status', 'draft')->count();

        // Naskah sudah "accepted" tapi belum ada LOA
        $noLoa = Submission::whereIn('status', ['accepted', 'copyediting', 'production', 'scheduled'])
            ->whereDoesntHave('letterOfAcceptances')
            ->count();

        return [
            Stat::make('Naskah Belum Di-assign', $unassigned)
                ->description($unassigned > 0 ? 'Submitted > 7 hari, belum ada editor' : 'Semua sudah tertangani')
                ->descriptionIcon($unassigned > 0 ? 'heroicon-o-clock' : 'heroicon-o-check-circle')
                ->color($unassigned > 0 ? 'danger' : 'success')
                ->icon('heroicon-o-inbox'),

            Stat::make('Menunggu Revisi Penulis', $staleRevision)
                ->description($staleRevision > 0 ? 'Revision diminta > 14 hari' : 'Tidak ada yang tertunda')
                ->descriptionIcon($staleRevision > 0 ? 'heroicon-o-exclamation-triangle' : 'heroicon-o-check-circle')
                ->color($staleRevision > 0 ? 'warning' : 'success')
                ->icon('heroicon-o-pencil-square'),

            Stat::make('Undangan Review Pending', $pendingInvite)
                ->description($pendingInvite > 0 ? 'Menunggu konfirmasi > 3 hari' : 'Semua reviewer sudah konfirmasi')
                ->descriptionIcon($pendingInvite > 0 ? 'heroicon-o-envelope' : 'heroicon-o-check-circle')
                ->color($pendingInvite > 0 ? 'warning' : 'success')
                ->icon('heroicon-o-user-plus'),

            Stat::make('LOA Draft', $loaDraft)
                ->description($loaDraft > 0 ? 'Belum diterbitkan ke penulis' : 'Tidak ada LOA tertunda')
                ->descriptionIcon($loaDraft > 0 ? 'heroicon-o-document-text' : 'heroicon-o-check-circle')
                ->color($loaDraft > 0 ? 'warning' : 'success')
                ->icon('heroicon-o-document-check'),

            Stat::make('Diterima Tanpa LOA', $noLoa)
                ->description($noLoa > 0 ? 'Naskah accepted belum ada LOA' : 'Semua sudah memiliki LOA')
                ->descriptionIcon($noLoa > 0 ? 'heroicon-o-exclamation-circle' : 'heroicon-o-check-circle')
                ->color($noLoa > 0 ? 'danger' : 'success')
                ->icon('heroicon-o-document-minus'),
        ];
    }
}
