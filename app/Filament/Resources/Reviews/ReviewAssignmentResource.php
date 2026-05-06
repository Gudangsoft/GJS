<?php

namespace App\Filament\Resources\Reviews;

use App\Filament\Resources\Reviews\Pages\ListReviewAssignments;
use App\Filament\Resources\Reviews\Tables\ReviewAssignmentsTable;
use App\Models\ReviewAssignment;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ReviewAssignmentResource extends Resource
{
    protected static ?string $model = ReviewAssignment::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;
    protected static ?string $navigationLabel = 'Penugasan Review';
    protected static string|\UnitEnum|null $navigationGroup = 'Naskah';
    protected static ?int $navigationSort = 2;
    protected static ?string $modelLabel = 'Penugasan Review';
    protected static ?string $pluralModelLabel = 'Daftar Penugasan Review';

    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::where('status', 'awaiting_response')->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema;
    }

    public static function table(Table $table): Table
    {
        return ReviewAssignmentsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListReviewAssignments::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
