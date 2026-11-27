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

                Section::make('Kartu tes & hasil CBT')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('kode_tes')
                            ->label('Kode login tes (CBT)')
                            ->copyable()
                            ->placeholder('belum terbit — muncul saat status jadi Tahap tes'),
                        TextEntry::make('ujian.status')
                            ->label('Pengerjaan CBT')
                            ->badge()
                            ->formatStateUsing(fn (?string $state): string => match ($state) {
                                'sedang' => 'Sedang mengerjakan',
                                'selesai' => 'Selesai',
                                default => 'Belum dikerjakan',
                            })
                            ->color(fn (?string $state): string => match ($state) {
                                'sedang' => 'warning',
                                'selesai' => 'success',
                                default => 'gray',
                            }),
                        TextEntry::make('ujian.skor')->label('Skor CBT')->placeholder('—'),
                        TextEntry::make('ujian.pelanggaran')->label('Pelanggaran')->placeholder('—'),
                        TextEntry::make('ujian.selesai_pada')->label('Selesai pada')->dateTime('d M Y H:i')->placeholder('—'),
                        TextEntry::make('nilaiQuran.nilai_baca')->label('Nilai baca Al-Qur\'an')->placeholder('—'),
                        TextEntry::make('nilaiQuran.nilai_tulis')->label('Nilai tulis Al-Qur\'an')->placeholder('—'),
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

                Section::make('Formulir pendaftaran murid baru (diisi pendaftar)')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('formulir.nama_panggilan')->label('Nama panggilan')->placeholder('-'),
                        TextEntry::make('formulir.golongan_darah')->label('Golongan darah')->placeholder('-'),
                        TextEntry::make('formulir.suku_bangsa')->label('Suku bangsa')->placeholder('-'),
                        TextEntry::make('formulir.nisn')->label('NISN (formulir)')->placeholder('-'),
                        TextEntry::make('formulir.nik')->label('NIK (formulir)')->placeholder('-'),
                        TextEntry::make('formulir.tempat_lahir')->label('Tempat lahir')->placeholder('-'),
                        TextEntry::make('formulir.tanggal_lahir')->label('Tanggal lahir')->date('d M Y')->placeholder('-'),
                        TextEntry::make('formulir.anak_ke')->label('Anak ke-')->placeholder('-'),
                        TextEntry::make('formulir.dari_bersaudara')->label('Dari ... bersaudara')->placeholder('-'),
                        TextEntry::make('formulir.alamat_jalan')->label('Alamat (jalan)')->placeholder('-')->columnSpanFull(),
                        TextEntry::make('formulir.kelurahan')->label('Kelurahan/desa')->placeholder('-'),
                        TextEntry::make('formulir.kecamatan')->label('Kecamatan')->placeholder('-'),
                        TextEntry::make('formulir.ayah_nama')->label('Nama ayah')->placeholder('-'),
                        TextEntry::make('formulir.ayah_ttl')->label('Tempat tanggal lahir ayah')->placeholder('-'),
                        TextEntry::make('formulir.ayah_pendidikan')->label('Pendidikan ayah')->placeholder('-'),
                        TextEntry::make('formulir.ayah_pekerjaan')->label('Pekerjaan ayah')->placeholder('-'),
                        TextEntry::make('formulir.ayah_penghasilan')->label('Penghasilan ayah')
                            ->formatStateUsing(fn (?string $state): string => match ($state) {
                                '<2jt' => '< Rp 2.000.000',
                                '2-5jt' => 'Rp 2.000.000 – Rp 5.000.000',
                                '>5jt' => '> Rp 5.000.000',
                                default => $state ?: '-',
                            }),
                        TextEntry::make('formulir.ayah_telepon')->label('Telepon ayah')->placeholder('-'),
                        TextEntry::make('formulir.ibu_nama')->label('Nama ibu')->placeholder('-'),
                        TextEntry::make('formulir.ibu_ttl')->label('Tempat tanggal lahir ibu')->placeholder('-'),
                        TextEntry::make('formulir.ibu_pendidikan')->label('Pendidikan ibu')->placeholder('-'),
                        TextEntry::make('formulir.ibu_pekerjaan')->label('Pekerjaan ibu')->placeholder('-'),
                        TextEntry::make('formulir.ibu_penghasilan')->label('Penghasilan ibu')
                            ->formatStateUsing(fn (?string $state): string => match ($state) {
                                '<2jt' => '< Rp 2.000.000',
                                '2-5jt' => 'Rp 2.000.000 – Rp 5.000.000',
                                '>5jt' => '> Rp 5.000.000',
                                default => $state ?: '-',
                            }),
                        TextEntry::make('formulir.ibu_telepon')->label('Telepon ibu')->placeholder('-'),
                        TextEntry::make('formulir.foto_latar_merah')
                            ->label('Pas foto latar merah')
                            ->state(fn ($record): ?string => $record->formulir?->foto_url)
                            ->formatStateUsing(fn (?string $state): string => $state ? 'Lihat pas foto latar merah' : 'Belum ada')
                            ->url(fn (?string $state): ?string => $state)
                            ->openUrlInNewTab()
                            ->color(fn (?string $state): string => $state ? 'primary' : 'gray'),
                        TextEntry::make('formulir.terkirim_pada')->label('Formulir terkirim')->dateTime('d M Y H:i')->placeholder('-'),
                    ])
                    ->visible(fn ($record): bool => $record->formulir()->exists()),

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
            'lengkapi_formulir' => 'warning',
            'tes' => 'primary',
            'diterima' => 'success',
            'ditolak' => 'danger',
            default => 'gray',
        };
    }
}
