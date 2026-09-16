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
                        TextEntry::make('minat_program')->label('Program diminati')->placeholder('-'),
                        TextEntry::make('nama_siswa')->label('Nama siswa'),
                        TextEntry::make('jenis_kelamin')->label('Jenis kelamin'),
                        TextEntry::make('sekolah_asal')->label('Sekolah asal')->placeholder('-'),
                        TextEntry::make('nama_wali')->label('Nama wali'),
                        TextEntry::make('whatsapp')->label('WhatsApp')->copyable(),
                        TextEntry::make('email')->label('Email')->placeholder('-')->copyable(),
                    ]),

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
