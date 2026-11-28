<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->visible(fn (User $record): bool => $record->id !== auth()->id()),
        ];
    }

    /**
     * Jangan sampai panel kehilangan super admin terakhir.
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $record = $this->getRecord();

        $sebelumSuper = $record->superAdmin();
        $sesudahSuper = in_array((string) ($data['peran'] ?? ''), ['super_admin', 'admin'], true);

        if ($sebelumSuper && ! $sesudahSuper) {
            $sisa = User::query()
                ->where('aktif', true)
                ->whereIn('peran', ['super_admin', 'admin'])
                ->whereKeyNot($record->getKey())
                ->count();

            if ($sisa === 0) {
                Notification::make()
                    ->title('Tidak bisa mengubah peran')
                    ->body('Akun ini satu-satunya super admin. Tunjuk super admin lain dulu sebelum mengubah perannya.')
                    ->danger()
                    ->send();

                $this->halt();
            }
        }

        return $data;
    }
}
