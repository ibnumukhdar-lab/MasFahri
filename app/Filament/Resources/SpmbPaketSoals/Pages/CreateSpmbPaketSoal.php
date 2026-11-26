<?php

namespace App\Filament\Resources\SpmbPaketSoals\Pages;

use App\Filament\Resources\SpmbPaketSoals\SpmbPaketSoalResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSpmbPaketSoal extends CreateRecord
{
    protected static string $resource = SpmbPaketSoalResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
