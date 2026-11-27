<?php

namespace App\Filament\Resources\SpmbNilaiQurans;

use App\Filament\Resources\SpmbNilaiQurans\Pages;
use App\Models\SpmbNilaiQuran;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

/**
 * KERANGKA: input nilai baca tulis Al-Qur'an oleh panitia.
 * Tabel & form dasar sudah ada; alur penilaian penuh menyusul.
 */
class SpmbNilaiQuranResource extends Resource
{
    protected static ?string $model = SpmbNilaiQuran::class;

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedBookOpen;

    protected static ?string $slug = 'nilai-quran';

    protected static ?string $navigationLabel = "Nilai Baca Tulis Al-Qur'an";

    protected static ?string $modelLabel = 'Nilai baca tulis';

    protected static ?string $pluralModelLabel = "Nilai baca tulis Al-Qur'an";

    protected static string | UnitEnum | null $navigationGroup = 'SPMB & Alumni';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('registration_id')
                    ->label('Pendaftar')
                    ->relationship('registration', 'nama_siswa')
                    ->searchable(['nomor', 'nama_siswa'])
                    ->required()
                    ->getOptionLabelFromRecordUsing(fn ($record): string => $record->nomor.' — '.$record->nama_siswa)
                    ->unique(ignoreRecord: true)
                    ->columnSpanFull(),

                Section::make("Baca Al-Qur'an")
                    ->schema([
                        TextInput::make('baca_tajwid')->label('Tajwid')->numeric()->minValue(0)->maxValue(100),
                        TextInput::make('baca_makhraj')->label('Makhraj')->numeric()->minValue(0)->maxValue(100),
                        TextInput::make('baca_kelancaran')->label('Kelancaran')->numeric()->minValue(0)->maxValue(100),
                    ])
                    ->columns(3)
                    ->columnSpanFull(),

                Section::make('Tulis Arab')
                    ->schema([
                        TextInput::make('tulis_ketepatan')->label('Ketepatan huruf')->numeric()->minValue(0)->maxValue(100),
                        TextInput::make('tulis_kerapian')->label('Keindahan / kerapian tulisan')->numeric()->minValue(0)->maxValue(100),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                Section::make('Praktek Sholat')
                    ->schema([
                        TextInput::make('sholat_bacaan')->label('Bacaan sholat')->numeric()->minValue(0)->maxValue(100),
                        TextInput::make('sholat_fiqih')->label('Pemahaman fiqih')->numeric()->minValue(0)->maxValue(100),
                        TextInput::make('sholat_gerakan')->label('Kesesuaian gerakan')->numeric()->minValue(0)->maxValue(100),
                    ])
                    ->columns(3)
                    ->columnSpanFull(),

                TextInput::make('penilai')
                    ->label('Penilai')
                    ->maxLength(120),

                Textarea::make('catatan')
                    ->label('Catatan penilai')
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('updated_at', 'desc')
            ->columns([
                TextColumn::make('registration.nomor')
                    ->label('Nomor')
                    ->searchable()
                    ->copyable(),

                TextColumn::make('registration.nama_siswa')
                    ->label('Nama siswa')
                    ->searchable()
                    ->wrap(),

                TextColumn::make('skor_baca')->label('Baca')->placeholder('—')->sortable(),
                TextColumn::make('skor_tulis')->label('Tulis')->placeholder('—')->sortable(),
                TextColumn::make('skor_sholat')->label('Sholat')->placeholder('—')->sortable(),
                TextColumn::make('skor_btq')->label('BTQ')->placeholder('—')->sortable()->weight('bold'),

                TextColumn::make('penilai')->label('Penilai')->placeholder('—'),

                TextColumn::make('updated_at')->label('Diperbarui')->dateTime('d M Y H:i'),
            ])
            ->recordActions([
                \Filament\Actions\EditAction::make()->label('Ubah'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSpmbNilaiQurans::route('/'),
            'create' => Pages\CreateSpmbNilaiQuran::route('/create'),
            'edit' => Pages\EditSpmbNilaiQuran::route('/{record}/edit'),
        ];
    }
}
