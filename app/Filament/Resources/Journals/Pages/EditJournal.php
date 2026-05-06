<?php

namespace App\Filament\Resources\Journals\Pages;

use App\Filament\Resources\Journals\JournalResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditJournal extends EditRecord
{
    protected static string $resource = JournalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['managers'] = $this->record->managers->pluck('id')->toArray();
        $data['editors']  = $this->record->editors->pluck('id')->toArray();
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        unset($data['managers'], $data['editors']);
        return $data;
    }

    protected function afterSave(): void
    {
        // Sync managers — attach dengan role=manager
        $managerIds = collect($this->data['managers'] ?? [])
            ->mapWithKeys(fn ($id) => [$id => ['role' => 'manager']])
            ->toArray();

        // Sync editors — attach dengan role=editor
        $editorIds = collect($this->data['editors'] ?? [])
            ->mapWithKeys(fn ($id) => [$id => ['role' => 'editor']])
            ->toArray();

        // Hapus semua manager lama lalu pasang baru
        $this->record->allMembers()->wherePivot('role', 'manager')->detach();
        if (!empty($managerIds)) {
            $this->record->allMembers()->attach($managerIds);
        }

        // Hapus semua editor lama lalu pasang baru
        $this->record->allMembers()->wherePivot('role', 'editor')->detach();
        if (!empty($editorIds)) {
            $this->record->allMembers()->attach($editorIds);
        }
    }
}
