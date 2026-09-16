@props(['judul' => '', 'kelas' => ''])
<section class="wrap py-12 md:py-14 {{ $kelas }}">
  @if ($judul)<h2 class="text-[22px] md:text-[27px] font-extrabold text-navy mb-2">{{ $judul }}</h2>
  <div class="h-[3px] w-14 bg-navy-line rounded mb-7"></div>@endif
  {{ $slot }}
</section>
