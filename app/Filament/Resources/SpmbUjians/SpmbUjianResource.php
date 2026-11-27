<?php

namespace App\Filament\Resources\SpmbUjians;

use App\Filament\Resources\SpmbUjians\Pages;
use App\Models\SpmbUjian;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

class SpmbUjianResource extends Resource
{
    protected static ?string $model = SpmbUjian::class;

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedChartBar;

    protected static ?string $slug = 'hasil-tes-cbt';

    protected static ?string $navigationLabel = 'Log Pengerjaan CBT';

    protected static ?string $modelLabel = 'Hasil tes';

    protected static ?string $pluralModelLabel = 'Hasil tes CBT';

    protected static string | UnitEnum | null $navigationGroup = 'Penerimaan SPMB';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('selesai_pada', 'desc')
            ->columns([
                TextColumn::make('registration.nomor')
                    ->label('Nomor pendaftaran')
                    ->searchable()
                    ->copyable(),

                TextColumn::make('registration.nama_siswa')
                    ->label('Nama siswa')
                    ->searchable()
                    ->wrap(),

                TextColumn::make('paket.judul')
                    ->label('Paket')
                    ->wrap()
                    ->toggleable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => $state === 'selesai' ? 'Selesai' : 'Sedang mengerjakan')
                    ->color(fn (string $state): string => $state === 'selesai' ? 'success' : 'warning'),

                TextColumn::make('skor')
                    ->label('Skor')
                    ->placeholder('—')
                    ->sortable(),

                TextColumn::make('benar')
                    ->label('Benar')
                    ->formatStateUsing(fn ($state, SpmbUjian $record): string => filled($state) ? $state.'/'.$record->total : '—'),

                TextColumn::make('pelanggaran')
                    ->label('Pelanggaran')
                    ->badge()
                    ->color(fn ($state): string => (int) $state > 0 ? 'danger' : 'gray'),

                TextColumn::make('mulai_pada')
                    ->label('Mulai')
                    ->dateTime('d M Y H:i')
                    ->toggleable(),

                TextColumn::make('selesai_pada')
                    ->label('Selesai')
                    ->dateTime('d M Y H:i')
                    ->placeholder('belum'),
            ])
            ->filters([
                SelectFilter::make('paket_id')
                    ->label('Paket')
                    ->relationship('paket', 'judul'),

                SelectFilter::make('status')
                    ->label('Status')
                    ->options(['sedang' => 'Sedang mengerjakan', 'selesai' => 'Selesai']),
            ])
            ->recordActions([
                Action::make('reset')
                    ->label('Reset')
                    ->icon('heroicon-o-arrow-path')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Reset pengerjaan tes?')
                    ->modalDescription('Jawaban dan skor percobaan ini dihapus supaya siswa bisa mengerjakan ulang. Gunakan hanya untuk kendala teknis.')
                    ->action(function (SpmbUjian $record): void {
                        Notification::make()
                            ->title('Percobaan tes '.$record->registration?->nomor.' direset')
                            ->success()
                            ->send();
                        $record->delete();
                    }),
            ])
            ->recordUrl(null);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSpmbUjians::route('/'),
        ];
    }
}
