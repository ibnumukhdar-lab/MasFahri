<?php

namespace App\Filament\Resources\DisplaySlides\Pages;

use App\Filament\Resources\DisplaySlides\DisplaySlideResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDisplaySlides extends ListRecords
{
    protected static string $resource = DisplaySlideResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Unggah slide'),
        ];
    }
}
