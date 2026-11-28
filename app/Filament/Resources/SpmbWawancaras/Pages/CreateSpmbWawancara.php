<?php

namespace App\Filament\Resources\SpmbWawancaras\Pages;

use App\Filament\Resources\SpmbWawancaras\SpmbWawancaraResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSpmbWawancara extends CreateRecord
{
    protected static string $resource = SpmbWawancaraResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
