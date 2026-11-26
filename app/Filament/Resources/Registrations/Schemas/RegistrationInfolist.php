<?php

namespace App\Filament\Resources\Registrations\Schemas;

use App\Models\Registration;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class RegistrationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Pendaftar')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('nomor')->label('Nomor pendaftaran')->badge()->color('primary'),
                        TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->formatStateUsing(fn (?string $state): string => Registration::STATUS[$state] ?? (string) $state)
                            ->color(fn (?string $state): string => static::warnaStatus($state)),
                        TextEntry::make('tahun_ajaran')->label('Tahun ajaran'),
                        TextEntry::make('minat_program')->label('Jalur pendaftaran')->placeholder('-'),
                        TextEntry::make('nama_siswa')->label('Nama siswa'),
                        TextEntry::make('jenis_kelamin')->label('Jenis kelamin'),
                        TextEntry::make('sekolah_asal')->label('Sekolah asal')->placeholder('-'),
                        TextEntry::make('nama_wali')->label('Nama wali'),
                        TextEntry::make('whatsapp')->label('WhatsApp')->copyable(),
                        TextEntry::make('email')->label('Email')->placeholder('-')->copyable(),
                    ]),

                Section::make('Biodata siswa (diisi pendaftar)')
                    ->schema([
                        TextEntry::make('biodata.nik')->label('NIK')->placeholder('-'),
                        TextEntry::make('biodata.nisn')->label('NISN')->placeholder('-'),
                        TextEntry::make('biodata.tempat_lahir')->label('Tempat lahir')->placeholder('-'),
                        TextEntry::make('biodata.tanggal_lahir')->label('Tanggal lahir')->date('d M Y')->placeholder('-'),
                        TextEntry::make('biodata.alamat')->label('Alamat')->placeholder('-')->columnSpanFull(),
                        TextEntry::make('biodata.anak_ke')->label('Anak ke-')->placeholder('-'),
                        TextEntry::make('biodata.jumlah_saudara')->label('Jumlah saudara')->placeholder('-'),
                        TextEntry::make('biodata.nama_ayah')->label('Nama ayah')->placeholder('-'),
                        TextEntry::make('biodata.pekerjaan_ayah')->label('Pekerjaan ayah')->placeholder('-'),
                        TextEntry::make('biodata.nama_ibu')->label('Nama ibu')->placeholder('-'),
                        TextEntry::make('biodata.pekerjaan_ibu')->label('Pekerjaan ibu')->placeholder('-'),
                        TextEntry::make('biodata.telepon_rumah')->label('Telepon rumah')->placeholder('-'),
                        TextEntry::make('biodata.hobi')->label('Hobi')->placeholder('-'),
                        TextEntry::make('biodata.persen')->label('Kelengkapan')->badge()
                            ->color(fn ($state): string => $state >= 80 ? 'success' : ($state >= 40 ? 'warning' : 'gray')),
                    ])
                    ->columns(2)
                    ->visible(fn ($record): bool => $record->biodata()->exists()),

                Section::make('Bukti transfer biaya formulir')
                    ->schema([
                        TextEntry::make('bukti_transfer')
                            ->label('Bukti transfer')
                            ->state(fn ($record): ?string => $record->bukti_transfer ? $record->bukti_transfer_url : null)
                            ->formatStateUsing(fn (?string $state): string => $state ? 'Lihat bukti transfer' : 'Belum ada')
                            ->url(fn (?string $state): ?string => $state)
                            ->openUrlInNewTab()
                            ->color(fn (?string $state): string => $state ? 'primary' : 'gray')
                            ->columnSpanFull(),
                    ])
                    ->visible(fn ($record): bool => (bool) $record->bukti_transfer),

                Section::make('Catatan & tindak lanjut')
                    ->schema([
                        TextEntry::make('catatan')->label('Catatan pendaftar')->placeholder('-')->columnSpanFull(),
                        TextEntry::make('pesan_admin')->label('Pesan admin')->placeholder('-')->columnSpanFull(),
                        TextEntry::make('created_at')->label('Waktu daftar')->dateTime('d M Y H:i'),
                        TextEntry::make('updated_at')->label('Terakhir diubah')->dateTime('d M Y H:i'),
                        TextEntry::make('ip')->label('IP')->placeholder('-'),
                    ]),
            ]);
    }

    public static function warnaStatus(?string $status): string
    {
        return match ($status) {
            'baru' => 'warning',
            'dihubungi' => 'info',
            'tes' => 'primary',
            'diterima' => 'success',
            'ditolak' => 'danger',
            default => 'gray',
        };
    }
}
