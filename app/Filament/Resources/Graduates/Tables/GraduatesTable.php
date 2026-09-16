<?php

namespace App\Filament\Resources\Graduates\Tables;

use App\Filament\Resources\Graduates\GraduateResource;
use App\Models\Graduate;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class GraduatesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('nama')
            ->columns([
                TextColumn::make('nisn')
                    ->label('NISN')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('nama')
                    ->label('Nama')
                    ->searchable()
                    ->sortable()
                    ->wrap(),

                TextColumn::make('kelas')
                    ->label('Kelas')
                    ->searchable(),

                TextColumn::make('tahun_ajaran')
                    ->label('Tahun ajaran')
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => $state === 'lulus' ? 'Lulus' : 'Tidak lulus')
                    ->color(fn (?string $state): string => $state === 'lulus' ? 'success' : 'danger'),

                IconColumn::make('tampil')
                    ->label('Tampil')
                    ->boolean(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'lulus' => 'Lulus',
                        'tidak_lulus' => 'Tidak lulus',
                    ]),

                SelectFilter::make('tahun_ajaran')
                    ->label('Tahun ajaran')
                    ->options(fn (): array => Graduate::query()
                        ->whereNotNull('tahun_ajaran')
                        ->distinct()
                        ->orderByDesc('tahun_ajaran')
                        ->pluck('tahun_ajaran', 'tahun_ajaran')
                        ->all()),
            ])
            ->headerActions([
                Action::make('unduh_template')
                    ->label('Unduh template')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('gray')
                    ->url(route('template.kelulusan'))
                    ->openUrlInNewTab(false),
                Action::make('impor_csv')
                    ->label('Impor CSV')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->modalDescription('Pakai tombol "Unduh template" di samping, isi datanya (kolom: nisn, nama, kelas, status, pesan, tahun_ajaran), lalu unggah berkasnya di sini. Baris dengan NISN yang sudah ada akan diperbarui; baris contoh pada template otomatis dilewati.')
                    ->schema([
                        FileUpload::make('berkas')
                            ->label('Berkas CSV')
                            ->acceptedFileTypes(['text/csv', 'text/plain', 'application/csv', 'application/vnd.ms-excel'])
                            ->disk('local')
                            ->directory('impor-kelulusan')
                            ->storeFiles(false)
                            ->required(),
                    ])
                    ->action(function (array $data): void {
                        $file = is_array($data['berkas']) ? Arr::first($data['berkas']) : $data['berkas'];

                        if (! $file) {
                            Notification::make()->title('Berkas tidak terbaca')->danger()->send();

                            return;
                        }

                        $handle = fopen($file->getRealPath(), 'r');

                        if ($handle === false) {
                            Notification::make()->title('Berkas tidak bisa dibuka')->danger()->send();

                            return;
                        }

                        $header = null;
                        $baru = 0;
                        $diperbarui = 0;
                        $dilewati = 0;
                        $baris = 0;

                        DB::transaction(function () use ($handle, &$header, &$baru, &$diperbarui, &$dilewati, &$baris): void {
                            while (($kolom = fgetcsv($handle, 0, ',', '"', '\\')) !== false) {
                                if ($kolom === [null] || $kolom === []) {
                                    continue;
                                }

                                if ($header === null) {
                                    $header = array_map(fn ($h) => strtolower(trim((string) $h)), $kolom);
                                    continue;
                                }

                                $baris++;
                                $data = array_combine(
                                    array_slice($header, 0, count($kolom)),
                                    array_slice($kolom, 0, count($header)),
                                );

                                if (! $data) {
                                    $dilewati++;
                                    continue;
                                }

                                $nisn = trim((string) ($data['nisn'] ?? ''));
                                $nama = trim((string) ($data['nama'] ?? ''));

                                if ($nisn === '' || $nama === '') {
                                    $dilewati++;
                                    continue;
                                }

                                $status = strtolower(trim((string) ($data['status'] ?? 'lulus')));
                                $status = in_array($status, ['lulus', 'tidak_lulus'], true) ? $status : 'lulus';

                                $graduate = Graduate::firstOrNew(['nisn' => $nisn]);
                                $isBaru = ! $graduate->exists;

                                $graduate->fill([
                                    'nama' => $nama,
                                    'kelas' => trim((string) ($data['kelas'] ?? '')) ?: null,
                                    'tahun_ajaran' => trim((string) ($data['tahun_ajaran'] ?? $data['tahun'] ?? '')) ?: null,
                                    'status' => $status,
                                    'pesan' => trim((string) ($data['pesan'] ?? '')) ?: null,
                                    'tampil' => true,
                                ])->save();

                                $isBaru ? $baru++ : $diperbarui++;
                            }
                        });

                        fclose($handle);

                        Notification::make()
                            ->title('Impor selesai')
                            ->body("{$baris} baris diproses · {$baru} baru · {$diperbarui} diperbarui · {$dilewati} dilewati")
                            ->success()
                            ->send();
                    }),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
