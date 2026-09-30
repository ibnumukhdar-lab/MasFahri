@extends('layouts.publik')
@section('judul', $page->meta_judul ?: $page->judul . ' — SMA IT Arafah Boarding School')
@section('deskripsi', $page->meta_deskripsi ?: \Illuminate\Support\Str::limit(strip_tags($page->body ?? ''), 155))
@section('isi')

@include('partials.hero', ['kecil' => $kecil ?? 'Profil', 'judul' => $page->judul, 'sub' => $sub ?? null])

@php
    $isi = (string) ($page->body ?: '<p>Isi halaman ini belum diisi.</p>');

    // 1. Paragraf penanda bagian (mis. <p><strong>VISI</strong></p>) → label kecil beraksen.
    $isi = preg_replace_callback(
        '~<p>\s*<(strong|b)>\s*([^<]{2,26})\s*</\1>\s*</p>~iu',
        function ($m) {
            $teks = trim(html_entity_decode(strip_tags($m[2])));
            if ($teks === '' || mb_strtoupper($teks) !== $teks) {
                return $m[0];   // hanya paragraf yang seluruhnya KAPITAL yang dijadikan label
            }
            return '<p class="hb-label">' . e(mb_convert_case(mb_strtolower($teks), MB_CASE_TITLE)) . '</p>';
        },
        $isi
    );

    // 2. Paragraf tepat setelah label → kartu sorotan (mis. kalimat visi).
    $isi = preg_replace('~(<p class="hb-label">[^<]*</p>)\s*<p>~u', '$1<p class="hb-kutipan">', $isi);

    // 3. Daftar → kartu bernomor.
    $isi = preg_replace('~<(ol|ul)(\s[^>]*)?>~iu', '<$1$2 class="hb-daftar">', $isi);
@endphp

<section class="wrap py-10 md:py-16">
  <style>
    /* ====== halaman statis (profil/kurikulum/tahfizh/visi/…): enak dibaca, hierarki jelas ====== */
    .hb-isi{max-width:860px;margin:0 auto;background:#fff;border:1px solid #e6ecf5;border-radius:22px;
      padding:24px 20px;font-family:'DM Sans',system-ui,sans-serif;font-size:15.5px;line-height:1.85;color:#3d5670}
    @media (min-width:768px){.hb-isi{padding:40px 44px;border-radius:28px}}
    .hb-isi>*:first-child{margin-top:0}
    .hb-isi h1,.hb-isi h2{font-family:'Manrope',system-ui,sans-serif;font-size:22px;line-height:1.28;font-weight:800;
      letter-spacing:-.02em;color:#234288;margin:0 0 6px}
    @media (min-width:768px){.hb-isi h1,.hb-isi h2{font-size:28px}}
    .hb-isi h2:not(:first-child),.hb-isi h1:not(:first-child){margin-top:38px}
    .hb-isi h2::after,.hb-isi h1::after{content:"";display:block;width:52px;height:3px;border-radius:3px;background:#fdd100;margin-top:12px}
    .hb-isi h3{font-family:'Manrope',system-ui,sans-serif;display:flex;gap:10px;align-items:center;
      font-size:17.5px;font-weight:800;color:#234288;margin:28px 0 8px;line-height:1.4}
    .hb-isi h3::before{content:"";flex:none;width:9px;height:9px;border-radius:999px;background:#fdd100}
    .hb-isi h4{font-size:12px;font-weight:800;letter-spacing:.15em;text-transform:uppercase;color:#667282;margin:24px 0 8px}
    .hb-isi p{margin:0 0 16px}
    .hb-isi strong,.hb-isi b{color:#234288}
    .hb-isi a{color:#234288;font-weight:700;text-decoration:none;border-bottom:2px solid #dbe7f4}
    .hb-isi a:hover{border-color:#fdd100}
    .hb-isi img{border-radius:18px;width:100%;height:auto;display:block;margin:6px 0 20px}
    .hb-isi blockquote{margin:0 0 18px;background:#f7faff;border-left:4px solid #fdd100;border-radius:14px;
      padding:18px 20px;color:#3d5670}
    .hb-isi table{width:100%;border-collapse:collapse;font-size:14px;margin:0 0 18px}
    .hb-isi th{background:#eef4fb;color:#234288;text-align:left;padding:10px 12px;border:1px solid #e2e9f2}
    .hb-isi td{padding:10px 12px;border:1px solid #e2e9f2}
    /* label bagian (VISI / MISI / …) */
    .hb-label{display:flex;align-items:center;gap:10px;font-family:'Manrope',system-ui,sans-serif;font-size:12px;
      font-weight:800;letter-spacing:.18em;text-transform:uppercase;color:#234288;margin:30px 0 14px}
    .hb-label::before{content:"";width:22px;height:3px;border-radius:3px;background:#fdd100;flex:none}
    /* kalimat sorotan (visi) */
    .hb-kutipan{background:#f7faff;border-left:5px solid #fdd100;border-radius:16px;padding:22px;
      font-family:'Manrope',system-ui,sans-serif;font-size:17px;line-height:1.8;color:#234288;font-weight:600;margin:0 0 20px}
    @media (min-width:768px){.hb-kutipan{font-size:18.5px;padding:26px 28px}}
    /* daftar → kartu bernomor */
    .hb-daftar{list-style:none;padding:0;margin:0 0 22px;display:grid;gap:12px;counter-reset:hb}
    @media (min-width:768px){
      .hb-daftar{grid-template-columns:repeat(2,minmax(0,1fr));gap:14px}
      /* jumlah butir ganjil → kartu terakhir melebar penuh supaya tidak ada ruang kosong menganggur */
      .hb-daftar li:last-child:nth-child(odd){grid-column:1 / -1}
    }
    .hb-daftar li{position:relative;background:#fff;border:1px solid #e6ecf5;border-radius:18px;
      padding:18px 20px 18px 62px;font-size:14.5px;line-height:1.72;counter-increment:hb;transition:border-color .18s ease,box-shadow .18s ease}
    .hb-daftar li:hover{border-color:#fdd100;box-shadow:0 16px 30px -24px rgba(35,66,136,.6)}
    .hb-daftar li::before{content:counter(hb);position:absolute;left:18px;top:16px;width:28px;height:28px;
      border-radius:999px;background:#e9f7fe;color:#234288;display:grid;place-items:center;
      font-family:'Manrope',system-ui,sans-serif;font-size:13px;font-weight:800}
    .hb-daftar li p{margin:0}
    .hb-daftar li p+p{margin-top:8px}
  </style>
  <div class="hb-isi">
    {!! $isi !!}
  </div>
</section>
@endsection
