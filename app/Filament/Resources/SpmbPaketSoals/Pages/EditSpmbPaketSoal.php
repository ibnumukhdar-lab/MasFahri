<?php

namespace App\Filament\Resources\SpmbPaketSoals\Pages;

use App\Filament\Resources\SpmbPaketSoals\SpmbPaketSoalResource;
use Filament\Resources\Pages\EditRecord;

class EditSpmbPaketSoal extends EditRecord
{
    protected static string $resource = SpmbPaketSoalResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
