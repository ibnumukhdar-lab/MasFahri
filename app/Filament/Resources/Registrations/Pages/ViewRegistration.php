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
            Action::make('verifikasi_bayar')
                ->label('Verifikasi pembayaran formulir')
                ->icon('heroicon-o-banknotes')
                ->color('success')
                ->visible(fn (): bool => in_array($this->record->status, ['baru', 'dihubungi'], true))
                ->modalHeading('Verifikasi pembayaran formulir')
                ->modalDescription('Setelah diverifikasi, pendaftar otomatis masuk tahap "Lengkapi formulir".')
                ->modalContent(fn () => view('filament.bukti-pembayaran', ['record' => $this->record]))
                ->modalSubmitActionLabel('Ya, verifikasi')
                ->action(function (): void {
                    $this->record->update(['status' => 'lengkapi_formulir']);
                    Notification::make()
                        ->title('Pembayaran terverifikasi')
                        ->body($this->record->nomor . ' → Lengkapi formulir.')
                        ->success()
                        ->send();
                    $this->refreshFormData(['status']);
                }),

            Action::make('lihat_bukti')
                ->label('Lihat bukti pembayaran')
                ->icon('heroicon-o-photo')
                ->visible(fn (): bool => (bool) $this->record->bukti_transfer)
                ->url(fn (): ?string => $this->record->bukti_transfer_url)
                ->openUrlInNewTab(),

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
