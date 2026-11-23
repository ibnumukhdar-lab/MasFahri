<?php

namespace App\Http\Controllers;

use App\Models\AlumniCerita;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Formulir cerita alumni (tanpa akun) + daftar publik + verifikasi admin.
 */
class AlumniCeritaController extends Controller
{
    public function index(Request $request)
    {
        $alumni = AlumniCerita::tampil()
            ->when($request->filled('angkatan'), fn ($q) => $q->where('tahun_lulus', $request->angkatan))
            ->orderByDesc('tahun_lulus')->orderBy('nama')->paginate(24)->withQueryString();

        return view('alumni-cerita', [
            'alumni' => $alumni,
            'angkatan' => AlumniCerita::tampil()->whereNotNull('tahun_lulus')->distinct()->orderByDesc('tahun_lulus')->pluck('tahun_lulus'),
        ]);
    }

    public function form()
    {
        return view('alumni-cerita-form');
    }

    public function simpan(Request $request)
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'min:3', 'max:120'],
            'wa' => ['required', 'string', 'min:9', 'max:25'],
            'tahun_lulus' => ['nullable', 'string', 'max:8'],
            'kelas_akhir' => ['nullable', 'string', 'max:40'],
            'status_saat_ini' => ['nullable', 'in:kuliah,bekerja,wirausaha,tahfizh,lainnya'],
            'status_keterangan' => ['nullable', 'string', 'max:160'],
            'prestasi' => ['nullable', 'string', 'max:600'],
            'pesan' => ['required', 'string', 'min:20', 'max:1500'],
            'instagram' => ['nullable', 'string', 'max:80'],
            'izin_tampil' => ['accepted'],
            'foto' => ['required', 'image', 'max:4096'],
        ], [
            'nama.required' => 'Nama wajib diisi.',
            'wa.required' => 'Nomor WhatsApp wajib diisi.',
            'pesan.required' => 'Pesan untuk adik kelas wajib diisi.',
            'pesan.min' => 'Ceritamu terlalu pendek — minimal 20 huruf, jangan malu-malu.',
            'pesan.max' => 'Pesan maksimal 1500 huruf.',
            'izin_tampil.accepted' => 'Centang dulu persetujuan tampil di situs.',
            'foto.required' => 'Foto diri wajib diunggah.',
            'foto.image' => 'Berkas harus berupa gambar (JPG/PNG).',
            'foto.max' => 'Ukuran foto maksimal 4 MB.',
        ]);

        $berkas = $request->file('foto');
        $nama = 'alumni-' . Str::random(6) . '.' . $berkas->getClientOriginalExtension();
        $berkas->move('/home/u8151173/public_html/smaitarafah.sch.id/media/unggahan', $nama);

        $token = Str::random(32);
        AlumniCerita::create([
            'nama' => $data['nama'],
            'wa' => preg_replace('/[^0-9+]/', '', $data['wa']),
            'tahun_lulus' => $data['tahun_lulus'] ?? null,
            'kelas_akhir' => $data['kelas_akhir'] ?? null,
            'status_saat_ini' => $data['status_saat_ini'] ?? null,
            'status_keterangan' => $data['status_keterangan'] ?? null,
            'prestasi' => $data['prestasi'] ?? null,
            'pesan' => $data['pesan'],
            'instagram' => $data['instagram'] ?? null,
            'foto' => 'unggahan/' . $nama,
            'token' => $token,
        ]);

        // notifikasi WA ke admin (setting wa_admin)
        $nomor = \Illuminate\Support\Facades\DB::table('settings')->where('kunci', 'wa_admin')->value('nilai');
        $nomor = preg_replace('/[^0-9]/', '', (string) $nomor) ?: '6285356002524';
        $teks = "Isian alumni baru: {$data['nama']}" . ($data['tahun_lulus'] ? " ({$data['tahun_lulus']})" : '')
            . "\nCek di /kelola → Alumni → Cerita alumni untuk memverifikasi.";
        if ($nomor && function_exists('curl_init')) {
            $ch = curl_init("https://wa.me/{$nomor}?text=" . rawurlencode($teks));
            curl_setopt_array($ch, [CURLOPT_NOBODY => true, CURLOPT_TIMEOUT => 5]);
            curl_exec($ch);
            curl_close($ch);
        }

        // halaman sukses berisi tautan sunting (disimpan dengan token)
        return redirect()->route('alumni.cerita.sukses', $token);
    }

    public function sukses(string $token)
    {
        $cerita = AlumniCerita::where('token', $token)->firstOrFail();

        return view('alumni-cerita-sukses', ['cerita' => $cerita]);
    }

    /** Form sunting ulang lewat tautan token (kirim ulang foto boleh, data lain diubah sendiri). */
    public function sunting(string $token)
    {
        $cerita = AlumniCerita::where('token', $token)->firstOrFail();

        return view('alumni-cerita-form', ['cerita' => $cerita, 'token' => $token]);
    }

    public function perbarui(Request $request, string $token)
    {
        $cerita = AlumniCerita::where('token', $token)->firstOrFail();
        $data = $request->validate([
            'nama' => ['required', 'string', 'min:3', 'max:120'],
            'wa' => ['required', 'string', 'min:9', 'max:25'],
            'tahun_lulus' => ['nullable', 'string', 'max:8'],
            'kelas_akhir' => ['nullable', 'string', 'max:40'],
            'status_saat_ini' => ['nullable', 'in:kuliah,bekerja,wirausaha,tahfizh,lainnya'],
            'status_keterangan' => ['nullable', 'string', 'max:160'],
            'prestasi' => ['nullable', 'string', 'max:600'],
            'pesan' => ['required', 'string', 'min:20', 'max:1500'],
            'instagram' => ['nullable', 'string', 'max:80'],
            'izin_tampil' => ['accepted'],
            'foto' => ['nullable', 'image', 'max:4096'],
        ]);

        $isi = [
            'nama' => $data['nama'],
            'wa' => preg_replace('/[^0-9+]/', '', $data['wa']),
            'tahun_lulus' => $data['tahun_lulus'] ?? null,
            'kelas_akhir' => $data['kelas_akhir'] ?? null,
            'status_saat_ini' => $data['status_saat_ini'] ?? null,
            'status_keterangan' => $data['status_keterangan'] ?? null,
            'prestasi' => $data['prestasi'] ?? null,
            'pesan' => $data['pesan'],
            'instagram' => $data['instagram'] ?? null,
            // suntingan menunggu verifikasi ulang supaya admin tahu ada perubahan
            'tampil' => false,
            'diverifikasi_pada' => null,
        ];

        if ($request->hasFile('foto')) {
            $berkas = $request->file('foto');
            $nama = 'alumni-' . Str::random(6) . '.' . $berkas->getClientOriginalExtension();
            $berkas->move('/home/u8151173/public_html/smaitarafah.sch.id/media/unggahan', $nama);
            $isi['foto'] = 'unggahan/' . $nama;
        }

        $cerita->update($isi);

        return redirect()->route('alumni.cerita.sukses', $token)->with('sukses', 'Perubahan disimpan. Menunggu verifikasi ulang admin.');
    }
}
