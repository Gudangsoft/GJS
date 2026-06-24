<?php

namespace App\Filament\Resources\SidebarBlocks\Pages;

use App\Filament\Resources\SidebarBlocks\SidebarBlockResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSidebarBlocks extends ListRecords
{
    protected static string $resource = SidebarBlockResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Tambah Blok Sidebar'),
        ];
    }
}
