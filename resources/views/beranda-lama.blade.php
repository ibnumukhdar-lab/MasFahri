@extends('layouts.publik')
@section('judul', 'SMA IT Arafah Boarding School — Beriman, Berakhlak, Cerdas')
@section('isi')

@include('partials.hero', ['kecil' => 'Selamat Datang', 'judul' => '<span class="text-white">Membentuk Generasi Modern, yang Beriman, Berakhlak dan Cerdas</span>', 'sub' => 'Selamat datang di website resmi SMA Islam Terpadu Arafah Boarding School — Jalan Setia Usaha No. 4, Sampit, Kotawaringin Timur.'])

<section class="wrap -mt-8 relative z-10">
  <div class="kartu shadow-[0_18px_50px_-30px_rgba(31,58,95,.55)] overflow-hidden grid md:grid-cols-[1.1fr_1fr]">
    <div class="p-6 md:p-8">
      <h2 class="text-[20px] font-extrabold text-navy mb-3">Tentang SMA IT Arafah Boarding School</h2>
      <p class="text-[14px] leading-relaxed text-slate-600">{{ $tentang }}</p>
      <div class="flex flex-wrap gap-2 mt-5">
        <a href="{{ route('halaman', 'tentang-sma-it-arafah') }}" class="bg-navy text-white text-[13px] font-semibold px-4 py-2 rounded-xl">Profil lengkap</a>
        <a href="{{ route('spmb') }}" class="border border-navy-soft text-navy text-[13px] font-semibold px-4 py-2 rounded-xl">Daftar SPMB</a>
      </div>
    </div>
    <div class="min-h-[220px] bg-navy-soft">
      @if ($gambarHero)<img src="{{ $gambarHero }}" alt="" class="w-full h-full object-cover">@endif
    </div>
  </div>
</section>

<x-seksi judul="Kenapa SMA IT Arafah Menjadi Pilihan yang Tepat?">
  <p class="text-[14px] text-slate-600 -mt-4 mb-6">Karena siswa mendapatkan pembekalan dan pembinaan unggul, di antaranya:</p>
  <ul class="grid sm:grid-cols-2 gap-y-3 gap-x-8">
    @foreach ($kenapa as $k)
      <li class="flex gap-3 text-[14px]"><span class="mt-[3px] w-5 h-5 shrink-0 rounded-full bg-navy-soft text-navy grid place-items-center text-[11px] font-bold">✓</span><span>{{ $k }}</span></li>
    @endforeach
  </ul>
</x-seksi>

<x-seksi judul="Visi & Misi">
  <div class="kartu p-6 md:p-8 text-[14.5px] leading-relaxed text-slate-700">
    <p class="mb-4"><span class="chip">Visi</span></p>
    <p class="mb-5">{{ $visi }}</p>
    <p class="mb-4"><span class="chip">Misi</span></p>
    <ul class="space-y-2 list-disc pl-5">@foreach ($misi as $m)<li>{{ $m }}</li>@endforeach</ul>
    <p class="mt-5"><a href="{{ route('halaman', 'visi') }}" class="text-[13px] font-semibold text-navy">Selengkapnya →</a></p>
  </div>
</x-seksi>

<x-seksi judul="Program Unggulan">
  <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
    @foreach ($program as $p)
      <article class="kartu p-5 hover:shadow-[0_10px_30px_-18px_rgba(31,58,95,.45)] transition">
        <h3 class="font-bold text-navy mb-2 text-[15px]">{{ $p['judul'] }}</h3>
        <p class="text-[13.5px] leading-relaxed text-slate-600">{{ $p['teks'] }}</p>
      </article>
    @endforeach
  </div>
</x-seksi>

<x-seksi judul="Kurikulum Nasional Plus">
  <p class="text-[14px] text-slate-600 -mt-4 mb-6">{{ $kurikulumLead }}</p>
  <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
    @foreach ($kurikulum as $p)
      <article class="kartu p-5">
        <h3 class="font-bold text-navy mb-2 text-[14.5px]">{{ $p['judul'] }}</h3>
        <p class="text-[13px] leading-relaxed text-slate-600">{{ $p['teks'] }}</p>
      </article>
    @endforeach
  </div>
  <p class="mt-5"><a href="{{ route('halaman', 'kurikulum') }}" class="text-[13px] font-semibold text-navy">Detail kurikulum →</a></p>
</x-seksi>

<x-seksi judul="Prestasi & Kegiatan Siswa">
  <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
    @foreach ($galeri as $f)
      <a href="{{ route('galeri') }}" class="block overflow-hidden rounded-2xl border border-slate-200">
        <img src="{{ $f }}" alt="" class="w-full h-40 object-cover hover:scale-105 transition duration-500" loading="lazy">
      </a>
    @endforeach
  </div>
  <p class="mt-5"><a href="{{ route('galeri') }}" class="text-[13px] font-semibold text-navy">Lihat semua galeri →</a></p>
</x-seksi>

<section class="bg-white border-y border-slate-200">
  <div class="wrap py-14 grid md:grid-cols-[1.15fr_1fr] gap-10 items-start">
    <div>
      <span class="chip">SPMB {{ \App\Models\Setting::ambil('tahun_ajaran', '2026/2027') }}</span>
      <h2 class="text-[24px] md:text-[28px] font-extrabold text-navy mt-3 mb-3">Daftarkan Putra/Putri Anda di SMA IT Arafah</h2>
      <p class="text-[14.5px] leading-relaxed text-slate-600 mb-5">SPMB dibuka sepanjang tahun dengan sistem waiting list, tersedia juga jalur prestasi bagi murid dengan pencapaian luar biasa. Pendaftaran tercatat otomatis oleh sistem dan mendapat nomor pendaftaran.</p>
      <ul class="text-[13.5px] space-y-2 text-slate-600">
        <li>· Jalur reguler: waiting list sepanjang tahun</li>
        <li>· Jalur prestasi untuk pencapaian bidang tertentu</li>
        <li>· Program: Tahfizh, Kulliyat Diiniyah, Boarding, BEE SMART, Student Root</li>
      </ul>
    </div>
    <div class="kartu p-6">
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

<x-seksi judul="Berita Terbaru">
  <div class="grid md:grid-cols-3 gap-4">@foreach ($berita as $post)@include('partials.kartu-berita', ['post' => $post])@endforeach</div>
  <p class="mt-6"><a href="{{ route('berita') }}" class="text-[13px] font-semibold text-navy">Semua berita →</a></p>
</x-seksi>
@endsection
