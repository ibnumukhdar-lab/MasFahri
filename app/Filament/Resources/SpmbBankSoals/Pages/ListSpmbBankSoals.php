<?php

namespace App\Filament\Resources\SpmbBankSoals\Pages;

use App\Filament\Resources\SpmbBankSoals\SpmbBankSoalResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSpmbBankSoals extends ListRecords
{
    protected static string $resource = SpmbBankSoalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Tambah soal'),
        ];
    }
}
