<?php

namespace App\Filament\Resources\LetterOfAcceptances\Pages;

use App\Filament\Resources\LetterOfAcceptances\LetterOfAcceptanceResource;
use App\Models\LetterOfAcceptance;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListLetterOfAcceptances extends ListRecords
{
    protected static string $resource = LetterOfAcceptanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('+ Buat LOA')
                ->icon('heroicon-o-plus'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Semua')
                ->badge(LetterOfAcceptance::count()),

            'draft' => Tab::make('Draft')
                ->badge(LetterOfAcceptance::where('status', 'draft')->count())
                ->badgeColor('warning')
                ->modifyQueryUsing(fn (Builder $q) => $q->where('status', 'draft')),

            'issued' => Tab::make('Diterbitkan')
                ->badge(LetterOfAcceptance::where('status', 'issued')->count())
                ->badgeColor('success')
                ->modifyQueryUsing(fn (Builder $q) => $q->where('status', 'issued')),

            'revoked' => Tab::make('Dicabut')
                ->badge(LetterOfAcceptance::where('status', 'revoked')->count())
                ->badgeColor('danger')
                ->modifyQueryUsing(fn (Builder $q) => $q->where('status', 'revoked')),
        ];
    }
}
