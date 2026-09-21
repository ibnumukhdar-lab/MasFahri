{{--
  Satu bagian beranda. Jenisnya diatur di panel (Konten → Halaman Beranda).
  Menambah jenis baru = tambahkan satu @case di bawah + pilihan di BerandaBagian::jenisDaftar().
--}}
@php
    $jenis = $b->jenis;
    $butir = $b->butir;
    $gambar = $b->gambar_url;
    $punyaTombol = filled($b->tombol_teks) && filled($b->tombol_tautan);
    $tautanTombol = $punyaTombol
        ? (str_starts_with((string) $b->tombol_tautan, 'http') ? $b->tombol_tautan : url($b->tombol_tautan))
        : null;

    // Teks boleh berisi beberapa paragraf (dipisah baris kosong) — dari halaman bisa lebih dari satu.
    $paragrafTeks = filled($b->teks)
        ? array_values(array_filter(array_map('trim', preg_split('~\n\s*\n~u', (string) $b->teks))))
        : [];
    $paragrafSub = filled($b->subjudul)
        ? array_values(array_filter(array_map('trim', preg_split('~\n\s*\n~u', (string) $b->subjudul))))
        : [];
@endphp

@auth
  <a class="bg-ubah" style="position:relative;float:right;margin:10px 20px -30px 0" href="{{ url('/kelola/beranda/'.$b->id.'/edit') }}" title="Ubah bagian ini di panel">✎ Ubah bagian ini</a>
@endauth

