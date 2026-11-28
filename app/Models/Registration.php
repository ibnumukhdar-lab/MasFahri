<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Registration extends Model
{
    protected $fillable = ['nomor', 'kode_tes', 'tahun_ajaran', 'gelombang_id', 'nama_wali', 'nama_siswa', 'jenis_kelamin', 'sekolah_asal', 'minat_program', 'whatsapp', 'email', 'catatan', 'status', 'ip', 'pesan_admin', 'bukti_transfer'];

    /** Gelombang SPMB tempat pendaftar ini masuk. */
    public function gelombang()
    {
        return $this->belongsTo(SpmbGelombang::class, 'gelombang_id');
    }
    public const STATUS = ['baru' => 'Belum terverifikasi', 'dihubungi' => 'Sudah dihubungi', 'lengkapi_formulir' => 'Lengkapi formulir', 'tes' => 'Tahap tes', 'diterima' => 'Diterima', 'ditolak' => 'Tidak diterima'];

    /** URL bukti transfer (disk media, folder spmb-bukti). */
    public function getBuktiTransferUrlAttribute(): ?string
    {
        return $this->bukti_transfer ? asset('media/' . $this->bukti_transfer) : null;
    }

    public function biodata()
    {
        return $this->hasOne(\App\Models\SpmbBiodata::class);
    }

    public function formulir()
    {
        return $this->hasOne(\App\Models\SpmbFormulir::class);
    }

    public function ujian()
    {
        return $this->hasOne(\App\Models\SpmbUjian::class);
    }

    /** Semua percobaan CBT — satu siswa bisa mengerjakan beberapa paket (per mapel). */
    public function ujianSemua()
    {
        return $this->hasMany(\App\Models\SpmbUjian::class);
    }

    /**
     * Ringkasan CBT: jumlah paket wajib, yang sudah selesai, sisa, ketuntasan, rata-rata.
     * Dipakai dasbor, halaman tes, pengumuman, dan penjaga status "Diterima".
     */
    public function progresTes(): array
    {
        $wajib = \App\Models\SpmbPaketSoal::query()
            ->where('aktif', true)->where('wajib', true)->pluck('id');

        $selesai = $this->ujianSemua()->where('status', 'selesai')->get()
            ->filter(fn ($u) => $wajib->contains($u->paket_id));

        $kurang = $wajib->diff($selesai->pluck('paket_id'));

        return [
            'total' => $wajib->count(),
            'selesai' => $selesai->count(),
            'kurang' => $kurang->count(),
            'tuntas' => $wajib->count() > 0 && $kurang->isEmpty(),
            'rata' => $selesai->count() ? (int) round($selesai->avg('skor')) : null,
        ];
    }

    public function nilaiQuran()
    {
        return $this->hasOne(\App\Models\SpmbNilaiQuran::class);
    }

    public function wawancara()
    {
        return $this->hasOne(\App\Models\SpmbWawancara::class);
    }

    /** Kode tes 6 karakter (tanpa 0/O/1/I) — gerbang masuk CBT, tercetak di kartu tes. */
    public static function kodeTesBaru(): string
    {
        $abjad = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';

        do {
            $kode = '';
            for ($i = 0; $i < 6; $i++) {
                $kode .= $abjad[random_int(0, strlen($abjad) - 1)];
            }
        } while (static::where('kode_tes', $kode)->exists());

        return $kode;
    }

    /** Pastikan kode tes sudah terbit (dipanggil saat status masuk tahap tes). */
    public function pastikanKodeTes(): string
    {
        if (! $this->kode_tes) {
            $this->kode_tes = static::kodeTesBaru();
            $this->save();
        }

        return $this->kode_tes;
    }

    /** Nomor pendaftaran berurut per tahun ajaran: SPMB-2026-0001 */
    public static function nomorBaru(string $tahunAjaran): string
    {
        $tahun = explode('/', $tahunAjaran)[0] ?: date('Y');
        $urut = static::where('tahun_ajaran', $tahunAjaran)->count() + 1;
        do {
            $nomor = sprintf('SPMB-%s-%04d', $tahun, $urut);
            $ada = static::where('nomor', $nomor)->exists();
            $urut++;
        } while ($ada);
        return $nomor;
    }
}
