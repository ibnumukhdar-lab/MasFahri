<?php

namespace App\Http\Controllers;

use App\Models\Category;
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

        return view('beranda', [
            'berita' => $berita,
            'tentang' => config('smaita.tentang'),
            'visi' => config('smaita.visi'),
            'misi' => config('smaita.misi'),
            'kenapa' => config('smaita.kenapa'),
            'program' => config('smaita.program'),
            'kurikulum' => config('smaita.kurikulum'),
            'kurikulumLead' => config('smaita.kurikulum_lead'),
            'galeri' => $album ? $album->photos->take(6)->map(fn ($f) => $f->url) : [],
            'gambarHero' => $album && $album->photos->first() ? $album->photos->first()->url : null,
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
        $isi = "User-agent: *\nAllow: /\n\nSitemap: " . url('/sitemap.xml') . "\n";

        return response($isi, 200, ['Content-Type' => 'text/plain']);
    }

    public function sitemap()
    {
        $urls = collect([url('/'), route('berita'), route('spmb'), route('kelulusan'), route('alumni'), route('galeri'), route('toolsguru')])
            ->merge(Page::terbit()->pluck('slug')->map(fn ($s) => route('halaman', $s)))
            ->merge(Post::terbit()->pluck('slug')->map(fn ($s) => route('berita.show', $s)));

        return response()->view('sitemap', ['urls' => $urls], 200, ['Content-Type' => 'application/xml']);
    }
}
