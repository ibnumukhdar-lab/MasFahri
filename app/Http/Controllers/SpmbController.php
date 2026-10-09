<?php

namespace App\Http\Controllers;

use App\Models\Registration;
use App\Models\Setting;
use Illuminate\Http\Request;

class SpmbController extends Controller
{
    /** Pilihan tahun ajaran pada formulir SPMB. */
    public const TAHUN_AJARAN = ['2027/2028', '2028/2029', '2029/2030'];

    /**
     * Nomor WhatsApp sekolah yang menerima pesan konfirmasi pendaftar.
     * Dipakai bila setting `wa_admin` kosong; setting tetap jadi prioritas.
     */
    public const WA_KONFIRMASI = '6285356002524';

    public function index()
    {
        $tahun = Setting::ambil('tahun_ajaran', '2027/2028');
        $pilihan = self::TAHUN_AJARAN;

        if (! in_array($tahun, $pilihan, true)) {
            $tahun = $pilihan[0];
        }

        return view('spmb', [
            'tahunAjaran' => $tahun,
            'pilihanTahun' => $pilihan,
        ]);
    }

    public function simpan(Request $request)
    {
        $data = $request->validate([
            'nama_wali' => ['required', 'string', 'max:120'],
            'nama_siswa' => ['required', 'string', 'max:120'],
            'jenis_kelamin' => ['nullable', 'in:Laki-laki,Perempuan'],
            'sekolah_asal' => ['nullable', 'string', 'max:150'],
            'minat_program' => ['nullable', 'string', 'max:60'],
            'tahun_ajaran' => ['required', 'string', 'max:20', \Illuminate\Validation\Rule::in(self::TAHUN_AJARAN)],
            'whatsapp' => ['nullable', 'string', 'max:25'],
            'catatan' => ['nullable', 'string', 'max:800'],
        ]);

        $data['nomor'] = Registration::nomorBaru($data['tahun_ajaran']);
        $data['ip'] = $request->ip();
        $registrasi = Registration::create($data);

        // Pesan konfirmasi dari PENDAFTAR ke WhatsApp sekolah: setelah formulir terkirim,
        // pendaftar diarahkan otomatis mengirim pesan berisi data pendaftarannya sendiri.
        $nomorWa = preg_replace('/\D+/', '', (string) (Setting::ambil('wa_admin') ?: self::WA_KONFIRMASI));
        $pesan = self::pesanKonfirmasi($registrasi);
        $tautan = $nomorWa !== '' ? 'https://wa.me/'.$nomorWa.'?text='.rawurlencode($pesan) : null;

        return redirect()->route('spmb.status', ['nomor' => $registrasi->nomor])
            ->with('sukses', "Pendaftaran {$registrasi->nomor} tercatat. Simpan nomor ini untuk mengecek status.")
            ->with('tautan_wa', $tautan)
            ->with('wa_nomor', $nomorWa)
            ->with('pesan_wa', $pesan);
    }

    /** Isi pesan konfirmasi data pendaftaran (dikirim pendaftar ke WhatsApp sekolah). */
    public static function pesanKonfirmasi(Registration $r): string
    {
        $baris = ["Nomor Pendaftaran: {$r->nomor}", "Nama Siswa: {$r->nama_siswa}"];

        foreach ([
            'Jenis Kelamin' => $r->jenis_kelamin,
            'Nama Wali' => $r->nama_wali,
            'Sekolah Asal' => $r->sekolah_asal,
            'Jalur Pendaftaran' => $r->minat_program,
            'Tahun Ajaran' => $r->tahun_ajaran,
            'No. WhatsApp Wali' => $r->whatsapp,
            'Catatan' => $r->catatan,
        ] as $label => $nilai) {
            if (filled($nilai)) {
                $baris[] = "{$label}: {$nilai}";
            }
        }

        return "Assalamu'alaikum, Admin SPMB SMA IT Arafah.\n\n"
            .'Saya sudah mengisi informasi pendaftaran (formulir SPMB) dengan data berikut:'
            ."\n\n".implode("\n", $baris)
            ."\n\nMohon konfirmasi pendaftaran ini beserta informasi tahap selanjutnya. Terima kasih.";
    }

    public function status(Request $request)
    {
        $nomor = trim((string) $request->query('nomor'));

        return view('spmb-status', [
            'nomor' => $nomor,
            'registrasi' => $nomor ? Registration::where('nomor', $nomor)->first() : null,
        ]);
    }
}
