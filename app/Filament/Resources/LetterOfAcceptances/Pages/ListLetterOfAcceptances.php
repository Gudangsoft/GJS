<?php

namespace App\Filament\Resources\LetterOfAcceptances\Pages;

use App\Filament\Resources\LetterOfAcceptances\LetterOfAcceptanceResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListLetterOfAcceptances extends ListRecords
{
    protected static string $resource = LetterOfAcceptanceResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()->label('Buat LOA')];
    }
}
