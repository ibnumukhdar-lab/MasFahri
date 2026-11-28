<?php

namespace App\Filament\Resources\SpmbNilaiQurans\Pages;

use App\Filament\Resources\SpmbNilaiQurans\SpmbNilaiQuranResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSpmbNilaiQuran extends CreateRecord
{
    protected static string $resource = SpmbNilaiQuranResource::class;

    public function mount(): void
    {
        parent::mount();

        static::stickyFormActions();
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }

    protected function getFormActions(): array
    {
        return [
            $this->getCreateFormAction()
                ->label('Simpan nilai')
                ->icon('heroicon-o-check')
                ->extraAttributes(['style' => 'min-height:46px;padding-inline:22px']),

            $this->getCancelFormAction()->label('Batal'),
        ];
    }
}
