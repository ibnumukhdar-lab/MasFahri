<?php

namespace App\Filament\Resources\TeacherLinks\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class TeacherLinksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('urut')
            ->columns([
                TextColumn::make('judul')
                    ->label('Judul')
                    ->searchable()
                    ->sortable()
                    ->wrap(),

                TextColumn::make('url')
                    ->label('URL')
                    ->limit(45)
                    ->searchable()
                    ->url(fn ($record): ?string => $record->url)
                    ->color('primary'),

                TextColumn::make('kelompok')
                    ->label('Kelompok')
                    ->badge()
                    ->color('sky')
                    ->searchable(),

                IconColumn::make('aktif')
                    ->label('Aktif')
                    ->boolean(),

                TextColumn::make('urut')
                    ->label('Urut')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('kelompok')
                    ->label('Kelompok')
                    ->options(fn (): array => \App\Models\TeacherLink::query()
                        ->whereNotNull('kelompok')
                        ->distinct()
                        ->orderBy('kelompok')
                        ->pluck('kelompok', 'kelompok')
                        ->all()),

                SelectFilter::make('aktif')
                    ->label('Status')
                    ->options([
                        '1' => 'Aktif',
                        '0' => 'Nonaktif',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
