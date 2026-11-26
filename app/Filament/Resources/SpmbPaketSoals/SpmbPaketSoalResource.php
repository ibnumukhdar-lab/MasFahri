<?php

namespace App\Filament\Resources\SpmbPaketSoals;

use App\Filament\Resources\SpmbPaketSoals\Pages;
use App\Models\SpmbPaketSoal;
use BackedEnum;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class SpmbPaketSoalResource extends Resource
{
    protected static ?string $model = SpmbPaketSoal::class;

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static ?string $slug = 'paket-soal-cbt';

    protected static ?string $navigationLabel = 'Paket Soal CBT';

    protected static ?string $modelLabel = 'Paket soal';

    protected static ?string $pluralModelLabel = 'Paket soal CBT';

    protected static string | UnitEnum | null $navigationGroup = 'SPMB & Alumni';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('judul')
                    ->label('Judul paket')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('mis. Tes Potensi Akademik & Minat Bakat'),

                Textarea::make('deskripsi')
                    ->label('Deskripsi / petunjuk singkat')
                    ->rows(2)
                    ->placeholder('Muncul di halaman masuk tes siswa.')
                    ->columnSpanFull(),

                TextInput::make('durasi_menit')
                    ->label('Durasi (menit)')
                    ->numeric()->minValue(1)->maxValue(300)
                    ->required()
                    ->default(30),

                Toggle::make('aktif')
                    ->label('Paket aktif (dibuka untuk siswa)')
                    ->default(true)
                    ->helperText('Hanya paket aktif + dalam jadwal yang bisa dikerjakan siswa.'),

                DateTimePicker::make('mulai_pada')
                    ->label('Buka mulai')
                    ->seconds(false)
                    ->helperText('Kosongkan = bisa dikerjakan kapan saja.'),

                DateTimePicker::make('selesai_pada')
                    ->label('Tutup pada')
                    ->seconds(false)
                    ->helperText('Kosongkan = tidak ada batas tutup.'),

                Repeater::make('soal')
                    ->label('Butir soal (pilihan ganda A–D)')
                    ->relationship()
                    ->schema([
                        Textarea::make('teks')
                            ->label('Teks soal')
                            ->required()
                            ->rows(2)
                            ->columnSpanFull(),

                        TextInput::make('opsi_a')->label('Pilihan A')->required()->maxLength(255),
                        TextInput::make('opsi_b')->label('Pilihan B')->required()->maxLength(255),
                        TextInput::make('opsi_c')->label('Pilihan C')->maxLength(255),
                        TextInput::make('opsi_d')->label('Pilihan D')->maxLength(255),
                        TextInput::make('opsi_e')->label('Pilihan E')->maxLength(255),

                        Select::make('kunci')
                            ->label('Kunci jawaban')
                            ->options(['A' => 'A', 'B' => 'B', 'C' => 'C', 'D' => 'D', 'E' => 'E'])
                            ->required()
                            ->default('A'),
                    ])
                    ->columns(4)
                    ->itemLabel(fn (array $state): ?string => str($state['teks'] ?? '')->limit(70)->toString())
                    ->collapsible()
                    ->cloneable()
                    ->addActionLabel('Tambah soal')
                    ->defaultItems(1)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->withCount('soal'))
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('judul')
                    ->label('Judul paket')
                    ->searchable()
                    ->wrap(),

                TextColumn::make('soal_count')
                    ->label('Soal')
                    ->suffix(' butir'),

                TextColumn::make('durasi_menit')
                    ->label('Durasi')
                    ->suffix(' menit'),

                TextColumn::make('jadwal')
                    ->label('Jadwal'),

                IconColumn::make('aktif')
                    ->label('Aktif')
                    ->boolean(),
            ])
            ->recordActions([
                \Filament\Actions\EditAction::make()->label('Ubah'),
            ])
            ->headerActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSpmbPaketSoals::route('/'),
            'create' => Pages\CreateSpmbPaketSoal::route('/create'),
            'edit' => Pages\EditSpmbPaketSoal::route('/{record}/edit'),
        ];
    }
}
