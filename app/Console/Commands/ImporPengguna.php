<?php

namespace App\Console\Commands;

use App\Models\AlumniProfile;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Impor akun pengguna dari ekspor WordPress (sandi TIDAK bisa dipindah — hash WP beda).
 * Setiap akun dibuat dengan sandi acak; daftar sandi dicetak & disimpan ke
 * storage/app/sandi-sementara.txt supaya bisa dibagikan sekali lalu direset user.
 *
 *   php artisan smaita:pengguna "D:/smaita-web/smaita-export.json"
 */
class ImporPengguna extends Command
{
    protected $signature = 'smaita:pengguna {berkas : path JSON ekspor} {--admin-email= : email yang dijadikan admin} {--admin-sandi= : sandi admin (kosong = acak)}';

    protected $description = 'Impor akun guru/musyrif/ustadz/orang tua/alumni dari WordPress + siapkan akun admin';

    private array $petaPeran = [
        'administrator' => 'admin',
        'um_guru' => 'guru',
        'um_staf' => 'guru',
        'um_staff' => 'guru',
        'um_musyrif-musyrifah' => 'musyrif',
        'um_ustadz-diniyah' => 'ustadz_diniyah',
        'orang_tua' => 'orang_tua',
        'um_alumni' => 'alumni',
        'subscriber' => 'orang_tua',
    ];

    public function handle(): int
    {
        $data = json_decode(File::get($this->argument('berkas')), true, 512, JSON_THROW_ON_ERROR);

        $emailAdmin = $this->option('admin-email') ?: 'admin@smaitarafah.sch.id';
        $sandiAdmin = $this->option('admin-sandi') ?: Str::password(12);
        $catatan = [];

        // ---------- akun admin ----------
        $admin = User::updateOrCreate(['email' => $emailAdmin], [
            'name' => 'Administrator Situs',
            'password' => Hash::make($sandiAdmin),
            'peran' => 'admin',
        ]);
        $catatan[] = sprintf('%-34s %-10s %s', $emailAdmin, 'admin', $sandiAdmin);

        // ---------- pengguna dari WordPress ----------
        $n = 0;
        foreach ($data['pengguna'] ?? [] as $u) {
            if (in_array($u['login'], ['admin'], true)) continue;
            $email = $u['email'] ?: $u['login'] . '@smaitarafah.sch.id';
            if (User::where('email', $email)->exists()) continue;

            $peran = 'alumni';
            foreach (explode(',', $u['peran']) as $p) {
                if (isset($this->petaPeran[trim($p)])) { $peran = $this->petaPeran[trim($p)]; break; }
            }

            $sandi = Str::password(10);
            $user = User::create([
                'name' => $u['nama'] ?: $u['login'],
                'email' => $email,
                'password' => Hash::make($sandi),
                'peran' => $peran,
                'aktif' => true,
            ]);

            if ($peran === 'alumni') {
                AlumniProfile::create([
                    'user_id' => $user->id,
                    'tahun_lulus' => null,
                    'tampil_publik' => false,
                ]);
            }

            $catatan[] = sprintf('%-34s %-10s %s', $email, $peran, $sandi);
            $n++;
        }

        $path = storage_path('app/sandi-sementara.txt');
        File::put($path, "Sandi sementara (mohon minta pengguna segera menggantinya):\n\n" . implode("\n", $catatan) . "\n");

        $this->info("Akun dibuat/diperbarui: " . ($n + 1));
        $this->line("Daftar sandi: $path");
        $this->newLine();
        foreach ($catatan as $c) $this->line('  ' . $c);
        $this->newLine();
        $this->warn('Sandi hanya ditampilkan sekali di sini — bagikan lewat jalur aman, jangan di chat.');
        return self::SUCCESS;
    }
}
