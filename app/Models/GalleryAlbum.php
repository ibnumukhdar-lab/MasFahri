<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GalleryAlbum extends Model
{
    protected $fillable = ['judul', 'slug', 'tanggal', 'keterangan', 'sampul', 'terbit', 'urut'];
    protected $casts = ['tanggal' => 'date', 'terbit' => 'boolean'];
    public function photos() { return $this->hasMany(GalleryPhoto::class)->orderBy('urut'); }
    public function getRouteKeyName(): string { return 'slug'; }
}
