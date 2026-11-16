<?php

namespace App\Filament\Resources\DisplaySlides;

use App\Filament\Resources\DisplaySlides\Pages;
use App\Models\DisplaySlide;
use BackedEnum;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class DisplaySlideResource extends Resource
{
    protected static ?string $model = DisplaySlide::class;

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedTv;

    protected static ?string $slug = 'display';

    protected static ?string $title = 'Display TV';

    protected static ?string $navigationLabel = 'Display TV';

    protected static ?string $modelLabel = 'Slide display';

    protected static ?string $pluralModelLabel = 'Slide display';

    protected static string | UnitEnum | null $navigationGroup = 'Konten';

    public static function getNavigationBadge(): ?string
    {
        return (string) DisplaySlide::where('aktif', true)->count() ?: null;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                FileUpload::make('berkas')
                    ->label('Gambar slide')
                    ->disk('media')->visibility('public')
                    ->directory('display')
                    ->image()
                    ->imageEditor()
                    ->required()
                    ->helperText('Sebaiknya 1920×1080 (16:9) agar pas di TV. Tersimpan di media/display.')
                    ->columnSpanFull(),

                TextInput::make('judul')
                    ->label('Keterangan (internal)')
                    ->maxLength(255)
                    ->placeholder('mis. Banner TKA & AN 2026'),

                TextInput::make('durasi')
                    ->label('Durasi tampil (detik)')
                    ->numeric()->minValue(3)->maxValue(600)
                    ->default(10),

                TextInput::make('urut')
                    ->label('Urutan')
                    ->numeric()->minValue(0)
                    ->default(0)
                    ->helperText('Angka kecil tampil lebih dulu.'),

                Toggle::make('aktif')
                    ->label('Tampilkan di layar')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('urut')
            ->reorderable('urut')
            ->columns([
                ImageColumn::make('url')
                    ->label('Gambar'),

                TextColumn::make('judul')
                    ->label('Keterangan')
                    ->searchable()
                    ->placeholder('—')
                    ->wrap(),

                TextColumn::make('durasi')
                    ->label('Durasi')
                    ->suffix(' dtk'),

                TextColumn::make('urut')
                    ->label('Urutan')
                    ->sortable(),

                IconColumn::make('aktif')
                    ->label('Tampil')
                    ->boolean(),
            ])
            ->filters([
                \Filament\Tables\Filters\TernaryFilter::make('aktif')
                    ->label('Sedang tampil'),
            ])
            ->headerActions([
                \Filament\Actions\Action::make('bukaDisplay')
                    ->label('Buka halaman /display')
                    ->icon('heroicon-o-tv')
                    ->url('/display')
                    ->openUrlInNewTab(),
            ])
            ->recordUrl(null);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDisplaySlides::route('/'),
            'create' => Pages\CreateDisplaySlide::route('/create'),
            'edit' => Pages\EditDisplaySlide::route('/{record}/edit'),
        ];
    }
}
