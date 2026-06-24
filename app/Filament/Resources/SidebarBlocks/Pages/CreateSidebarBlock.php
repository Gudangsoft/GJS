<?php

namespace App\Filament\Resources\SidebarBlocks\Pages;

use App\Filament\Resources\SidebarBlocks\SidebarBlockResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSidebarBlock extends CreateRecord
{
    protected static string $resource = SidebarBlockResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
