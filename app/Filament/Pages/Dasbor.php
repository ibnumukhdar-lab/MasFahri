<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard;

class Dasbor extends Dashboard
{
    protected string $view = 'filament.pages.dasbor';

    protected static ?string $navigationLabel = 'Dasbor';

    protected static ?string $title = 'Dasbor';

    protected static ?int $navigationSort = -2;

    public function getHeading(): string
    {
        return '';
    }

    public function getSubheading(): ?string
    {
        return null;
    }
}
