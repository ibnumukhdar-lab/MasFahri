<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * Jembatan akun SIAKAD → panel /kelola.
 *
 * Akun SIAKAD (siakad.smaitarafah.sch.id) dipakai apa adanya: email + kata sandi
 * yang sama. Panel tidak menyimpan sandi mereka sendiri untuk akun SIAKAD —
 * setiap login diverifikasi ke basis data SIAKAD (koneksi `siakad`, baca saja),
 * lalu dibuatkan/padamkan salinan lokal supaya bisa diberi PERAN panel.
 *
 * Peran bawaan akun SIAKAD = panitia_spmb (hanya fitur SPMB). Pemilik bisa
 * mengubahnya menjadi operator_website / super_admin di menu Pengguna.
 */
class AkunSiakad
{
    /** Akun super admin cadangan bila kolom peran SIAKAD tak terbaca. */
    public const EMAIL_SUPER_ADMIN = ['ibnu.mukhdar@gmail.com', 'fahrizalmukhdar1992@gmail.com'];

    /**
     * Cocokkan email + sandi dengan akun lokal dulu, lalu akun SIAKAD.
     * Mengembalikan User siap login (sudah tersimpan lokal), atau null.
     */
    public static function cocokkan(string $email, string $sandi): ?User
    {
        $email = Str::lower(trim($email));
        if ($email === '' || $sandi === '') {
            return null;
        }

        // 1) Akun lokal (akun lama panel tetap bisa masuk dengan sandinya sendiri).
        $lokal = User::query()->whereRaw('LOWER(email) = ?', [$email])->first();
        if ($lokal && Hash::check($sandi, $lokal->password)) {
            return $lokal;
        }

        // 2) Akun SIAKAD.
        $baris = self::barisSiakad($email);
        if (! $baris || ! Hash::check($sandi, (string) $baris->password)) {
            return null;
        }

        return self::pastikanAkunLokal($baris);
    }

    /** Baris users SIAKAD berdasarkan email (null bila koneksi/tabel tak tersedia). */
    public static function barisSiakad(string $email): ?object
    {
        try {
            return DB::connection('siakad')->table('users')
                ->whereRaw('LOWER(email) = ?', [Str::lower(trim($email))])
                ->first();
        } catch (\Throwable $e) {
            Log::warning('AkunSiakad: gagal membaca basis data SIAKAD — '.$e->getMessage());

            return null;
        }
    }

    /**
     * Buat/perbarui akun lokal dari baris users SIAKAD.
     * Sandi lokal diisi acak (tidak pernah dipakai; verifikasi selalu ke SIAKAD).
     */
    public static function pastikanAkunLokal(object $baris): User
    {
        $email = Str::lower(trim((string) $baris->email));
        $peran = self::peranUntuk($baris);

        $user = User::query()->whereRaw('LOWER(email) = ?', [$email])->first()
            ?? new User(['email' => $email]);

        if (! $user->exists) {
            $user->password = Str::password(32);
            $user->aktif = true;
        }

        $user->name = trim((string) ($baris->name ?? $email)) ?: $email;

        // Peran: akun SIAKAD yang belum punya peran panel disamakan menjadi
        // panitia_spmb. Peran panel yang SUDAH ditetapkan pemilik
        // (panitia_spmb / operator_website / super_admin) TIDAK diubah.
        if (in_array($email, self::EMAIL_SUPER_ADMIN, true)) {
            $user->peran = 'super_admin';
        } elseif (! in_array((string) $user->peran, User::PERAN_PANEL, true)) {
            $user->peran = $peran;
        }

        if (Schema::hasColumn('users', 'nipa')) {
            $user->nipa = $baris->nipa ?? null;
        }

        if (Schema::hasColumn('users', 'id_siakad')) {
            $user->id_siakad = $baris->id ?? null;
        }

        $user->save();

        return $user;
    }

    /** Peran bawaan untuk akun SIAKAD baru. */
    public static function peranUntuk(object $baris): string
    {
        $email = Str::lower(trim((string) ($baris->email ?? '')));
        if (in_array($email, self::EMAIL_SUPER_ADMIN, true)) {
            return 'super_admin';
        }

        try {
            $peranSiakad = DB::connection('siakad')->table('model_has_roles as m')
                ->join('roles as r', 'r.id', '=', 'm.role_id')
                ->where('m.model_id', $baris->id)
                ->pluck('r.name')
                ->all();

            foreach ($peranSiakad as $nama) {
                if (Str::lower((string) $nama) === 'super admin') {
                    return 'super_admin';
                }
            }
        } catch (\Throwable $e) {
            Log::warning('AkunSiakad: peran SIAKAD tak terbaca — '.$e->getMessage());
        }

        return 'panitia_spmb';
    }

    /**
     * Tarik SEMUA akun SIAKAD ke tabel users lokal (tanpa menunggu mereka login),
     * supaya pemilik bisa langsung menetapkan peran.
     *
     * @return array{baru:int, diperbarui:int, total:int, galat:?string}
     */
    public static function tarikSemua(): array
    {
        try {
            $daftar = DB::connection('siakad')->table('users')->orderBy('id')->get();
        } catch (\Throwable $e) {
            return ['baru' => 0, 'diperbarui' => 0, 'total' => 0, 'galat' => $e->getMessage()];
        }

        $baru = 0;
        $diperbarui = 0;

        foreach ($daftar as $baris) {
            $email = Str::lower(trim((string) $baris->email));
            if ($email === '') {
                continue;
            }

            $ada = User::query()->whereRaw('LOWER(email) = ?', [$email])->exists();
            self::pastikanAkunLokal($baris);
            $ada ? $diperbarui++ : $baru++;
        }

        return ['baru' => $baru, 'diperbarui' => $diperbarui, 'total' => $daftar->count(), 'galat' => null];
    }
}
