<?php

namespace App\Filament\Resources\Reviews\Pages;

use App\Filament\Resources\Reviews\ReviewAssignmentResource;
use Filament\Resources\Pages\ListRecords;

class ListReviewAssignments extends ListRecords
{
    protected static string $resource = ReviewAssignmentResource::class;
}
