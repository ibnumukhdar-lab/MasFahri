<?php
namespace App\Filament\Resources\Ekskuls\RelationManagers;

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

    protected static ?string $title = 'Foto kegiatan';

    protected static ?string $modelLabel = 'Foto kegiatan';

    protected static ?string $pluralModelLabel = 'Foto kegiatan';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                FileUpload::make('berkas')
                    ->label('Unggah foto kegiatan')
                    ->disk('media')->visibility('public')
                    ->directory('unggahan')
                    ->image()
                    ->imageEditor()
                    ->required()
                    ->helperText('Tersimpan di media/unggahan. Bisa unggah beberapa sekaligus dari HP.')
                    ->columnSpanFull(),

                TextInput::make('judul')
                    ->label('Keterangan foto (opsional)')
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
            ->reorderable('urut')
            ->columns([
                ImageColumn::make('url')
                    ->label('Foto')
                    ->height(48)
                    ->square(),

                TextColumn::make('judul')
                    ->label('Keterangan')
                    ->placeholder('(tanpa keterangan)')
                    ->wrap(),

                TextColumn::make('urut')
                    ->label('Urutan')
                    ->sortable(),
            ])
            ->headerActions([
                CreateAction::make()->label('Tambah foto'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
