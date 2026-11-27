<?php

namespace App\Filament\Resources\SpmbBankSoals;

use App\Filament\Resources\SpmbBankSoals\Pages;
use App\Models\SpmbBankSoal;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use UnitEnum;

/** Bank soal: simpan soal sekali, pakai di banyak paket. */
class SpmbBankSoalResource extends Resource
{
    protected static ?string $model = SpmbBankSoal::class;

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $slug = 'bank-soal';

    protected static ?string $navigationLabel = 'Bank Soal';

    protected static ?string $modelLabel = 'soal';

    protected static ?string $pluralModelLabel = 'Bank Soal';

    protected static string | UnitEnum | null $navigationGroup = 'Penerimaan SPMB';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Identitas soal')
                    ->schema([
                        Select::make('mata_pelajaran')
                            ->label('Mata pelajaran')
                            ->options(array_combine(SpmbBankSoal::MAPEL, SpmbBankSoal::MAPEL))
                            ->searchable()
                            ->required(),

                        Select::make('tingkat')
                            ->label('Tingkat kesulitan')
                            ->options(SpmbBankSoal::TINGKAT)
                            ->default('sedang')
                            ->required(),

                        Textarea::make('teks')
                            ->label('Teks soal')
                            ->rows(3)
                            ->required()
                            ->columnSpanFull(),

                        TextInput::make('opsi_a')->label('Pilihan A')->required()->maxLength(255),
                        TextInput::make('opsi_b')->label('Pilihan B')->required()->maxLength(255),
                        TextInput::make('opsi_c')->label('Pilihan C')->maxLength(255),
                        TextInput::make('opsi_d')->label('Pilihan D')->maxLength(255),

                        Select::make('kunci')
                            ->label('Kunci jawaban')
                            ->options(['A' => 'A', 'B' => 'B', 'C' => 'C', 'D' => 'D'])
                            ->required(),

                        Toggle::make('aktif')
                            ->label('Aktif (bisa diambil ke paket)')
                            ->default(true),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('mata_pelajaran')
                    ->label('Mata pelajaran')
                    ->badge()
                    ->searchable()
                    ->sortable(),

                TextColumn::make('teks')
                    ->label('Soal')
                    ->limit(80)
                    ->wrap()
                    ->searchable(),

                TextColumn::make('tingkat')
                    ->label('Tingkat')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'mudah' => 'success',
                        'sulit' => 'danger',
                        default => 'warning',
                    })
                    ->formatStateUsing(fn ($state) => SpmbBankSoal::TINGKAT[$state] ?? $state),

                TextColumn::make('kunci')
                    ->label('Kunci')
                    ->badge()
                    ->color('gray'),

                TextColumn::make('aktif')
                    ->label('Aktif')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state ? 'Aktif' : 'Nonaktif')
                    ->color(fn ($state) => $state ? 'success' : 'gray'),

                TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->dateTime('d M Y')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('mata_pelajaran')
                    ->label('Mata pelajaran')
                    ->options(array_combine(SpmbBankSoal::MAPEL, SpmbBankSoal::MAPEL)),

                SelectFilter::make('tingkat')
                    ->label('Tingkat')
                    ->options(SpmbBankSoal::TINGKAT),

                TernaryFilter::make('aktif')->label('Aktif'),
            ])
            ->recordActions([
                EditAction::make()->label('Ubah'),
                DeleteAction::make()->label('Hapus'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSpmbBankSoals::route('/'),
            'create' => Pages\CreateSpmbBankSoal::route('/create'),
            'edit' => Pages\EditSpmbBankSoal::route('/{record}/edit'),
        ];
    }
}
