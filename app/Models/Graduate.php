<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Graduate extends Model
{
    protected $fillable = ['nisn', 'nama', 'kelas', 'tahun_ajaran', 'status', 'pesan', 'tampil'];
    protected $casts = ['tampil' => 'boolean'];
}
