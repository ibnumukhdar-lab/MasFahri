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

        // Beranda disusun dari bagian yang bisa diatur di panel: Konten → Halaman Beranda.
        return view('beranda', [
            'bagian' => BerandaBagian::aktif()->orderBy('urutan')->get(),
            'berita' => $berita,
            'galeri' => $galeri,
            'gambarHero' => $gambarHero,
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
