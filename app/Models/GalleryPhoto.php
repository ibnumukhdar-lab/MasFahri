<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GalleryPhoto extends Model
{
    protected $fillable = ['gallery_album_id', 'berkas', 'judul', 'urut'];
    public function album() { return $this->belongsTo(GalleryAlbum::class, 'gallery_album_id'); }
    public function getUrlAttribute(): string
    {
        return str_starts_with($this->berkas, 'http') ? $this->berkas : asset('media/' . ltrim($this->berkas, '/'));
    }
}
