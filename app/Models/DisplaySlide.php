<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DisplaySlide extends Model
{
    protected $fillable = ['berkas', 'judul', 'durasi', 'urut', 'aktif'];

    protected function casts(): array
    {
        return [
            'durasi' => 'integer',
            'urut' => 'integer',
            'aktif' => 'boolean',
        ];
    }

    public function getUrlAttribute(): string
    {
        return str_starts_with($this->berkas, 'http') ? $this->berkas : asset('media/' . ltrim($this->berkas, '/'));
    }

    public function scopeAktif($query)
    {
        return $query->where('aktif', true)->orderBy('urut')->orderBy('id');
    }
}
