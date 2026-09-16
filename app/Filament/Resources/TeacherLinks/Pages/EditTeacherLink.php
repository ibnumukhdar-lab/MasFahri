<?php

namespace App\Filament\Resources\TeacherLinks\Pages;

use App\Filament\Resources\TeacherLinks\TeacherLinkResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTeacherLink extends EditRecord
{
    protected static string $resource = TeacherLinkResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
