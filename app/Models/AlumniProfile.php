<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AlumniProfile extends Model
{
    protected $fillable = ['user_id', 'tahun_lulus', 'kelas_akhir', 'lanjut_ke', 'nama_kampus', 'kota', 'instagram', 'pesan', 'foto', 'tampil_publik', 'diverifikasi_pada'];
    protected $casts = ['tampil_publik' => 'boolean', 'diverifikasi_pada' => 'datetime'];
    public function user() { return $this->belongsTo(User::class); }
}
