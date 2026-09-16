<?php

namespace App\Http\Controllers;

use App\Models\AlumniProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AlumniController extends Controller
{
    public function index(Request $request)
    {
        $alumni = AlumniProfile::with('user')
            ->whereNotNull('diverifikasi_pada')
            ->where('tampil_publik', true)
            ->when($request->filled('q'), function ($q) use ($request) {
                $cari = $request->q;
                $q->where(fn ($w) => $w->where('tahun_lulus', 'like', "%$cari%")
                    ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%$cari%")));
            })
            ->orderByDesc('tahun_lulus')->paginate(24)->withQueryString();

        return view('alumni', ['alumni' => $alumni]);
    }

    public function simpan(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'whatsapp' => ['nullable', 'string', 'max:25'],
            'tahun_lulus' => ['nullable', 'string', 'max:8'],
            'kelas_akhir' => ['nullable', 'string', 'max:40'],
            'lanjut_ke' => ['nullable', 'string', 'max:120'],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'peran' => 'alumni',
            'whatsapp' => $data['whatsapp'] ?? null,
        ]);

        AlumniProfile::create([
            'user_id' => $user->id,
            'tahun_lulus' => $data['tahun_lulus'] ?? null,
            'kelas_akhir' => $data['kelas_akhir'] ?? null,
            'lanjut_ke' => $data['lanjut_ke'] ?? null,
            'tampil_publik' => false,
        ]);

        Auth::login($user);

        return redirect()->route('profil')->with('sukses', 'Pendaftaran alumni berhasil. Akun menunggu verifikasi admin sebelum tampil di direktori.');
    }
}
