<?php

namespace App\Filament\Resources\Registrations\Tables;

use App\Filament\Resources\Registrations\Schemas\RegistrationForm;
use App\Filament\Resources\Registrations\Schemas\RegistrationInfolist;
use App\Models\Registration;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class RegistrationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('nomor')
                    ->label('Nomor')
                    ->searchable()
                    ->sortable()
                    ->copyable(),

                TextColumn::make('nama_siswa')
                    ->label('Nama siswa')
                    ->searchable()
                    ->wrap(),

                TextColumn::make('nama_wali')
                    ->label('Nama wali')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('whatsapp')
                    ->label('WhatsApp')
                    ->searchable()
                    ->copyable(),

                TextColumn::make('minat_program')
                    ->label('Program')
                    ->toggleable(),

                TextColumn::make('tahun_ajaran')
                    ->label('Tahun ajaran')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => Registration::STATUS[$state] ?? (string) $state)
                    ->color(fn (?string $state): string => RegistrationInfolist::warnaStatus($state)),

                TextColumn::make('created_at')
                    ->label('Masuk')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(Registration::STATUS),

                SelectFilter::make('tahun_ajaran')
                    ->label('Tahun ajaran')
                    ->options(fn (): array => Registration::query()
                        ->whereNotNull('tahun_ajaran')
                        ->distinct()
                        ->orderByDesc('tahun_ajaran')
                        ->pluck('tahun_ajaran', 'tahun_ajaran')
                        ->all()),
            ])
            ->recordActions([
                ViewAction::make()
                    ->label('Lihat'),

                ActionGroup::make([
                    Action::make('ubah_status')
                        ->label('Ubah status & pesan')
                        ->icon('heroicon-o-arrow-path-rounded-square')
                        ->fillForm(fn (Registration $record): array => [
                            'status' => $record->status,
                            'pesan_admin' => $record->pesan_admin,
                        ])
                        ->schema(RegistrationForm::statusSchema())
                        ->action(function (Registration $record, array $data): void {
                            $record->update([
                                'status' => $data['status'],
                                'pesan_admin' => $data['pesan_admin'] ?? null,
                            ]);

                            Notification::make()
                                ->title('Status diperbarui')
                                ->body($record->nomor . ' → ' . (Registration::STATUS[$data['status']] ?? $data['status']))
                                ->success()
                                ->send();
                        }),

                    Action::make('tandai_dihubungi')
                        ->label('Tandai dihubungi')
                        ->icon('heroicon-o-phone')
                        ->visible(fn (Registration $record): bool => $record->status === 'baru')
                        ->requiresConfirmation()
                        ->action(function (Registration $record): void {
                            $record->update(['status' => 'dihubungi']);

                            Notification::make()->title($record->nomor . ' ditandai sudah dihubungi')->success()->send();
                        }),

                    Action::make('terima')
                        ->label('Terima')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->visible(fn (Registration $record): bool => ! in_array($record->status, ['diterima', 'ditolak'], true))
                        ->requiresConfirmation()
                        ->action(function (Registration $record): void {
                            $record->update(['status' => 'diterima']);

                            Notification::make()->title($record->nomor . ' diterima')->success()->send();
                        }),

                    Action::make('tolak')
                        ->label('Tolak')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->visible(fn (Registration $record): bool => ! in_array($record->status, ['diterima', 'ditolak'], true))
                        ->requiresConfirmation()
                        ->action(function (Registration $record): void {
                            $record->update(['status' => 'ditolak']);

                            Notification::make()->title($record->nomor . ' ditolak')->warning()->send();
                        }),
                ])->label('Tindak lanjut'),
            ])
            ->toolbarActions([
                //
            ]);
    }
}
