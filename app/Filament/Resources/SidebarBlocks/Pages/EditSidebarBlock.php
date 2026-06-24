<?php

namespace App\Filament\Resources\SidebarBlocks\Pages;

use App\Filament\Resources\SidebarBlocks\SidebarBlockResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSidebarBlock extends EditRecord
{
    protected static string $resource = SidebarBlockResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
