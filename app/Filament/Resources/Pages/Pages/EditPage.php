<?php

namespace App\Filament\Resources\Pages\Pages;

use App\Filament\Resources\Pages\PageResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPage extends EditRecord
{
    protected static string $resource = PageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    /** Kolom bantu "sisipkan dari galeri" tidak disimpan ke database. */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        unset($data['sisipkan_galeri']);

        return $data;
    }
}
