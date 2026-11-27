<?php

namespace App\Http\Controllers;

use App\Models\AlumniCerita;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Formulir cerita alumni (tanpa akun) + daftar publik + halaman cerita + verifikasi admin.
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

    public function show(string $slug)
    {
        $cerita = AlumniCerita::tampil()->where('slug', $slug)->firstOrFail();
        $cerita->increment('dilihat');

        return view('alumni-cerita-show', [
            'cerita' => $cerita,
            'lainnya' => AlumniCerita::tampil()->whereKeyNot($cerita->id)->orderBy('nama')->take(6)->get(),
        ]);
    }

/**
 * Konversi gambar transparan (PNG/WebP) ke JPEG berlatar putih — foto "hitam" dari HP alumni
 * disebabkan transparansi JPEG menjadi hitam. Gambar 100% transparan ditolak (tidak ada isinya).
 */
private function konversiFoto(string $path): string
{
    // Aturan pemilik (26 Nov): sesederhana mungkin — crop 1:1 dari tengah,
    // transparan jadi putih, simpan JPEG. Tanpa deteksi isi, tanpa penolakan.
    $info = @getimagesize($path);
    if (! $info) {
        @unlink($path);

        throw ValidationException::withMessages(['foto' => 'Berkas bukan gambar yang dikenali. Gunakan foto JPG.']);
    }

    $gambar = @imagecreatefromstring(file_get_contents($path));
    if (! $gambar) {
        @unlink($path);

        throw ValidationException::withMessages(['foto' => 'Foto tidak bisa dibuka. Coba ambil ulang fotonya.']);
    }

    $w = imagesx($gambar);
    $h = imagesy($gambar);

    // --- CROP TENGAH 1:1 ---
    $sisi = min($w, $h);
    $cx = (int) floor(($w - $sisi) / 2);
    $cy = (int) floor(($h - $sisi) / 2);

    // batas 1200px supaya berkas ringan di hosting
    $sisi = min($sisi, 1200);

    $hasil = imagecreatetruecolor($sisi, $sisi);
    $putih = imagecolorallocate($hasil, 255, 255, 255);
    imagefill($hasil, 0, 0, $putih);          // transparan → putih
    imagecopyresampled($hasil, $gambar, 0, 0, $cx, $cy, $sisi, $sisi, $sisi, $sisi);
    imagedestroy($gambar);

    imagejpeg($hasil, $path, 88);             // tulis ulang sebagai JPEG di path yang sama
    imagedestroy($hasil);

    return 'jpg';
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
            'foto' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
        ], [
            'nama.required' => 'Nama wajib diisi.',
            'wa.required' => 'Nomor WhatsApp wajib diisi.',
            'pesan.required' => 'Pesan untuk adik kelas wajib diisi.',
            'pesan.min' => 'Ceritamu terlalu pendek — minimal 20 huruf, jangan malu-malu.',
            'pesan.max' => 'Pesan maksimal 1500 huruf.',
            'izin_tampil.accepted' => 'Centang dulu persetujuan tampil di situs.',
            'foto.required' => 'Foto diri wajib diunggah.',
            'foto.image' => 'Berkas harus berupa gambar (JPG/PNG).',
            'foto.max' => 'Ukuran foto maksimal 10 MB.',
        ]);

        $berkas = $request->file('foto');
        $ekstensi = strtolower($berkas->getClientOriginalExtension() ?: 'jpg');
        $nama = 'alumni-' . Str::random(6) . '.' . $ekstensi;
        $berkas->move('/home/u8151173/public_html/smaitarafah.sch.id/media/unggahan', $nama);
        $pathTersimpan = '/home/u8151173/public_html/smaitarafah.sch.id/media/unggahan/' . $nama;
        $ekstensiBaru = $this->konversiFoto($pathTersimpan);
        if ($ekstensiBaru !== $ekstensi) {
            // PNG/WebP → JPEG: ganti nama berkas mengikuti ekstensi baru
            $namaBaru = preg_replace('/\.' . preg_quote($ekstensi, '/') . '$/', '.' . $ekstensiBaru, $nama);
            rename($pathTersimpan, '/home/u8151173/public_html/smaitarafah.sch.id/media/unggahan/' . $namaBaru);
            $nama = $namaBaru;
        }

        $token = Str::random(32);
        AlumniCerita::create([
            'nama' => $data['nama'],
            'slug' => AlumniCerita::slugUnik($data['nama']),
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
        $nomor = DB::table('settings')->where('kunci', 'wa_admin')->value('nilai');
        $nomor = preg_replace('/[^0-9]/', '', (string) $nomor) ?: '6285356002524';

        $teks = "Isian alumni baru: {$data['nama']}" . (($data['tahun_lulus'] ?? null) ? " ({$data['tahun_lulus']})" : '')
            . "\nCek di /kelola → SPMB & Alumni → Cerita Alumni untuk memverifikasi.";
        if (function_exists('curl_init')) {
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
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
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
            $ekstensi = strtolower($berkas->getClientOriginalExtension() ?: 'jpg');
            $nama = 'alumni-' . Str::random(6) . '.' . $ekstensi;
            $berkas->move('/home/u8151173/public_html/smaitarafah.sch.id/media/unggahan', $nama);
            $pathTersimpan = '/home/u8151173/public_html/smaitarafah.sch.id/media/unggahan/' . $nama;
            $ekstensiBaru = $this->konversiFoto($pathTersimpan);
            if ($ekstensiBaru !== $ekstensi) {
                $namaBaru = preg_replace('/\.' . preg_quote($ekstensi, '/') . '$/', '.' . $ekstensiBaru, $nama);
                rename($pathTersimpan, '/home/u8151173/public_html/smaitarafah.sch.id/media/unggahan/' . $namaBaru);
                $nama = $namaBaru;
            }
            $isi['foto'] = 'unggahan/' . $nama;
        }

        $cerita->update($isi);

        return redirect()->route('alumni.cerita.sukses', $token)->with('sukses', 'Perubahan disimpan. Menunggu verifikasi ulang admin.');
    }
}
