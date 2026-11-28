<?php

namespace App\Filament\Resources\SpmbWawancaras;

use App\Filament\Resources\SpmbWawancaras\Pages;
use App\Models\SpmbWawancara;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
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

/** Nilai wawancara minat & bakat — diisi penguji/panitia. */
class SpmbWawancaraResource extends Resource
{
    protected static ?string $model = SpmbWawancara::class;

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedChatBubbleLeftRight;

    protected static ?string $slug = 'wawancara';

    protected static ?string $navigationLabel = 'Wawancara Minat & Bakat';

    protected static ?string $modelLabel = 'nilai wawancara';

    protected static ?string $pluralModelLabel = 'Wawancara Minat & Bakat';

    protected static string | UnitEnum | null $navigationGroup = 'Penerimaan SPMB';

    protected static ?int $navigationSort = 7;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Peserta')
                    ->schema([
                        Select::make('registration_id')
                            ->label('Pendaftar')
                            ->relationship('registration', 'nama_siswa')
                            ->searchable(['nomor', 'nama_siswa'])
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->getOptionLabelFromRecordUsing(fn ($record): string => $record->nomor.' — '.$record->nama_siswa)
                            ->columnSpanFull(),

                        DatePicker::make('tanggal')
                            ->label('Tanggal wawancara')
                            ->native(false)
                            ->displayFormat('d M Y'),

                        TextInput::make('penilai')
                            ->label('Nama penguji / pewawancara')
                            ->maxLength(120),
                    ])
                    ->columns(2),

                Section::make('Nilai aspek (0–100)')
                    ->schema([
                        TextInput::make('nilai_minat')->label('Minat')->numeric()->minValue(0)->maxValue(100),
                        TextInput::make('nilai_bakat')->label('Bakat')->numeric()->minValue(0)->maxValue(100),
                        TextInput::make('nilai_komunikasi')->label('Komunikasi')->numeric()->minValue(0)->maxValue(100),
                    ])
                    ->columns(3),

                Textarea::make('catatan')
                    ->label('Catatan penguji')
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('updated_at', 'desc')
            ->columns([
                TextColumn::make('registration.nomor')->label('Nomor')->searchable(),
                TextColumn::make('registration.nama_siswa')->label('Nama siswa')->searchable()->wrap(),

                TextColumn::make('nilai_minat')->label('Minat')->placeholder('—')->alignCenter(),
                TextColumn::make('nilai_bakat')->label('Bakat')->placeholder('—')->alignCenter(),
                TextColumn::make('nilai_komunikasi')->label('Komunikasi')->placeholder('—')->alignCenter(),

                TextColumn::make('skor_wawancara')
                    ->label('Nilai wawancara')
                    ->placeholder('—')
                    ->weight('bold')
                    ->alignCenter(),

                TextColumn::make('tanggal')->label('Tanggal')->date('d M Y')->placeholder('—'),
                TextColumn::make('penilai')->label('Penguji')->placeholder('—'),
            ])
            ->recordActions([
                EditAction::make()->label('Ubah'),
                DeleteAction::make()->label('Hapus'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSpmbWawancaras::route('/'),
            'create' => Pages\CreateSpmbWawancara::route('/create'),
            'edit' => Pages\EditSpmbWawancara::route('/{record}/edit'),
        ];
    }
}
