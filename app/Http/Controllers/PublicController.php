<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\BerandaBagian;
use App\Models\GalleryAlbum;
use App\Models\Page;
use App\Models\Post;
use App\Models\Setting;
use App\Models\TeacherLink;
use Illuminate\Http\Request;

class PublicController extends Controller
{
    public function beranda()
    {
        $berita = Post::terbit()->with('categories')->latest('terbit_pada')->take(6)->get();
        $album = GalleryAlbum::with('photos')->where('terbit', true)->latest('tanggal')->first();

        $galeri = $album ? $album->photos->take(6)->map(fn ($f) => $f->url) : [];
        $gambarHero = $album && $album->photos->first() ? $album->photos->first()->url : null;

        // Jalan mundur bila perlu: tambahkan ?lama pada alamat beranda.
        if (request()->query('lama')) {
            return view('beranda-lama', [
                'berita' => $berita,
                'galeri' => $galeri,
                'gambarHero' => $gambarHero,
                'tentang' => config('smaita.tentang'),
                'visi' => config('smaita.visi'),
                'misi' => config('smaita.misi'),
                'kenapa' => config('smaita.kenapa'),
                'program' => config('smaita.program'),
                'kurikulum' => config('smaita.kurikulum'),
                'kurikulumLead' => config('smaita.kurikulum_lead'),
            ]);
        }

        // ---------- Beranda gaya LANDING PAGE ----------
        // Foto diambil dari album galeri yang kategorinya dipilih di panel; tiap kategori = satu seksi.
        $peta = GalleryAlbum::with('photos')
            ->where('terbit', true)
            ->whereNotNull('kategori')
            ->orderByDesc('tanggal')
            ->get()
            ->groupBy(fn ($a) => (string) $a->kategori)
            ->map(fn ($grup) => $grup->flatMap->photos->values());

        // ?contoh=1 → seksi yang fotonya belum ada diisi foto yang sudah diunggah, supaya tampilan bisa ditinjau.
        $contoh = request()->boolean('contoh');
        $demo = $contoh ? GalleryAlbum::with('photos')->where('terbit', true)->latest('tanggal')->first() : null;
        $demoFoto = $demo ? $demo->photos : collect();

        $seksi = [
            [
                'id' => 'prestasi', 'kicker' => 'Prestasi', 'judul' => 'Prestasi Siswa',
                'lead' => 'Siswa kami bertumbuh lewat kompetisi dan kegiatan — dari musabaqah, olimpiade bahasa Arab, sampai kegiatan kepemudaan masjid.',
                'chips' => ['Juara Favorit The Raising Dai 2025', 'Olimpiade Bahasa Arab (OBA) ke-8', 'Penyerahan piagam apresiasi', 'Kemah Brigade Remaja Masjid Kotim'],
                'kategori' => 'prestasi', 'kabut' => false,
            ],
            [
                'id' => 'pembelajaran', 'kicker' => 'Kegiatan Pembelajaran', 'judul' => 'Belajar Aktif Setiap Hari',
                'lead' => 'Bukan sekadar mencatat: diskusi, proyek, presentasi, dan kunjungan pembelajaran agar siswa paham, bukan sekadar hafal.',
                'chips' => ['Diskusi & presentasi kelas', 'Kunjungan pembelajaran', 'Proyek siswa', 'Bimbingan guru mata pelajaran'],
                'kategori' => 'pembelajaran', 'kabut' => true,
            ],
            [
                'id' => 'fasilitas-sekolah', 'kicker' => 'Fasilitas Sekolah', 'judul' => 'Sekolah yang Nyaman untuk Belajar',
                'lead' => 'Ruang kelas, perpustakaan, laboratorium, dan area bermain yang menunjang kegiatan belajar siswa.',
                'chips' => ['Ruang kelas', 'Perpustakaan', 'Laboratorium', 'Lapangan & area bermain'],
                'kategori' => 'fasilitas-sekolah', 'kabut' => false,
            ],
            [
                'id' => 'fasilitas-asrama', 'kicker' => 'Fasilitas Asrama', 'judul' => 'Asrama dengan Pembinaan 24 Jam',
                'lead' => 'Boarding school dengan pembinaan ibadah, kebersihan, dan kemandirian — didampingi musyrif setiap hari.',
                'chips' => ['24 kamar putra & putri', 'Musyrif pendamping', 'Inspeksi kebersihan harian', 'Dapur & makan bersama'],
                'kategori' => 'fasilitas-asrama', 'kabut' => true,
            ],
            [
                'id' => 'student-root', 'kicker' => 'Program Student Root', 'judul' => 'Pembinaan Karakter Berbasis Data',
                'lead' => 'Program unggulan sekolah: setiap siswa dibina dalam kelompok kecil dengan catatan perkembangan <b>intelektual, emosional, sosial, dan spiritual</b> — dipantau harian, dilaporkan ke wali.',
                'chips' => ['🧠 Intelektual', '❤️ Emosional', '🤝 Sosial', '🕋 Spiritual', '12 kelompok binaan', '130 siswa aktif'],
                'kategori' => 'student-root', 'kabut' => false,
            ],
            [
                'id' => 'diniyah', 'kicker' => 'Program Diniyah', 'judul' => 'Kajian Kitab Kuning',
                'lead' => 'Kurikulum diniyah khas pesantren, dibimbing guru lulusan pesantren: nahwu, shorf, fiqih, aqidah, akhlaq, dan siroh.',
                'chips' => ['Nahwu', 'Shorf', 'Fiqih', 'Aqidah', 'Akhlaq', 'Siroh Nabawiyah', 'Qiroat Kutub'],
                'kategori' => 'diniyah', 'kabut' => true,
            ],
        ];

        // Alumni digabung ke daftar seksi, dengan chip nama lulusan yang ditampilkan di panel.
        $alumni = \App\Models\Graduate::where('tampil', true)->orderByDesc('tahun_ajaran')->take(8)->get();
        $chipAlumni = $alumni->map(fn ($a) => trim($a->nama.' — Angkatan '.$a->tahun_ajaran))->all();
        if (empty($chipAlumni)) {
            $chipAlumni = ['Data alumni belum ditambahkan — bisa diisi di panel → Alumni'];
        }
        $seksi[] = [
            'id' => 'alumni', 'kicker' => 'Alumni', 'judul' => 'Jejak Lulusan Kami',
            'lead' => 'Lulusan SMA IT Arafah melanjutkan ke kampus, pesantren, dan dunia kerja — sebagian kembali membangun daerah.',
            'chips' => $chipAlumni,
            'kategori' => 'alumni', 'kabut' => false,
        ];

        foreach ($seksi as $i => $sk) {
            $foto = $peta[$sk['kategori']] ?? collect();
            $catatan = null;
            if ($foto->isEmpty() && $contoh && $demoFoto->isNotEmpty()) {
                $foto = $demoFoto;
                $catatan = 'Contoh tampilan — foto untuk kategori ini belum diunggah.';
            }
            $seksi[$i]['foto'] = $foto->take(12);
            if ($catatan) {
                $seksi[$i]['catatan'] = $catatan;
            }
        }

        // Video profil dari pengaturan (tempel link YouTube di panel/pengaturan).
        $videoUrl = (string) Setting::ambil('video_profil', '');
        $videoId = preg_match('~(?:youtu\.be/|[?&]v=|/embed/|/shorts/)([A-Za-z0-9_-]{6,})~', $videoUrl, $m) ? $m[1] : null;
        $jumlahSiswa = (string) Setting::ambil('jumlah_siswa', '130');

        return view('beranda-landing', [
            'seksi' => $seksi,
            'heroFoto' => Setting::ambil('landing_hero', '') ?: $gambarHero,
            'videoId' => $videoId,
            'jumlahSiswa' => $jumlahSiswa,
            'tahunAjaran' => Setting::ambil('tahun_ajaran', '2026/2027'),
            'waAdmin' => Setting::ambil('wa_admin'),
        ]);
    }

