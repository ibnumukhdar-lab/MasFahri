<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'peran',
        'whatsapp',
        'aktif',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Siapa yang boleh masuk ke panel /kelola.
     * Hanya akun aktif dengan peran 'admin' (peran lain boleh dibuat admin di menu Pengguna).
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return (bool) $this->aktif && $this->peran === 'admin';
    }

    public function alumniProfile()
    {
        return $this->hasOne(AlumniProfile::class);
    }

    public function posts()
    {
        return $this->hasMany(Post::class, 'penulis_id');
    }

    public function getPeranLabelAttribute(): string
    {
        return [
            'admin' => 'Administrator',
            'guru' => 'Guru',
            'musyrif' => 'Musyrif/Musyrifah',
            'ustadz_diniyah' => 'Ustadz Diniyah',
            'orang_tua' => 'Orang Tua',
            'alumni' => 'Alumni',
        ][$this->peran] ?? ucfirst((string) $this->peran);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'aktif' => 'boolean',
        ];
    }
}
