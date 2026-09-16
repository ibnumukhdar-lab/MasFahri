<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class Post extends Model
{
    protected $fillable = ['judul', 'slug', 'ringkasan', 'body', 'status', 'terbit_pada', 'gambar_sampul', 'penulis_id', 'wp_id', 'dilihat'];

    protected $casts = ['terbit_pada' => 'datetime'];

    public function categories() { return $this->belongsToMany(Category::class, 'category_post'); }
    public function penulis() { return $this->belongsTo(User::class, 'penulis_id'); }
    public function getRouteKeyName(): string { return 'slug'; }

    public function scopeTerbit(Builder $q): Builder
    {
        return $q->where('status', 'terbit')->whereNotNull('terbit_pada')->where('terbit_pada', '<=', now());
    }

    public function getKategoriUtamaAttribute(): ?Category
    {
        return $this->categories->first();
    }

    public function getRingkasAttribute(): string
    {
        $teks = $this->ringkasan ?: strip_tags((string) $this->body);
        $teks = trim(preg_replace('/\s+/', ' ', $teks));
        return Str::limit($teks, 150);
    }

    /** URL gambar sampul: URL lama WordPress dibiarkan, path lokal lewat disk media. */
    public function getGambarUrlAttribute(): ?string
    {
        if (! $this->gambar_sampul) return null;
        if (str_starts_with($this->gambar_sampul, 'http')) return $this->gambar_sampul;
        return asset('media/' . ltrim($this->gambar_sampul, '/'));
    }

    public function getTanggalIndonesiaAttribute(): string
    {
        $bulan = [1=>'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
        $t = $this->terbit_pada;
        return $t ? $t->day . ' ' . $bulan[(int) $t->month] . ' ' . $t->year : '';
    }
}