    /** Halaman statis hasil impor WordPress (tentang, visi, kurikulum, tahfizh, diniyah, ...). */
    public function halaman(string $slug)
    {
        $page = Page::terbit()->where('slug', $slug)->firstOrFail();

        return view('halaman', ['page' => $page]);
    }

    /** Ekskul: halaman khusus (di WordPress dulu tautannya 404). */
    public function ekskul()
    {
        $page = Page::terbit()->where('slug', 'ekskul')->first();

        return view('halaman-ekskul', ['page' => $page, 'ekskul' => config('smaita.ekskul')]);
    }

    public function berita(Request $request)
    {
        $posts = Post::terbit()->with('categories')
            ->when($request->filled('kategori'), fn ($q) => $q->whereHas('categories', fn ($c) => $c->where('slug', $request->kategori)))
            ->when($request->filled('q'), fn ($q) => $q->where(fn ($w) => $w->where('judul', 'like', '%' . $request->q . '%')->orWhere('body', 'like', '%' . $request->q . '%')))
            ->latest('terbit_pada')->paginate(9)->withQueryString();

        return view('berita.index', [
            'posts' => $posts,
            'kategori' => Category::withCount('posts')->orderByDesc('posts_count')->get(),
            'total' => Post::terbit()->count(),
        ]);
    }

