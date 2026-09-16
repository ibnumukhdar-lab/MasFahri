<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['kunci', 'nilai'];

    public static function ambil(string $kunci, ?string $bawaan = null): ?string
    {
        $semua = Cache::rememberForever('settings', fn () => static::pluck('nilai', 'kunci')->all());
        return $semua[$kunci] ?? $bawaan;
    }

    public static function simpan(array $pasangan): void
    {
        foreach ($pasangan as $k => $v) static::updateOrCreate(['kunci' => $k], ['nilai' => $v]);
        Cache::forget('settings');
    }
}
