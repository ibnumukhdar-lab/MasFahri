<?php

namespace App\Http\Controllers;

use App\Models\Registration;
use App\Models\SpmbBiodata;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * Area pendaftar SPMB: login dengan nomor pendaftaran + sandi nomor WhatsApp.
 * Dashboard baru terbuka setelah panitia menandai status selain 'baru'
 * (aksi "Tandai dihubungi"/"Terima"/"Ubah status" di panel).
 */
class SpmbPendaftarController extends Controller
{
    /** Status yang dianggap "sudah diverifikasi panitia". */
    public const STATUS_TERVERIFIKASI = ['dihubungi', 'tes', 'diterima'];

    public function formLogin()
    {
        return view('spmb-masuk');
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'nomor' => ['required', 'string', 'max:40'],
            'whatsapp' => ['required', 'string', 'max:25'],
        ], [
            'nomor.required' => 'Nomor pendaftaran wajib diisi.',
            'whatsapp.required' => 'Nomor WhatsApp wajib diisi.',
        ]);

        $nomor = strtoupper(trim($data['nomor']));
        $wa = preg_replace('/\D+/', '', $data['whatsapp']);

        $registrasi = Registration::where('nomor', $nomor)->first();

        if (! $registrasi || $this->sandiTidakCocok($registrasi, $wa)) {
            return back()->withErrors(['nomor' => 'Nomor pendaftaran atau nomor WhatsApp tidak cocok.'])->onlyInput('nomor');
        }

        // Simpan sesi pendaftar (bukan akun User — id saja).
        $request->session()->put('spmb_pendaftar_id', $registrasi->id);
        $request->session()->regenerate();

        if (! $this->terverifikasi($registrasi)) {
            return redirect()->route('spmb.pendaftar.kena')->with('nomor', $registrasi->nomor);
        }

        return redirect()->route('spmb.pendaftar.dasbor');
    }

    public function logout(Request $request)
    {
        $request->session()->forget('spmb_pendaftar_id');
        $request->session()->regenerateToken();

        return redirect()->route('spmb.pendaftar.masuk');
    }

    /** Halaman keterangan "belum terverifikasi". */
    public function belumVerifikasi(Request $request)
    {
        $registrasi = $this->pendaftarSesi($request);
        if (! $registrasi) {
            return redirect()->route('spmb.pendaftar.masuk');
        }

        return view('spmb-kena', ['registrasi' => $registrasi]);
    }

    public function dasbor(Request $request)
    {
        $registrasi = $this->pendaftarSesi($request);
        if (! $registrasi) {
            return redirect()->route('spmb.pendaftar.masuk');
        }
        if (! $this->terverifikasi($registrasi)) {
            return redirect()->route('spmb.pendaftar.kena');
        }

        return view('spmb-dasbor', [
            'registrasi' => $registrasi,
            'biodata' => $registrasi->biodata()->firstOrCreate([]),
        ]);
    }

    public function simpanBiodata(Request $request)
    {
        $registrasi = $this->pendaftarSesi($request);
        if (! $registrasi) {
            return redirect()->route('spmb.pendaftar.masuk');
        }
        if (! $this->terverifikasi($registrasi)) {
            return redirect()->route('spmb.pendaftar.kena');
        }

        $data = $request->validate([
            'nik' => ['nullable', 'digits:16'],
            'nisn' => ['nullable', 'digits_between:8,12'],
            'tempat_lahir' => ['nullable', 'string', 'max:100'],
            'tanggal_lahir' => ['nullable', 'date'],
            'alamat' => ['nullable', 'string', 'max:600'],
            'anak_ke' => ['nullable', 'integer', 'min:1', 'max:20'],
            'jumlah_saudara' => ['nullable', 'integer', 'min:0', 'max:30'],
            'nama_ayah' => ['nullable', 'string', 'max:120'],
            'pekerjaan_ayah' => ['nullable', 'string', 'max:120'],
            'nama_ibu' => ['nullable', 'string', 'max:120'],
            'pekerjaan_ibu' => ['nullable', 'string', 'max:120'],
            'telepon_rumah' => ['nullable', 'string', 'max:25'],
            'hobi' => ['nullable', 'string', 'max:150'],
            'foto' => ['nullable', 'image', 'max:4096'],
        ], [
            'nik.digits' => 'NIK harus 16 angka.',
            'nisn.digits_between' => 'NISN 8-12 angka.',
            'tanggal_lahir.date' => 'Tanggal lahir tidak valid.',
            'foto.image' => 'Foto harus berupa gambar (JPG/PNG).',
            'foto.max' => 'Ukuran foto maksimal 4 MB.',
        ]);

        if ($request->hasFile('foto')) {
            $berkas = $request->file('foto');
            $nama = 'spmb-biodata/' . $registrasi->nomor . '-' . \Illuminate\Support\Str::random(6) . '.'
                . strtolower($berkas->getClientOriginalExtension() ?: 'jpg');
            Storage::disk('media')->put($nama, file_get_contents($berkas->getRealPath()));
            $data['foto'] = $nama;
        } else {
            unset($data['foto']);
        }

        $registrasi->biodata()->updateOrCreate([], $data);

        return back()->with('sukses', 'Biodata tersimpan. Terima kasih!');
    }

    // ---------- pembantu ----------

    private function pendaftarSesi(Request $request): ?Registration
    {
        $id = $request->session()->get('spmb_pendaftar_id');

        return $id ? Registration::find($id) : null;
    }

    private function terverifikasi(Registration $r): bool
    {
        return in_array($r->status, self::STATUS_TERVERIFIKASI, true);
    }

    /** Sandi = nomor WA yang didaftarkan; cocok dengan atau tanpa awalan 62/0. */
    private function sandiTidakCocok(Registration $r, string $wa): bool
    {
        $tersimpan = preg_replace('/\D+/', '', (string) $r->whatsapp);

        // 08xx vs 628xx dianggap sama.
        $normal = fn (string $n): string => str_starts_with($n, '62') ? '0' . substr($n, 2) : $n;

        return $wa === '' || $normal($wa) !== $normal($tersimpan);
    }
}
