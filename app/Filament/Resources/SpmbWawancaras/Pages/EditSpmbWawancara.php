<?php

namespace App\Filament\Resources\SpmbWawancaras\Pages;

use App\Filament\Resources\SpmbWawancaras\SpmbWawancaraResource;
use Filament\Resources\Pages\EditRecord;

class EditSpmbWawancara extends EditRecord
{
    protected static string $resource = SpmbWawancaraResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
