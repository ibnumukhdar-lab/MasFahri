<?php

namespace App\Filament\Resources\SpmbNilaiQurans\Pages;

use App\Filament\Resources\SpmbNilaiQurans\SpmbNilaiQuranResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSpmbNilaiQurans extends ListRecords
{
    protected static string $resource = SpmbNilaiQuranResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Input nilai'),
        ];
    }
}
