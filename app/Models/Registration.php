<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Registration extends Model
{
    protected $fillable = ['nomor', 'tahun_ajaran', 'nama_wali', 'nama_siswa', 'jenis_kelamin', 'sekolah_asal', 'minat_program', 'whatsapp', 'email', 'catatan', 'status', 'ip', 'pesan_admin'];
    public const STATUS = ['baru' => 'Baru', 'dihubungi' => 'Sudah dihubungi', 'tes' => 'Tahap tes', 'diterima' => 'Diterima', 'ditolak' => 'Tidak diterima'];

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
