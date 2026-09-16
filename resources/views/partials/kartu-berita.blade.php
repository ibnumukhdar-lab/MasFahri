<article class="kartu overflow-hidden flex flex-col">
  @if ($post->gambar_url)<img src="{{ $post->gambar_url }}" alt="" class="w-full h-40 object-cover">@endif
  <div class="p-5 flex-1">
    <div class="flex items-center gap-2 mb-2">
      <span class="chip">{{ $post->kategori_utama->nama ?? 'Berita' }}</span>
      <span class="text-[11.5px] text-slate-400">{{ $post->tanggal_indonesia }}</span>
    </div>
    <h3 class="font-bold text-navy text-[15px] leading-snug mb-2">{{ \Illuminate\Support\Str::limit($post->judul, 84) }}</h3>
    <p class="text-[13px] text-slate-500 leading-relaxed">{{ $post->ringkas }}</p>
  </div>
  <a href="{{ route('berita.show', $post->slug) }}" class="px-5 pb-5 text-[13px] font-semibold text-navy">Baca selengkapnya →</a>
</article>
