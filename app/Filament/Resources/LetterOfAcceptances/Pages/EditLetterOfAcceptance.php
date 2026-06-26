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
        return [
            \Filament\Actions\Action::make('preview')
                ->label('Preview LOA')
                ->icon('heroicon-o-eye')
                ->color('info')
                ->url(fn () => route('loa.preview', $this->record))
                ->openUrlInNewTab(),

            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // If old data stored authors as flat strings, convert to [{name, affiliation}]
        if (is_array($data['authors'] ?? null)) {
            $data['authors'] = array_map(function ($a) {
                if (is_array($a)) return $a; // already {name, affiliation}
                return ['name' => (string)$a, 'affiliation' => ''];
            }, $data['authors']);
        }
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // authors is [{name, affiliation}] from Repeater — just ensure it's an array
        if (!is_array($data['authors'] ?? null)) {
            $data['authors'] = [];
        }
        return $data;
    }
}
