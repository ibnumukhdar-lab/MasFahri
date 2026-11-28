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
                    ->color(fn (User $record): string => match ((string) $record->peran) {
                        'super_admin', 'admin' => 'primary',
                        'operator_website' => 'success',
                        'panitia_spmb' => 'warning',
                        default => 'gray',
                    }),

                TextColumn::make('nipa')
                    ->label('NIPA')
                    ->placeholder('-')
                    ->toggleable(),

                TextColumn::make('whatsapp')
                    ->label('WhatsApp')
                    ->placeholder('-')
                    ->toggleable(),

                IconColumn::make('aktif')
                    ->label('Aktif')
                    ->boolean(),


                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('peran')
                    ->label('Peran')
                    ->options(User::PERAN_PILIHAN + [
                        'admin' => 'Administrator (lama)',
                        'guru' => 'Guru',
                        'musyrif' => 'Musyrif/Musyrifah',
                        'ustadz_diniyah' => 'Ustadz Diniyah',
                        'orang_tua' => 'Orang Tua',
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
            ]);
    }
}
