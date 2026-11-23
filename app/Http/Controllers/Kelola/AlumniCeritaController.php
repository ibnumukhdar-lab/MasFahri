<?php

namespace App\Http\Controllers\Kelola;

use App\Http\Controllers\Controller;
use App\Models\AlumniCerita;
use Illuminate\Http\Request;

class AlumniCeritaController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'menunggu');

        return view('kelola-alumni', [
            'alumni' => AlumniCerita::when($status === 'menunggu',
                    fn ($q) => $q->where(fn ($w) => $w->where('tampil', false)->orWhereNull('diverifikasi_pada')))
                ->when($status === 'tampil', fn ($q) => $q->tampil())
                ->latest()->paginate(20)->withQueryString(),
            'status' => $status,
            'hitung' => [
                'menunggu' => AlumniCerita::where(fn ($w) => $w->where('tampil', false)->orWhereNull('diverifikasi_pada'))->count(),
                'tampil' => AlumniCerita::tampil()->count(),
                'semua' => AlumniCerita::count(),
            ],
        ]);
    }

    public function setujui(AlumniCerita $cerita)
    {
        $cerita->update(['tampil' => true, 'diverifikasi_pada' => now()]);

        return back()->with('sukses', 'Cerita ' . $cerita->nama . ' kini tampil publik.');
    }

    public function sembunyikan(AlumniCerita $cerita)
    {
        $cerita->update(['tampil' => false, 'diverifikasi_pada' => null]);

        return back()->with('sukses', 'Cerita ' . $cerita->nama . ' disembunyikan.');
    }

    public function hapus(AlumniCerita $cerita)
    {
        $foto = $cerita->foto;
        $cerita->delete();
        if ($foto) {
            @unlink('/home/u8151173/public_html/smaitarafah.sch.id/media/' . $foto);
        }

        return back()->with('sukses', 'Isian dihapus beserta fotonya.');
    }
}
