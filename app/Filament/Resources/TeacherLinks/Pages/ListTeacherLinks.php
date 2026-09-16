<?php

namespace App\Filament\Resources\TeacherLinks\Pages;

use App\Filament\Resources\TeacherLinks\TeacherLinkResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTeacherLinks extends ListRecords
{
    protected static string $resource = TeacherLinkResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
