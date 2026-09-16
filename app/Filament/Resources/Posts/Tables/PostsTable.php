<?php

namespace App\Filament\Resources\Posts\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PostsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('terbit_pada', 'desc')
            ->columns([
                ImageColumn::make('gambar_url')
                    ->label('Sampul')
                    ->height(40)
                    ->width(60)
                    ->square(),

                TextColumn::make('judul')
                    ->label('Judul')
                    ->searchable()
                    ->sortable()
                    ->wrap()
                    ->limit(70),

                TextColumn::make('categories.nama')
                    ->label('Kategori')
                    ->badge()
                    ->separator(',')
                    ->color('sky'),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => $state === 'terbit' ? 'Terbit' : 'Draf')
                    ->color(fn (?string $state): string => $state === 'terbit' ? 'success' : 'gray'),

                TextColumn::make('terbit_pada')
                    ->label('Terbit')
                    ->dateTime('d M Y H:i')
                    ->sortable(),

                TextColumn::make('dilihat')
                    ->label('Dilihat')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'draf' => 'Draf',
                        'terbit' => 'Terbit',
                    ]),

                SelectFilter::make('categories')
                    ->label('Kategori')
                    ->relationship('categories', 'nama')
                    ->preload()
                    ->multiple(),
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
