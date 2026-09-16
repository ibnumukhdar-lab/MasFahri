<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['nama', 'slug', 'deskripsi', 'warna', 'wp_id'];
    public function posts() { return $this->belongsToMany(Post::class, 'category_post'); }
    public function getRouteKeyName(): string { return 'slug'; }
}
