<?php

namespace App\Filament\Pages;

use App\Support\AkunSiakad;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use DanHarrin\LivewireRateLimiting\WithRateLimiting;
use Filament\Auth\Http\Responses\Contracts\LoginResponse;
use Filament\Auth\Pages\Login;
use Filament\Facades\Filament;
use Illuminate\Contracts\Support\Htmlable;

/**
 * Halaman masuk panel /kelola memakai akun SIAKAD.
 *
 * Urutan pemeriksaan: akun lokal dulu (akun lama panel), lalu akun SIAKAD
 * (email + kata sandi yang sama seperti di siakad.smaitarafah.sch.id).
 * Akun SIAKAD yang baru pertama masuk otomatis dibuatkan salinan lokal
 * dengan peran "panitia_spmb"; pemilik bisa menaikkannya menjadi
 * operator_website / super_admin di menu Pengguna.
 */
class MasukKelola extends Login
{
    use WithRateLimiting;

    public function getHeading(): string | Htmlable
    {
        return 'Masuk Kelola';
    }

    public function getSubheading(): string | Htmlable | null
    {
        return 'Gunakan email dan kata sandi SIAKAD Anda (siakad.smaitarafah.sch.id).';
    }

    public function authenticate(): ?LoginResponse
    {
        try {
            $this->rateLimit(5);
        } catch (TooManyRequestsException $exception) {
            $this->getRateLimitedNotification($exception)?->send();

            return null;
        }

        $data = $this->form->getState();

        $user = AkunSiakad::cocokkan(
            (string) ($data['email'] ?? ''),
            (string) ($data['password'] ?? ''),
        );

        if ((! $user) || (! $this->isUserAllowedToAccessPanel($user))) {
            $this->throwFailureValidationException();
        }

        Filament::auth()->login($user, (bool) ($data['remember'] ?? false));

        session()->regenerate();

        return app(LoginResponse::class);
    }
}
