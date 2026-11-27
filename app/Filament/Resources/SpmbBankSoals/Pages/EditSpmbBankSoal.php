<?php

namespace App\Filament\Resources\SpmbBankSoals\Pages;

use App\Filament\Resources\SpmbBankSoals\SpmbBankSoalResource;
use Filament\Resources\Pages\EditRecord;

class EditSpmbBankSoal extends EditRecord
{
    protected static string $resource = SpmbBankSoalResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
