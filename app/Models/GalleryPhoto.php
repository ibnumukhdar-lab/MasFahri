<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GalleryPhoto extends Model
{
    protected $fillable = ['gallery_album_id', 'berkas', 'judul', 'urut', 'layar_utama'];

    protected function casts(): array
    {
        return ['layar_utama' => 'boolean'];
    }

    public function album() { return $this->belongsTo(GalleryAlbum::class, 'gallery_album_id'); }

    /** Gambar yang dicentang sebagai latar halaman utama (satu saja). */
    public function scopeLayarUtama($query)
    {
        return $query->where('layar_utama', true);
    }

    /** Jadikan gambar ini satu-satunya latar halaman utama. */
    public function jadikanLayarUtama(bool $nyala = true): void
    {
        if ($nyala) {
            static::query()->whereKeyNot($this->getKey())->update(['layar_utama' => false]);
        }

        $this->update(['layar_utama' => $nyala]);
    }

    public function getUrlAttribute(): string
    {
        return str_starts_with($this->berkas, 'http') ? $this->berkas : asset('media/' . ltrim($this->berkas, '/'));
    }
}
