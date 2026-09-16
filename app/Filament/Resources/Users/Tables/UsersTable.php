<?php

namespace App\Filament\Resources\Users\Tables;

use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('name')
            ->columns([
                TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable()
                    ->wrap(),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->copyable(),

                TextColumn::make('peran_label')
                    ->label('Peran')
                    ->badge()
                    ->color(fn (User $record): string => $record->peran === 'admin' ? 'primary' : 'gray'),

                TextColumn::make('whatsapp')
                    ->label('WhatsApp')
                    ->placeholder('-')
                    ->toggleable(),

                IconColumn::make('aktif')
                    ->label('Aktif')
                    ->boolean(),

                TextColumn::make('alumniProfile.tahun_lulus')
                    ->label('Tahun lulus')
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('peran')
                    ->label('Peran')
                    ->options([
                        'admin' => 'Administrator',
                        'guru' => 'Guru',
                        'musyrif' => 'Musyrif/Musyrifah',
                        'ustadz_diniyah' => 'Ustadz Diniyah',
                        'orang_tua' => 'Orang Tua',
                        'alumni' => 'Alumni',
                    ]),

                SelectFilter::make('aktif')
                    ->label('Status akun')
                    ->options([
                        '1' => 'Aktif',
                        '0' => 'Nonaktif',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),

                Action::make('verifikasi_alumni')
                    ->label('Verifikasi alumni')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->visible(fn (User $record): bool => $record->alumniProfile !== null)
                    ->requiresConfirmation()
                    ->modalDescription('Menandai profil alumni ini terverifikasi dan menampilkannya di direktori publik.')
                    ->action(function (User $record): void {
                        $record->alumniProfile?->update([
                            'diverifikasi_pada' => now(),
                            'tampil_publik' => true,
                        ]);

                        Notification::make()
                            ->title('Alumni diverifikasi')
                            ->body($record->name)
                            ->success()
                            ->send();
                    }),
            ]);
    }
}
