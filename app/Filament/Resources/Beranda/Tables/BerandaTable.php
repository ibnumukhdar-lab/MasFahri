<?php

namespace App\Filament\Resources\Beranda\Tables;

use App\Filament\Resources\Beranda\BerandaResource;
use App\Models\BerandaBagian;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class BerandaTable
{
    public static function configure(Table $table): Table
    {
        return $table
            // Urutan bisa diubah dengan MENAHAN & MENARIK barisnya.
            ->reorderable('urutan')
            ->defaultSort('urutan')
            ->columns([
                TextColumn::make('judul')
                    ->label('Bagian')
                    ->weight('semibold')
                    ->placeholder('(belum diberi judul)')
                    ->description(fn (BerandaBagian $record): string => BerandaBagian::labelJenis($record->jenis))
                    ->searchable(),

                ToggleColumn::make('aktif')
                    ->label('Tampil')
                    ->onColor('success'),

                TextColumn::make('jenis')
                    ->label('Jenis')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => BerandaBagian::labelJenis($state))
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultPaginationPageOption(50)
            ->recordUrl(fn (BerandaBagian $record): string => BerandaResource::getUrl('edit', ['record' => $record]))
            ->recordActions([
                Action::make('lihat')
                    ->label('Lihat')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(fn (): string => url('/'))
                    ->openUrlInNewTab(),
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
