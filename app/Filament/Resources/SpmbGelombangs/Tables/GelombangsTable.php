<?php

namespace App\Filament\Resources\SpmbGelombangs\Tables;

use App\Http\Controllers\SpmbController;
use App\Models\SpmbGelombang;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class GelombangsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('tahun_ajaran', 'desc')
            ->columns([
                TextColumn::make('nama')
                    ->label('Gelombang')
                    ->weight('bold')
                    ->description(fn (SpmbGelombang $r): string => $r->tahun_ajaran)
                    ->searchable()
                    ->sortable(),

                TextColumn::make('rentang_label')
                    ->label('Pendaftaran')
                    ->wrap(),

                TextColumn::make('jadwal_tes_count')
                    ->label('Jadwal tes')
                    ->counts('jadwalTes')
                    ->badge()
                    ->color(fn ($state): string => $state > 0 ? 'success' : 'warning')
                    ->formatStateUsing(fn ($state): string => $state.' jadwal'),

                TextColumn::make('pendaftar_count')
                    ->label('Pendaftar')
                    ->counts('pendaftar')
                    ->badge()
                    ->color('gray'),

                TextColumn::make('status_label')
                    ->label('Status')
                    ->badge()
                    ->color(fn (SpmbGelombang $r): string => match ($r->status_label) {
                        'Sedang dibuka' => 'success',
                        'Belum dibuka' => 'info',
                        'Pendaftaran ditutup' => 'warning',
                        default => 'gray',
                    }),

                IconColumn::make('aktif')
                    ->label('Tampil')
                    ->boolean(),
            ])
            ->recordActions([
                EditAction::make()
                    ->label('Atur jadwal'),

                Action::make('duplikat')
                    ->label('Duplikat')
                    ->icon('heroicon-o-document-duplicate')
                    ->color('gray')
                    ->schema([
                        TextInput::make('nama')
                            ->label('Nama gelombang baru')
                            ->default(fn (SpmbGelombang $record): string => Str::contains($record->nama, 'Gelombang')
                                ? 'Gelombang '.((int) filter_var($record->nama, FILTER_SANITIZE_NUMBER_INT) + 1)
                                : $record->nama.' (salinan)')
                            ->required()
                            ->maxLength(60),

                        Select::make('tahun_ajaran')
                            ->label('Tahun ajaran')
                            ->options(array_combine(SpmbController::TAHUN_AJARAN, SpmbController::TAHUN_AJARAN))
                            ->default(fn (SpmbGelombang $record): string => $record->tahun_ajaran)
                            ->required()
                            ->native(false),
                    ])
                    ->action(function (SpmbGelombang $record, array $data): void {
                        $baru = $record->replicate(['jadwal_tes_count', 'pendaftar_count']);
                        $baru->nama = $data['nama'];
                        $baru->tahun_ajaran = $data['tahun_ajaran'];
                        $baru->save();

                        foreach ($record->jadwalTes()->get() as $jadwal) {
                            $salin = $jadwal->replicate();
                            $salin->gelombang_id = $baru->id;
                            // Geser setahun agar tidak bentrok dengan jadwal lama.
                            $salin->tanggal = $jadwal->tanggal?->copy()->addYear();
                            $salin->save();
                        }

                        Notification::make()
                            ->title('Gelombang diduplikat')
                            ->body("{$baru->nama} ({$baru->tahun_ajaran}) dengan {$record->jadwalTes()->count()} jadwal tes — silakan sesuaikan tanggalnya.")
                            ->success()
                            ->send();
                    }),

                DeleteAction::make()
                    ->requiresConfirmation()
                    ->modalDescription('Jadwal tes pada gelombang ini ikut terhapus. Pendaftar yang sudah tercatat tetap ada.'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('Belum ada gelombang')
            ->emptyStateDescription('Tambahkan gelombang pertama (mis. Gelombang 1 2027/2028) lalu isi jadwal tesnya.')
            ->emptyStateIcon('heroicon-o-calendar-days');
    }
}
