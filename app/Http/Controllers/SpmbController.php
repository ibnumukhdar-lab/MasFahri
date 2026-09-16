<?php

namespace App\Http\Controllers;

use App\Models\Registration;
use App\Models\Setting;
use Illuminate\Http\Request;

class SpmbController extends Controller
{
    public function index()
    {
        return view('spmb', ['tahunAjaran' => Setting::ambil('tahun_ajaran', '2026/2027')]);
    }

    public function simpan(Request $request)
    {
        $data = $request->validate([
            'nama_wali' => ['required', 'string', 'max:120'],
            'nama_siswa' => ['required', 'string', 'max:120'],
            'jenis_kelamin' => ['nullable', 'in:Laki-laki,Perempuan'],
            'sekolah_asal' => ['nullable', 'string', 'max:150'],
            'minat_program' => ['nullable', 'string', 'max:60'],
            'tahun_ajaran' => ['required', 'string', 'max:20'],
            'whatsapp' => ['nullable', 'string', 'max:25'],
            'catatan' => ['nullable', 'string', 'max:800'],
        ]);

        $data['nomor'] = Registration::nomorBaru($data['tahun_ajaran']);
        $data['ip'] = $request->ip();
        $registrasi = Registration::create($data);

        // Notifikasi WhatsApp ke admin (tautan siap klik) — tercatat juga di log.
        $wa = Setting::ambil('wa_admin');
        $pesan = rawurlencode("Pendaftaran SPMB baru {$registrasi->nomor}\nNama siswa: {$registrasi->nama_siswa}\nWali: {$registrasi->nama_wali}\nSekolah asal: {$registrasi->sekolah_asal}\nProgram: {$registrasi->minat_program}");
        $tautan = $wa ? "https://wa.me/{$wa}?text={$pesan}" : null;

        return redirect()->route('spmb.status', ['nomor' => $registrasi->nomor])
            ->with('sukses', "Pendaftaran {$registrasi->nomor} tercatat. Simpan nomor ini untuk mengecek status.")
            ->with('tautan_wa', $tautan);
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
