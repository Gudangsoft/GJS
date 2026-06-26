<?php

namespace App\Filament\Widgets;

use App\Models\Submission;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class SubmissionTrendWidget extends ChartWidget
{
    protected static ?int    $sort    = 3;
    protected static bool    $isLazy  = true;
    protected ?string $pollingInterval = null;
    protected string  $color   = 'primary';
    protected ?string $heading = 'Tren Naskah Masuk';
    protected ?string $maxHeight = '220px';

    protected function getType(): string
    {
        return 'line';
    }

    protected function getFilters(): ?array
    {
        return [
            '3'  => '3 Bulan',
            '6'  => '6 Bulan',
            '12' => '12 Bulan',
        ];
    }

    protected function getData(): array
    {
        $months = (int) ($this->filter ?? 6);

        $rows = Submission::query()
            ->whereNotNull('submitted_at')
            ->where('submitted_at', '>=', now()->subMonths($months)->startOfMonth())
            ->select(DB::raw("DATE_FORMAT(submitted_at, '%Y-%m') as month"), DB::raw('COUNT(*) as n'))
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('n', 'month');

        $labels = [];
        $values = [];

        for ($i = $months - 1; $i >= 0; $i--) {
            $dt       = now()->subMonths($i);
            $key      = $dt->format('Y-m');
            $labels[] = $dt->format('M Y');
            $values[] = $rows[$key] ?? 0;
        }

        return [
            'datasets' => [
                [
                    'label'           => 'Naskah Masuk',
                    'data'            => $values,
                    'fill'            => true,
                    'tension'         => 0.35,
                    'backgroundColor' => 'rgba(59,130,246,0.08)',
                    'borderColor'     => 'rgb(59,130,246)',
                    'pointBackgroundColor' => 'rgb(59,130,246)',
                    'pointRadius'     => 4,
                ],
            ],
            'labels' => $labels,
        ];
    }
}
