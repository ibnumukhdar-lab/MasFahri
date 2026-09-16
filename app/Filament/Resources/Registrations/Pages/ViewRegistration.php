<?php

namespace App\Filament\Resources\Registrations\Pages;

use App\Filament\Resources\Registrations\RegistrationResource;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewRegistration extends ViewRecord
{
    protected static string $resource = RegistrationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('tandai_dihubungi')
                ->label('Tandai dihubungi')
                ->icon('heroicon-o-phone')
                ->requiresConfirmation()
                ->action(function (): void {
                    $this->record->update(['status' => 'dihubungi']);
                    Notification::make()->title('Ditandai sudah dihubungi')->success()->send();
                    $this->refreshFormData(['status']);
                }),

            Action::make('terima')
                ->label('Terima')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->action(function (): void {
                    $this->record->update(['status' => 'diterima']);
                    Notification::make()->title('Pendaftar diterima')->success()->send();
                    $this->refreshFormData(['status']);
                }),

            Action::make('tolak')
                ->label('Tolak')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->requiresConfirmation()
                ->action(function (): void {
                    $this->record->update(['status' => 'ditolak']);
                    Notification::make()->title('Pendaftar ditolak')->warning()->send();
                    $this->refreshFormData(['status']);
                }),
        ];
    }
}
