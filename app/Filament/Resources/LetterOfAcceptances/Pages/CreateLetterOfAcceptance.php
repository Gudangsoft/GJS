<?php

namespace App\Filament\Resources\LetterOfAcceptances\Pages;

use App\Filament\Resources\LetterOfAcceptances\LetterOfAcceptanceResource;
use App\Models\LetterOfAcceptance;
use App\Models\Journal;
use Filament\Resources\Pages\CreateRecord;

class CreateLetterOfAcceptance extends CreateRecord
{
    protected static string $resource = LetterOfAcceptanceResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['issued_by'] = auth()->id();

        if (empty($data['loa_number']) && !empty($data['journal_id'])) {
            $journal = Journal::find($data['journal_id']);
            if ($journal) $data['loa_number'] = LetterOfAcceptance::generateNumber($journal);
        }

        if (is_string($data['authors'] ?? null)) {
            $data['authors'] = array_map('trim', explode(',', $data['authors']));
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