@switch($jenis)

  {{-- ===================== HERO ===================== --}}
  @case('hero')
    @php $foto = $gambar ?: ($gambarHero ?? null); @endphp
    <section class="bg-bagian" style="padding-top:24px">
      <div class="wrap">
        <div class="relative overflow-hidden rounded-[26px] bg-navy" style="min-height:320px">
          @if ($foto)
            <img src="{{ $foto }}" alt="{{ $b->judul }}" class="absolute inset-0 w-full h-full object-cover">
          @endif
          <div class="absolute inset-0" style="background:linear-gradient(100deg, rgba(13,26,45,.94) 0%, rgba(20,42,70,.80) 45%, rgba(24,52,86,.42) 100%)"></div>
          <div class="relative flex flex-col justify-center px-6 py-12 md:px-14 md:py-20" style="min-height:320px">
            <div class="max-w-[660px]">
              @if (filled($b->subjudul))
                <span class="inline-block text-[11px] font-bold tracking-wide uppercase px-3 py-1 rounded-full" style="background:rgba(255,255,255,.14);color:#dbe7f4">{{ $b->subjudul }}</span>
              @endif
              @if (filled($b->judul))
                <h1 class="text-white font-extrabold mt-4" style="font-size:27px;line-height:1.16;letter-spacing:-.02em">{{ $b->judul }}</h1>
              @endif
              @foreach ($paragrafTeks as $par)
                <p class="mt-4 leading-relaxed" style="color:rgba(255,255,255,.82);font-size:14.5px">{{ $par }}</p>
              @endforeach

              @if ($punyaTombol)
                <div class="mt-7 flex flex-wrap items-center gap-3">
                  <a href="{{ $tautanTombol }}" class="bg-hero-tbl bg-white text-navy">{{ $b->tombol_teks }} →</a>
                  <a href="{{ route('spmb') }}" class="bg-hero-tbl" style="border:1px solid rgba(255,255,255,.4);color:#fff">Pendaftaran SPMB</a>
                </div>
              @endif

              @if (count($butir))
                <div class="mt-7 flex flex-wrap gap-2">
                  @foreach ($butir as $chip)
                    @if (filled($chip['teks'] ?? null))
                      <span class="text-[12px] font-semibold px-3 py-1.5 rounded-full" style="background:rgba(255,255,255,.12);color:#eaf1f9">{{ $chip['teks'] }}</span>
                    @endif
                  @endforeach
                </div>
              @endif
            </div>
          </div>
        </div>
      </div>
    </section>
    @break

  {{-- ===================== TENTANG ===================== --}}
  @case('tentang')
    <section class="bg-bagian">
      <div class="wrap">
        <div class="bg-kartu overflow-hidden grid md:grid-cols-[1.05fr_1fr]">
          <div class="p-7 md:p-10">
            <span class="chip">Tentang</span>
            <h2 class="bg-judul mt-3">{{ $b->judul }}</h2>
            @foreach ($paragrafTeks as $par)<p class="bg-lead">{{ $par }}</p>@endforeach
            <div class="flex flex-wrap gap-2 mt-6">
              <a href="{{ $tautanTombol ?: route('halaman', 'tentang-sma-it-arafah') }}" class="text-[13px] font-semibold px-4 py-2.5 rounded-xl bg-navy text-white">{{ $b->tombol_teks ?: 'Profil lengkap' }}</a>
              <a href="{{ route('spmb') }}" class="text-[13px] font-semibold px-4 py-2.5 rounded-xl" style="border:1px solid #dbe7f4;color:#1f3a5f">Daftar SPMB</a>
            </div>
          </div>
          <div class="bg-navy-soft" style="min-height:250px">
            @if ($gambar ?: ($gambarHero ?? null))
              <img src="{{ $gambar ?: $gambarHero }}" alt="{{ $b->judul }}" class="w-full h-full object-cover">
            @endif
          </div>
        </div>
      </div>
    </section>
    @break

  {{-- ===================== KEUNGGULAN ===================== --}}
  @case('keunggulan')
    <section class="bg-bagian">
      <div class="wrap">
        <div class="max-w-[720px] mb-9">
          <span class="chip">Keunggulan</span>
          <h2 class="bg-judul mt-3">{{ $b->judul }}</h2>
          @foreach ($paragrafSub as $par)<p class="bg-lead">{{ $par }}</p>@endforeach
        </div>
        <ul class="grid sm:grid-cols-2 gap-x-10 gap-y-5">
          @foreach ($butir as $k)
            <li class="flex gap-3">
              <span class="bg-centang">✓</span>
              <span class="text-[14.5px] leading-relaxed text-slate-600">
                @if (filled($k['judul'] ?? null))<strong class="text-navy">{{ $k['judul'] }}</strong> @endif{{ $k['teks'] ?? '' }}
              </span>
            </li>
          @endforeach
        </ul>
      </div>
    </section>
    @break

  {{-- ===================== VISI & MISI ===================== --}}
  @case('visi_misi')
    <section class="bg-bagian">
      <div class="wrap">
        <div class="max-w-[720px] mb-9">
          <span class="chip">Arah Sekolah</span>
          <h2 class="bg-judul mt-3">{{ $b->judul }}</h2>
        </div>
        <div class="bg-kartu p-7 md:p-10">
          @foreach ($paragrafTeks as $par)
            <p class="text-[16px] md:text-[18px] leading-relaxed text-navy" style="border-left:3px solid #c6d8ea;padding-left:16px">{{ $par }}</p>
          @endforeach
          @if (count($butir))
            <p class="mt-8 mb-4"><span class="chip">Misi</span></p>
            <ul class="grid md:grid-cols-2 gap-x-10 gap-y-4">
              @foreach ($butir as $m)
                <li class="flex gap-3">
                  <span class="bg-centang">✓</span>
                  <span class="text-[14.5px] leading-relaxed text-slate-600">
                    @if (filled($m['judul'] ?? null))<strong class="text-navy">{{ $m['judul'] }}:</strong> @endif{{ $m['teks'] ?? '' }}
                  </span>
                </li>
              @endforeach
            </ul>
          @endif
        </div>
      </div>
    </section>
    @break

  {{-- ===================== PROGRAM / KURIKULUM ===================== --}}
  @case('program')
  @case('kurikulum')
    <section class="bg-bagian">
      <div class="wrap">
        <div class="max-w-[720px] mb-9">
          <span class="chip">{{ $jenis === 'program' ? 'Program' : 'Kurikulum' }}</span>
          <h2 class="bg-judul mt-3">{{ $b->judul }}</h2>
          @foreach ($paragrafSub as $par)<p class="bg-lead">{{ $par }}</p>@endforeach
        </div>
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
          @foreach ($butir as $i => $p)
            <article class="bg-kartu p-6">
              <span class="inline-grid place-items-center w-9 h-9 rounded-xl mb-4" style="background:#eef4fb;color:#1f3a5f;font-weight:800;font-size:13px">{{ sprintf('%02d', $i + 1) }}</span>
              @if (filled($p['judul'] ?? null))<h3 class="font-bold text-navy text-[15.5px] mb-2">{{ $p['judul'] }}</h3>@endif
              <p class="text-[13.5px] leading-relaxed text-slate-600">{{ $p['teks'] ?? '' }}</p>
            </article>
          @endforeach
        </div>
      </div>
    </section>
    @break

  {{-- ===================== STATISTIK ===================== --}}
  @case('statistik')
    <section class="bg-bagian">
      <div class="wrap">
        <div class="max-w-[720px] mb-9">
          <span class="chip">Angka</span>
          <h2 class="bg-judul mt-3">{{ $b->judul }}</h2>
          @foreach ($paragrafSub as $par)<p class="bg-lead">{{ $par }}</p>@endforeach
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-5">
          @foreach ($butir as $s)
            <div class="bg-kartu p-6 text-center">
              <p class="bg-angka">{{ $s['judul'] ?? '' }}</p>
              <p class="text-[13px] text-slate-500 mt-2 leading-snug">{{ $s['teks'] ?? '' }}</p>
            </div>
          @endforeach
        </div>
      </div>
    </section>
    @break

  {{-- ===================== GALERI ===================== --}}
  @case('galeri')
    <section class="bg-bagian">
      <div class="wrap">
        <div class="max-w-[720px] mb-9">
          <span class="chip">Galeri</span>
          <h2 class="bg-judul mt-3">{{ $b->judul }}</h2>
          @foreach ($paragrafSub as $par)<p class="bg-lead">{{ $par }}</p>@endforeach
        </div>
        @if (count($galeri ?? []))
          <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
            @foreach ($galeri as $f)
              <a href="{{ route('galeri') }}" class="bg-foto block aspect-square">
                <img src="{{ $f }}" alt="Kegiatan siswa" class="w-full h-full object-cover" loading="lazy">
              </a>
            @endforeach
          </div>
          <p class="mt-7"><a href="{{ route('galeri') }}" class="text-[13px] font-semibold text-navy">Lihat semua galeri →</a></p>
        @else
          <p class="text-[14px] text-slate-500">Belum ada foto di galeri. Unggah lewat panel → Galeri.</p>
        @endif
      </div>
    </section>
    @break

  {{-- ===================== BERITA ===================== --}}
  @case('berita')
    <section class="bg-bagian">
      <div class="wrap">
        <div class="max-w-[720px] mb-9">
          <span class="chip">Berita</span>
          <h2 class="bg-judul mt-3">{{ $b->judul }}</h2>
          @foreach ($paragrafSub as $par)<p class="bg-lead">{{ $par }}</p>@endforeach
        </div>
        @if (($berita ?? collect())->count())
          <div class="grid md:grid-cols-3 gap-5">
            @foreach ($berita as $post)
              @include('partials.kartu-berita', ['post' => $post])
            @endforeach
          </div>
          <p class="mt-8"><a href="{{ route('berita') }}" class="text-[13px] font-semibold text-navy">Semua berita →</a></p>
        @endif
      </div>
    </section>
    @break

  {{-- ===================== SPMB (teks + formulir) ===================== --}}
  @case('spmb')
    <section style="background:#fff;border-top:1px solid #e7eef6;border-bottom:1px solid #e7eef6">
      <div class="wrap bg-bagian grid md:grid-cols-[1.1fr_1fr] gap-10 items-start">
        <div>
          <span class="chip">SPMB {{ \App\Models\Setting::ambil('tahun_ajaran', '2026/2027') }}</span>
          <h2 class="bg-judul mt-3">{{ $b->judul }}</h2>
          @foreach ($paragrafTeks as $par)<p class="bg-lead">{{ $par }}</p>@endforeach
          @if (count($butir))
            <ul class="mt-6 space-y-2.5">
              @foreach ($butir as $p)
                <li class="text-[13.5px] text-slate-600 flex gap-2">
                  <span class="bg-centang" style="width:18px;height:18px;font-size:11px">·</span>
                  <span>@if (filled($p['judul'] ?? null))<strong class="text-navy">{{ $p['judul'] }}:</strong> @endif{{ $p['teks'] ?? '' }}</span>
                </li>
              @endforeach
            </ul>
          @endif
        </div>
        <div class="bg-kartu p-6 md:p-7">
          <p class="font-bold text-navy mb-4">Formulir Pendaftaran Singkat</p>
          <form action="{{ route('spmb.simpan') }}" method="post" class="space-y-3 text-[13.5px]">
            @csrf
            <input name="nama_wali" required placeholder="Nama Wali" class="w-full border border-slate-200 rounded-xl px-3.5 py-2.5">
            <input name="nama_siswa" required placeholder="Nama Siswa" class="w-full border border-slate-200 rounded-xl px-3.5 py-2.5">
            <select name="jenis_kelamin" class="w-full border border-slate-200 rounded-xl px-3.5 py-2.5 text-slate-500">
              <option value="">Jenis Kelamin</option><option>Laki-laki</option><option>Perempuan</option>
            </select>
            <input name="sekolah_asal" placeholder="Sekolah Asal" class="w-full border border-slate-200 rounded-xl px-3.5 py-2.5">
            <input name="whatsapp" placeholder="Nomor WhatsApp Wali" class="w-full border border-slate-200 rounded-xl px-3.5 py-2.5">
            <button class="w-full bg-navy text-white rounded-xl py-2.5 font-semibold">Kirim Pendaftaran</button>
            <p class="text-[12px] text-slate-500">Data tersimpan di sistem sekolah dan diteruskan ke admin lewat WhatsApp.</p>
          </form>
        </div>
      </div>
    </section>
    @break

  {{-- ===================== TANYA-JAWAB ===================== --}}
  @case('faq')
    <section class="bg-bagian">
      <div class="wrap">
        <div class="max-w-[720px] mb-9">
          <span class="chip">Tanya-jawab</span>
          <h2 class="bg-judul mt-3">{{ $b->judul }}</h2>
          @foreach ($paragrafSub as $par)<p class="bg-lead">{{ $par }}</p>@endforeach
        </div>
        <div class="space-y-3 max-w-[860px]">
          @foreach ($butir as $t)
            <details class="bg-tanya">
              <summary>{{ $t['judul'] ?? 'Pertanyaan' }}</summary>
              <p>{{ $t['teks'] ?? '' }}</p>
            </details>
          @endforeach
        </div>
      </div>
    </section>
    @break

  {{-- ===================== AJAKAN SINGKAT ===================== --}}
  @case('cta')
    <section style="background:#1f3a5f">
      <div class="wrap bg-bagian flex flex-col md:flex-row md:items-center md:justify-between gap-6">
        <div class="max-w-[620px]">
          <h2 class="text-white font-extrabold" style="font-size:22px;line-height:1.25">{{ $b->judul }}</h2>
          @foreach ($paragrafTeks as $par)<p class="mt-3 text-[14.5px] leading-relaxed" style="color:rgba(255,255,255,.82)">{{ $par }}</p>@endforeach
        </div>
        @if ($punyaTombol)
          <a href="{{ $tautanTombol }}" class="bg-hero-tbl bg-white text-navy" style="white-space:nowrap">{{ $b->tombol_teks }} →</a>
        @endif
      </div>
    </section>
    @break

@endswitch
