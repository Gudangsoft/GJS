<?php

namespace App\Filament\Resources\SidebarBlocks;

use App\Filament\Resources\SidebarBlocks\Pages\CreateSidebarBlock;
use App\Filament\Resources\SidebarBlocks\Pages\EditSidebarBlock;
use App\Filament\Resources\SidebarBlocks\Pages\ListSidebarBlocks;
use App\Filament\Resources\SidebarBlocks\Schemas\SidebarBlockForm;
use App\Filament\Resources\SidebarBlocks\Tables\SidebarBlocksTable;
use App\Models\JournalSidebarBlock;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SidebarBlockResource extends Resource
{
    protected static ?string $model = JournalSidebarBlock::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedViewColumns;
    protected static ?string $navigationLabel  = 'Blok Sidebar';
    protected static string|\UnitEnum|null $navigationGroup = 'Pengaturan';
    protected static ?int    $navigationSort   = 4;
    protected static ?string $modelLabel       = 'Blok Sidebar';
    protected static ?string $pluralModelLabel = 'Blok Sidebar';
    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return SidebarBlockForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SidebarBlocksTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListSidebarBlocks::route('/'),
            'create' => CreateSidebarBlock::route('/create'),
            'edit'   => EditSidebarBlock::route('/{record}/edit'),
        ];
    }
}
