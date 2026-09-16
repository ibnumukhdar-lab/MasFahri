<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $fillable = ['judul', 'slug', 'body', 'blocks', 'status', 'urut', 'tampil_di_menu', 'meta_judul', 'meta_deskripsi', 'wp_id'];
    protected $casts = ['blocks' => 'array', 'tampil_di_menu' => 'boolean'];
    public function getRouteKeyName(): string { return 'slug'; }
    public function scopeTerbit($q) { return $q->where('status', 'terbit'); }
}
