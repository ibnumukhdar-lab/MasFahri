<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ekskul extends Model
{
    protected $fillable = ['nama', 'slug', 'keterangan', 'ikon', 'sampul', 'terbit', 'urut'];

    protected $casts = ['terbit' => 'boolean'];

    public function photos()
    {
        return $this->hasMany(EkskulPhoto::class)->orderBy('urut');
    }

    public function scopeTampil($q)
    {
        return $q->where('terbit', true);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /** Gambar sampul: berkas yang diunggah, URL luar, atau foto pertama galeri. */
    public function getSampulUrlAttribute(): ?string
    {
        $s = trim((string) $this->sampul);
        if ($s !== '') {
            return str_starts_with($s, 'http') ? $s : asset('media/' . ltrim($s, '/'));
        }

        $foto = $this->relationLoaded('photos') ? $this->photos->first() : $this->photos()->first();

        return $foto ? $foto->url : null;
    }
}
