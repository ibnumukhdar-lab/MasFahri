<?php

namespace App\Filament\Resources\GalleryAlbums\RelationManagers;

use App\Models\GalleryPhoto;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
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

                Toggle::make('layar_utama')
                    ->label('Jadikan gambar layar utama')
                    ->helperText('Latar belakang halaman depan di belakang teks sambutan. Hanya satu gambar yang bisa aktif — mencentang ini otomatis melepas yang lain.')
                    ->columnSpanFull(),
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

                IconColumn::make('layar_utama')
                    ->label('Layar utama')
                    ->boolean()
                    ->trueIcon('heroicon-o-photo')
                    ->falseIcon('heroicon-o-minus-small')
                    ->trueColor('success')
                    ->falseColor('gray')
                    ->tooltip('Gambar yang tampil di latar halaman utama'),
            ])
            ->recordActions([
                \Filament\Actions\Action::make('layarUtama')
                    ->label(fn (GalleryPhoto $record): string => $record->layar_utama ? 'Batalkan layar utama' : 'Jadikan layar utama')
                    ->icon('heroicon-o-photo')
                    ->color(fn (GalleryPhoto $record): string => $record->layar_utama ? 'gray' : 'success')
                    ->action(function (GalleryPhoto $record): void {
                        $record->jadikanLayarUtama(!$record->layar_utama);

                        \Filament\Notifications\Notification::make()
                            ->title($record->fresh()->layar_utama
                                ? 'Gambar ini sekarang menjadi latar halaman utama'
                                : 'Gambar dilepas dari layar utama')
                            ->success()
                            ->send();
                    }),
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
