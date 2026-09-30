{{-- Pita judul halaman dalam: kicker kecil + judul + kalimat pengantar. --}}
<section class="bg-navy text-white" style="position:relative;overflow:hidden">
  <div aria-hidden="true" style="position:absolute;inset:0;background:radial-gradient(120% 110% at 10% 0%, rgba(255,255,255,.12) 0%, rgba(255,255,255,0) 58%)"></div>
  <div class="wrap" style="position:relative">
    <div class="max-w-[820px] py-12 md:py-20">
      <p class="text-putih-70 text-[12px] font-semibold tracking-widest uppercase mb-3 flex items-center gap-2.5">
        <span aria-hidden="true" style="display:inline-block;width:22px;height:3px;border-radius:3px;background:#fdd100"></span>
        {{ $kecil ?? 'SMA IT Arafah Boarding School' }}
      </p>
      <h1 class="text-3xl md:text-[42px] font-extrabold leading-tight mb-4">{!! $judul !!}</h1>
      @isset($sub)<p class="text-putih-85 text-[15px] md:text-[17px] leading-relaxed">{!! $sub !!}</p>@endisset
    </div>
  </div>
</section>
