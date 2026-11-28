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
     * Peran yang boleh masuk panel /kelola.
     * - super_admin      : pemilik (Fahrizal) — semua fitur + penetapan peran
     * - operator_website : ditunjuk pemilik — semua fitur konten, TANPA penetapan peran
     * - panitia_spmb     : default semua akun SIAKAD — hanya fitur SPMB
     * - admin            : peran lama (dianggap operator_website)
     */
    public const PERAN_PANEL = ['super_admin', 'operator_website', 'panitia_spmb', 'admin'];

    /** Pilihan peran yang bisa ditetapkan di menu Pengguna. */
    public const PERAN_PILIHAN = [
        'panitia_spmb' => 'Panitia SPMB (hanya fitur SPMB)',
        'operator_website' => 'Operator Website (semua fitur konten)',
        'super_admin' => 'Super Admin (semua fitur + penetapan peran)',
    ];

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
        'nipa',
        'id_siakad',
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
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return (bool) $this->aktif && in_array((string) $this->peran, self::PERAN_PANEL, true);
    }

    /** Pemilik: semua fitur + penetapan peran. */
    public function superAdmin(): bool
    {
        return $this->aktif && in_array((string) $this->peran, ['super_admin', 'admin'], true);
    }

    /** Semua fitur konten (operator website + super admin). */
    public function bolehSemua(): bool
    {
        return $this->aktif && in_array((string) $this->peran, ['super_admin', 'operator_website', 'admin'], true);
    }

    /** Hanya fitur SPMB. */
    public function hanyaSpbm(): bool
    {
        return $this->aktif && (string) $this->peran === 'panitia_spmb';
    }

    public function posts()
    {
        return $this->hasMany(Post::class, 'penulis_id');
    }

    public function getPeranLabelAttribute(): string
    {
        return self::PERAN_PILIHAN[$this->peran] ?? [
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