    public function beritaShow(Post $post)
    {
        abort_unless($post->status === 'terbit', 404);
        $post->increment('dilihat');

        return view('berita.show', ['post' => $post, 'lainnya' => Post::terbit()->whereKeyNot($post->id)->latest('terbit_pada')->take(3)->get()]);
    }

    public function galeri()
    {
        return view('galeri', ['albums' => GalleryAlbum::with('photos')->where('terbit', true)->orderBy('urut')->latest('tanggal')->get()]);
    }

    public function toolsguru()
    {
        return view('toolsguru', ['tautan' => TeacherLink::aktif()->get()->groupBy('kelompok')]);
    }

    public function robots()
    {
        // Halaman publik boleh diindeks; panel admin dan proses pendaftaran tidak.
        $baris = [
            'User-agent: *',
            'Allow: /',
            'Disallow: /kelola',
            'Disallow: /livewire',
            'Disallow: /spmb/status',
            '',
            'Sitemap: ' . url('/sitemap.xml'),
            '',
        ];

        return response(implode("\n", $baris), 200, ['Content-Type' => 'text/plain; charset=utf-8']);
    }

    public function sitemap()
    {
        // Satu alamat = satu baris. Kunci array memakai alamat itu sendiri,
        // jadi halaman yang juga ada di daftar tetap tidak tercatat dua kali.
        $urls = [];

        $tambah = function (string $lokasi, ?string $ubah, string $prioritas) use (&$urls) {
            if ($lokasi === '') {
                return;
            }
            if (isset($urls[$lokasi])) {
                // Sudah ada: simpan tanggal yang lebih baru bila ada.
                if ($ubah && (empty($urls[$lokasi]['ubah']) || $ubah > $urls[$lokasi]['ubah'])) {
                    $urls[$lokasi]['ubah'] = $ubah;
                }

                return;
            }
            $urls[$lokasi] = ['loc' => $lokasi, 'ubah' => $ubah, 'prioritas' => $prioritas];
        };

        $tetap = [
            url('/') => '1.0',
            route('berita') => '0.9',
            route('spmb') => '0.9',
            route('kelulusan') => '0.8',
            route('alumni') => '0.7',
            route('galeri') => '0.7',
            route('ekskul') => '0.7',
            route('toolsguru') => '0.6',
        ];
        foreach ($tetap as $lokasi => $prioritas) {
            $tambah($lokasi, null, $prioritas);
        }

        foreach (Page::terbit()->get() as $halaman) {
            $tambah(route('halaman', $halaman->slug), optional($halaman->updated_at)->toAtomString(), '0.6');
        }

        foreach (Post::terbit()->orderByDesc('terbit_pada')->get() as $tulisan) {
            $tanggal = $tulisan->terbit_pada ?: $tulisan->updated_at;
            $tambah(route('berita.show', $tulisan->slug), optional($tanggal)->toAtomString(), '0.8');
        }

        return response()->view('sitemap', ['urls' => array_values($urls)], 200, ['Content-Type' => 'application/xml; charset=utf-8']);
    }
}
