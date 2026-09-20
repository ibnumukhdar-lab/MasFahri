<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Satu bagian halaman beranda. Jenisnya menentukan bentuk tampilannya,
 * isinya (judul/teks/gambar/daftar) diisi dari panel.
 */
class BerandaBagian extends Model
{
    protected $fillable = [
        'jenis', 'urutan', 'aktif', 'judul', 'subjudul', 'teks',
        'gambar', 'tombol_teks', 'tombol_tautan', 'data',
    ];

    protected $casts = [
        'aktif' => 'boolean',
        'urutan' => 'integer',
        'data' => 'array',
    ];

    public function scopeAktif($q)
    {
        return $q->where('aktif', true);
    }

    /** Alamat gambar: berkas dari galeri/unggahan, atau URL luar apa adanya. */
    public function getGambarUrlAttribute(): ?string
    {
        $nilai = trim((string) $this->gambar);

        if ($nilai === '') {
            return null;
        }

        return str_starts_with($nilai, 'http') ? $nilai : asset('media/'.ltrim($nilai, '/'));
    }

    /** Daftar isi 'data' yang aman dipakai view (selalu array). */
    public function getButirAttribute(): array
    {
        $data = $this->data;

        return is_array($data) ? array_values(array_filter($data, fn ($b) => is_array($b))) : [];
    }

    public static function jenisDaftar(): array
    {
        return [
            'hero' => 'Hero — gambar besar + judul',
            'tentang' => 'Tentang — teks + gambar',
            'keunggulan' => 'Keunggulan — daftar centang',
            'visi_misi' => 'Visi & Misi',
            'program' => 'Program — kartu berisi judul & teks',
            'kurikulum' => 'Kurikulum — kartu berisi judul & teks',
            'statistik' => 'Statistik — angka besar + label',
            'galeri' => 'Galeri — foto dari album terbaru',
            'berita' => 'Berita terbaru — 3 tulisan terakhir',
            'spmb' => 'Ajakan SPMB — teks + formulir pendaftaran',
            'faq' => 'Tanya-jawab (buka-tutup)',
            'cta' => 'Ajakan singkat — pita navy + tombol',
        ];
    }

    public static function labelJenis(?string $jenis): string
    {
        $semua = static::jenisDaftar();

        return $semua[$jenis] ?? (string) $jenis;
    }

    /** Isi bawaan untuk bagian yang baru dibuat, supaya tidak kosong melompong. */
    public static function bawaan(string $jenis): array
    {
        return match ($jenis) {
            'hero' => ['judul' => 'Judul besar beranda', 'subjudul' => 'Kalimat pembuka singkat di bawah judul.'],
            'tentang' => ['judul' => 'Tentang Sekolah', 'teks' => 'Tuliskan penjelasan singkat tentang sekolah di sini.'],
            'keunggulan' => ['judul' => 'Keunggulan', 'data' => [['teks' => 'Tuliskan satu keunggulan di sini.']]],
            'visi_misi' => ['judul' => 'Visi & Misi', 'teks' => 'Visi sekolah…', 'data' => [['teks' => 'Misi pertama…']]],
            'program' => ['judul' => 'Program Unggulan', 'data' => [['judul' => 'Nama program', 'teks' => 'Penjelasan singkat program.']]],
            'kurikulum' => ['judul' => 'Kurikulum', 'data' => [['judul' => 'Poin kurikulum', 'teks' => 'Penjelasan singkat.']]],
            'statistik' => ['judul' => 'Sekolah dalam Angka', 'data' => [['judul' => '300+', 'teks' => 'Siswa aktif']]],
            'galeri' => ['judul' => 'Prestasi & Kegiatan', 'subjudul' => 'Cuplikan kegiatan siswa.'],
            'berita' => ['judul' => 'Berita Terbaru'],
            'spmb' => ['judul' => 'Daftarkan Putra/Putri Anda', 'teks' => 'SPMB dibuka sepanjang tahun dengan sistem waiting list.'],
            'faq' => ['judul' => 'Pertanyaan yang Sering Diajukan', 'data' => [['judul' => 'Pertanyaan?', 'teks' => 'Jawabannya…']]],
            'cta' => ['judul' => 'Siap bergabung?', 'teks' => 'Hubungi kami untuk informasi lebih lanjut.', 'tombol_teks' => 'Hubungi Kami', 'tombol_tautan' => '/spmb'],
            default => [],
        };
    }
}
