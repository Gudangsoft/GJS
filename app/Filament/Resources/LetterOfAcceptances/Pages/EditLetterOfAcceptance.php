<?php

namespace App\Filament\Resources\LetterOfAcceptances\Pages;

use App\Filament\Resources\LetterOfAcceptances\LetterOfAcceptanceResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditLetterOfAcceptance extends EditRecord
{
    protected static string $resource = LetterOfAcceptanceResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        if (is_array($data['authors'] ?? null)) {
            $data['authors'] = implode(', ', $data['authors']);
        }
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (is_string($data['authors'] ?? null)) {
            $data['authors'] = array_map('trim', explode(',', $data['authors']));
        }
        return $data;
    }
}
