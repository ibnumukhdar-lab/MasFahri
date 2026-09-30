<?php
namespace App\Filament\Resources\Ekskuls\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class EkskulsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('urut')
            ->reorderable('urut')
            ->columns([
                TextColumn::make('nama')
                    ->label('Ekstrakurikuler')
                    ->searchable()
                    ->sortable()
                    ->wrap(),

                TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->limit(60)
                    ->wrap()
                    ->toggleable(),

                TextColumn::make('photos_count')
                    ->label('Jumlah foto')
                    ->counts('photos')
                    ->badge()
                    ->color('sky')
                    ->sortable(),

                TextColumn::make('urut')
                    ->label('Urutan')
                    ->sortable(),

                IconColumn::make('terbit')
                    ->label('Tampil')
                    ->boolean(),
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
