<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeacherLink extends Model
{
    protected $fillable = ['judul', 'url', 'kelompok', 'keterangan', 'aktif', 'urut'];
    protected $casts = ['aktif' => 'boolean'];
    public function scopeAktif($q) { return $q->where('aktif', true)->orderBy('urut'); }
}
