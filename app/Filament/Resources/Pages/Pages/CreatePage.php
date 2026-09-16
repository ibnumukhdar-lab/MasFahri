<?php

namespace App\Filament\Resources\Pages\Pages;

use App\Filament\Resources\Pages\PageResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePage extends CreateRecord
{
    protected static string $resource = PageResource::class;

    /** Kolom bantu "sisipkan dari galeri" tidak disimpan ke database. */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        unset($data['sisipkan_galeri']);

        return $data;
    }
}
