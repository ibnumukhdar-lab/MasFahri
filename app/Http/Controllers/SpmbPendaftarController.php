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

        if ($registrasi->status === 'tes') {
            $registrasi->pastikanKodeTes();
        }
        $registrasi->load('ujian');

        return view('spmb-dasbor', [
            'registrasi' => $registrasi,
            'biodata' => $registrasi->biodata()->firstOrCreate([]),
            'ujian' => $registrasi->ujian,
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

    // ---------- kartu tes & CBT ----------

    /** Kartu tes (halaman cetak) — kode tes jadi gerbang masuk CBT. */
    public function kartu(Request $request)
    {
        $registrasi = $this->pendaftarSesi($request);
        if (! $registrasi) {
            return redirect()->route('spmb.pendaftar.masuk');
        }
        if ($registrasi->status !== 'tes' && ! $registrasi->ujian()->exists()) {
            return redirect()->route('spmb.pendaftar.dasbor');
        }

        $kode = $registrasi->pastikanKodeTes();

        return view('spmb-kartu', [
            'registrasi' => $registrasi,
            'kode' => $kode,
            'paket' => \App\Models\SpmbPaketSoal::terbuka(),
        ]);
    }

    /** Pintu masuk CBT: hasil bila sudah selesai, atau form kode tes. */
    public function tes(Request $request)
    {
        $registrasi = $this->pendaftarSesi($request);
        if (! $registrasi) {
            return redirect()->route('spmb.pendaftar.masuk');
        }

        $ujian = $registrasi->ujian()->with('paket')->first();

        return view('spmb-tes', [
            'registrasi' => $registrasi,
            'ujian' => $ujian,
            'paket' => $ujian?->paket ?: \App\Models\SpmbPaketSoal::terbuka(),
        ]);
    }

    /** Cocokkan kode tes → buat/lanjutkan percobaan ujian. */
    public function tesMulai(Request $request)
    {
        $registrasi = $this->pendaftarSesi($request);
        if (! $registrasi) {
            return redirect()->route('spmb.pendaftar.masuk');
        }

        $data = $request->validate(
            ['kode' => ['required', 'string', 'max:12']],
            ['kode.required' => 'Kode tes wajib diisi — lihat kartu tes Anda.']
        );

        $paket = \App\Models\SpmbPaketSoal::terbuka();
        if (! $paket) {
            return back()->withErrors(['kode' => 'Tes belum dibuka oleh panitia. Coba lagi sesuai jadwal di kartu tes.']);
        }
        if ($paket->soal()->count() === 0) {
            return back()->withErrors(['kode' => 'Soal tes belum disiapkan panitia.']);
        }
        if ($registrasi->status !== 'tes' && ! $registrasi->ujian()->exists()) {
            return back()->withErrors(['kode' => 'Akun Anda belum masuk tahap tes. Hubungi panitia.']);
        }
        if (strtoupper(trim($data['kode'])) !== strtoupper((string) $registrasi->kode_tes)) {
            return back()->withErrors(['kode' => 'Kode tes tidak cocok. Periksa kembali kartu tes Anda.']);
        }

        $ujian = \App\Models\SpmbUjian::firstOrCreate(
            ['registration_id' => $registrasi->id, 'paket_id' => $paket->id],
            ['status' => 'sedang', 'mulai_pada' => now()]
        );

        if ($ujian->status === 'selesai') {
            return redirect()->route('spmb.pendaftar.tes');
        }

        if ($ujian->sisa_detik <= 0) {
            $this->akhiriDanNilai($ujian);

            return redirect()->route('spmb.pendaftar.tes')
                ->withErrors(['kode' => 'Waktu tes sudah habis.']);
        }

        return redirect()->route('spmb.pendaftar.kerjakan');
    }

    /** Halaman pengerjaan CBT (penuh layar). */
    public function kerjakan(Request $request)
    {
        $registrasi = $this->pendaftarSesi($request);
        if (! $registrasi) {
            return redirect()->route('spmb.pendaftar.masuk');
        }

        $ujian = $registrasi->ujian()->with('paket.soal')->first();
        if (! $ujian || $ujian->status !== 'sedang') {
            return redirect()->route('spmb.pendaftar.tes');
        }

        if ($ujian->sisa_detik <= 0) {
            $this->akhiriDanNilai($ujian);

            return redirect()->route('spmb.pendaftar.tes');
        }

        // Kunci jawaban TIDAK pernah dikirim ke browser.
        $soal = $ujian->paket->soal->values()->map(fn ($s) => [
            'id' => $s->id,
            'teks' => $s->teks,
            'pilihan' => $s->pilihan,
        ]);

        return view('spmb-cbt', [
            'registrasi' => $registrasi,
            'paket' => $ujian->paket,
            'ujian' => $ujian,
            'sisaDetik' => $ujian->sisa_detik,
            'soal' => $soal,
        ]);
    }

    /** Terima jawaban dari browser → nilai di server, simpan hasil. */
    public function simpanTes(Request $request)
    {
        $registrasi = $this->pendaftarSesi($request);
        if (! $registrasi) {
            return response()->json(['galat' => 'Sesi berakhir.'], 401);
        }

        $ujian = $registrasi->ujian()->with('paket.soal')->first();
        if (! $ujian) {
            return response()->json(['galat' => 'Ujian tidak ditemukan.'], 404);
        }
        if ($ujian->status === 'selesai') {
            return response()->json([
                'galat' => 'Ujian sudah selesai.',
                'skor' => $ujian->skor,
                'benar' => $ujian->benar,
                'total' => $ujian->total,
            ], 409);
        }

        $jawaban = $request->input('jawaban', []);
        if (! is_array($jawaban)) {
            $jawaban = [];
        }

        $ujian->pelanggaran = max((int) $ujian->pelanggaran, (int) $request->input('pelanggaran', 0));
        $this->akhiriDanNilai($ujian, $jawaban);

        return response()->json([
            'skor' => $ujian->skor,
            'benar' => $ujian->benar,
            'total' => $ujian->total,
            'pelanggaran' => $ujian->pelanggaran,
        ]);
    }

    /** Nilai di server: bandingkan kunci, tandai selesai, catat lewat waktu. */
    private function akhiriDanNilai(\App\Models\SpmbUjian $ujian, array $jawaban = []): void
    {
        $soal = $ujian->paket->soal;
        $benar = 0;

        foreach ($soal as $s) {
            $dipilih = strtoupper(trim((string) ($jawaban[$s->id] ?? '')));
            if ($dipilih !== '' && $dipilih === strtoupper((string) $s->kunci)) {
                $benar++;
            }
        }

        $total = $soal->count();
        $lewat = now()->gt($ujian->mulai_pada->copy()->addMinutes((int) $ujian->paket->durasi_menit)->addMinutes(2));

        $ujian->forceFill([
            'status' => 'selesai',
            'jawaban' => array_map(fn ($v) => (string) $v, $jawaban ?: (array) $ujian->jawaban),
            'benar' => $benar,
            'total' => $total,
            'skor' => $total > 0 ? (int) round($benar / $total * 100) : 0,
            'selesai_pada' => now(),
            'pelanggaran' => (int) $ujian->pelanggaran + ($lewat ? 1 : 0),
        ])->save();
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
