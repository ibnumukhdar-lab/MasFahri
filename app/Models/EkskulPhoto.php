<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EkskulPhoto extends Model
{
    protected $fillable = ['ekskul_id', 'berkas', 'judul', 'urut'];

    public function ekskul()
    {
        return $this->belongsTo(Ekskul::class);
    }

    public function getUrlAttribute(): string
    {
        return str_starts_with((string) $this->berkas, 'http')
            ? $this->berkas
            : asset('media/' . ltrim((string) $this->berkas, '/'));
    }
}
