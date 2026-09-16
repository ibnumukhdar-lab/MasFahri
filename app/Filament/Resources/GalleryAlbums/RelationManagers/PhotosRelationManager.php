<?php

namespace App\Filament\Resources\GalleryAlbums\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PhotosRelationManager extends RelationManager
{
    protected static string $relationship = 'photos';

    protected static ?string $title = 'Foto';

    protected static ?string $modelLabel = 'Foto';

    protected static ?string $pluralModelLabel = 'Foto';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                FileUpload::make('berkas')
                    ->label('Berkas foto')
                    ->disk('media')->visibility('public')
                    ->directory('unggahan')
                    ->image()
                    ->imageEditor()
                    ->required()
                    ->helperText('Tersimpan di public/media/unggahan.')
                    ->columnSpanFull(),

                TextInput::make('judul')
                    ->label('Judul / keterangan foto')
                    ->maxLength(255),

                TextInput::make('urut')
                    ->label('Urutan')
                    ->numeric()
                    ->default(0),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('judul')
            ->defaultSort('urut')
            ->columns([
                ImageColumn::make('url')
                    ->label('Foto')
                    ->height(60)
                    ->width(80),

                TextColumn::make('judul')
                    ->label('Judul')
                    ->searchable()
                    ->wrap(),

                TextColumn::make('berkas')
                    ->label('Berkas')
                    ->limit(45)
                    ->toggleable(),

                TextColumn::make('urut')
                    ->label('Urut')
                    ->numeric()
                    ->sortable(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Tambah foto'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
