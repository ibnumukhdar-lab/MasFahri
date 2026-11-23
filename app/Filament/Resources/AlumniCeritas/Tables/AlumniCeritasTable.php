<?php

namespace App\Filament\Resources\AlumniCeritas\Tables;

use App\Models\AlumniCerita;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class AlumniCeritasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->columns([
                ImageColumn::make('foto')
                    ->label('Foto')
                    ->disk('media')
                    ->getStateUsing(fn (AlumniCerita $r): ?string => $r->foto)
                    ->circular()
                    ->size(42),

                TextColumn::make('nama')
                    ->label('Nama')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('wa')
                    ->label('WhatsApp')
                    ->searchable(),

                TextColumn::make('tahun_lulus')
                    ->label('Angkatan')
                    ->sortable()
                    ->badge()
                    ->color('gray'),

                TextColumn::make('status_saat_ini')
                    ->label('Status')
                    ->formatStateUsing(fn (?string $state): string => $state ? (AlumniCerita::STATUS[$state] ?? $state) : '—')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'kuliah' => 'info',
                        'bekerja' => 'success',
                        'wirausaha' => 'warning',
                        'tahfizh' => 'primary',
                        default => 'gray',
                    }),

                TextColumn::make('pesan')
                    ->label('Pesan')
                    ->limit(60)
                    ->tooltip(fn (AlumniCerita $r): ?string => $r->pesan),

                IconColumn::make('tampil')
                    ->label('Tampil')
                    ->boolean()
                    ->state(fn (AlumniCerita $r): bool => $r->tampil && $r->diverifikasi_pada !== null),
            ])
            ->filters([
                TernaryFilter::make('tampil')
                    ->label('Status tampil')
                    ->nullable()
                    ->placeholder('Semua')
                    ->trueLabel('Sudah tampil')
                    ->falseLabel('Menunggu verifikasi'),

                SelectFilter::make('tahun_lulus')
                    ->label('Angkatan')
                    ->options(fn (): array => AlumniCerita::query()
                        ->whereNotNull('tahun_lulus')
                        ->distinct()
                        ->orderByDesc('tahun_lulus')
                        ->pluck('tahun_lulus', 'tahun_lulus')
                        ->all()),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()
                    ->after(function (AlumniCerita $record): void {
                        if ($record->foto) {
                            \Illuminate\Support\Facades\Storage::disk('media')->delete($record->foto);
                        }
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                \Filament\Actions\CreateAction::make(),
            ]);
    }
}
