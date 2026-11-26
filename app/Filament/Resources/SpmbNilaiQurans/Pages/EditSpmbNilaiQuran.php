<?php

namespace App\Filament\Resources\SpmbNilaiQurans\Pages;

use App\Filament\Resources\SpmbNilaiQurans\SpmbNilaiQuranResource;
use Filament\Resources\Pages\EditRecord;

class EditSpmbNilaiQuran extends EditRecord
{
    protected static string $resource = SpmbNilaiQuranResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
