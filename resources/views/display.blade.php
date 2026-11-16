<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex">
<title>Display · SMA IT Arafah</title>
<style>
    html, body { margin: 0; padding: 0; height: 100%; background: #000; overflow: hidden; }
    #slide {
        position: fixed; inset: 0;
        background: #000 center / contain no-repeat;
        opacity: 0; transition: opacity .9s ease;
    }
    #slide.tampak { opacity: 1; }
    #jam {
        position: fixed; right: 2.2vw; bottom: 2.4vh;
        font: 600 3.2vw/1 system-ui, sans-serif;
        color: #fff; text-shadow: 0 2px 10px rgba(0,0,0,.8);
        letter-spacing: .06em; opacity: .92; z-index: 5;
    }
    #kosong {
        position: fixed; inset: 0; display: flex; flex-direction: column;
        align-items: center; justify-content: center; gap: 1.5rem;
        color: #8ea2c0; font: 400 1.6rem/1.5 system-ui, sans-serif;
        text-align: center; padding: 2rem;
    }
    #kosong[hidden] { display: none; }
    #kosong .logo { font-size: 3rem; }
    @media (prefers-reduced-motion: reduce) { #slide { transition: none; } }

    /* ==== Tombol layar penuh (pojok kiri bawah, samar sampai disentuh) ==== */
    #kfs {
        position: fixed; left: 2.2vw; bottom: 2.4vh; z-index: 6;
        display: inline-flex; align-items: center; gap: .55rem;
        padding: .55rem .9rem; border-radius: 12px;
        border: 1px solid rgba(255,255,255,.28);
        background: rgba(10,20,40,.55); color: #fff;
        font: 600 clamp(13px, 1.1vw, 16px)/1 system-ui, sans-serif;
        letter-spacing: .02em; cursor: pointer;
        opacity: .35; transition: opacity .25s ease, background .25s ease;
        backdrop-filter: blur(4px);
    }
    #kfs:hover, #kfs:focus-visible { opacity: 1; background: rgba(10,20,40,.85); }
    #kfs:focus { outline: 2px solid rgba(255,255,255,.6); outline-offset: 2px; }
    #kfs svg { display: block; width: 1.1em; height: 1.1em; }
    #kfs.keluar { opacity: .35; }
    /* Di perangkat sentuh tombol tetap samar; sentuhan pertama menyala, kedua menekan */
    @media (pointer: coarse) { #kfs { opacity: .5; } }
    html:fullscreen #slide, html:-webkit-full-screen #slide { inset: 0; }
</style>
</head>
<body>
    <div id="slide" class="tampak"></div>
    <div id="jam"></div>
    <div id="kosong" hidden>
        <div class="logo">🏫</div>
        <div>Belum ada slide yang dipasang.<br>Aktifkan lewat menu <b>Display TV</b> di panel <b>/kelola</b>.</div>
    </div>

    <button id="kfs" type="button" title="Layar penuh (F)"
            aria-label="Buka layar penuh">
        <svg id="kfs-ikon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
             stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M3 8V3h5M21 8V3h-5M3 16v5h5M21 16v5h-5"/>
        </svg>
        <span id="kfs-teks">Layar penuh</span>
    </button>

<script>
(function () {
    var slides = @json($dataJs);
    var el = document.getElementById('slide');
    var kosong = document.getElementById('kosong');
    var LAMAU = 900; // durasi transisi pudar (ms) — sama dengan CSS
    var idx = 0;

    if (!slides.length) {
        el.classList.remove('tampak');
        kosong.hidden = false;
    } else {
        el.style.backgroundImage = 'url("' + slides[0].url + '")';
        el.classList.add('tampak');
        if (slides.length > 1) {
            (function lanjut() {
                var tahan = Math.max(3, slides[idx].durasi) * 1000;
                setTimeout(function () {
                    el.classList.remove('tampak');
                    setTimeout(function () {
                        idx = (idx + 1) % slides.length;
                        el.style.backgroundImage = 'url("' + slides[idx].url + '")';
                        el.classList.add('tampak');
                        lanjut();
                    }, LAMAU);
                }, tahan);
            })();
        }
    }

    function jam() {
        var d = new Date();
        var j = String(d.getHours()).padStart(2, '0');
        var m = String(d.getMinutes()).padStart(2, '0');
        document.getElementById('jam').textContent = j + ':' + m;
    }
    setInterval(jam, 5000); jam();

    // Segarkan halaman tiap 10 menit supaya slide baru/pindah urutan ikut tampil di TV
    // tanpa perlu menyentuh perangkat layar.
    setTimeout(function () { location.reload(); }, 10 * 60 * 1000);

    /* ==== Layar penuh ==== */
    var tombol = document.getElementById('kfs');
    var teks = document.getElementById('kfs-teks');
    var ikon = document.getElementById('kfs-ikon');

    function sedangPenuh() {
        return !!(document.fullscreenElement || document.webkitFullscreenElement);
    }
    function perbarui() {
        var penuh = sedangPenuh();
        teks.textContent = penuh ? 'Keluar layar penuh' : 'Layar penuh';
        tombol.classList.toggle('keluar', penuh);
        tombol.setAttribute('aria-label', teks.textContent);
        // ikon: masuk = sudut keluar; keluar = sudut menutup
        ikon.innerHTML = penuh
            ? '<path d="M10 3v5H5M14 21v-5h5M21 14h-5v5M3 10h5V3"/>'
            : '<path d="M3 8V3h5M21 8V3h-5M3 16v5h5M21 16v5h-5"/>';
    }
    function toggle() {
        var penuh = sedangPenuh();
        if (penuh) {
            (document.exitFullscreen || document.webkitExitFullscreen).call(document);
        } else {
            var minta = document.documentElement.requestFullscreen || document.documentElement.webkitRequestFullscreen;
            if (minta) {
                minta.call(document.documentElement).catch(function () {
                    teks.textContent = 'Layar penuh tidak diizinkan';
                    setTimeout(function () { perbarui(); }, 2500);
                });
            }
        }
    }
    tombol.addEventListener('click', toggle);
    document.addEventListener('fullscreenchange', perbarui);
    document.addEventListener('webkitfullscreenchange', perbarui);
    // Pintasan: tombol F (masuk/keluar)
    document.addEventListener('keydown', function (e) {
        if (e.key === 'f' || e.key === 'F') {
            if (!e.ctrlKey && !e.altKey && !e.metaKey) { e.preventDefault(); toggle(); }
        }
        if (e.key === 'Escape' && sedangPenuh()) { /* biarkan browser menangani Esc */ }
    });
    perbarui();
})();
</script>
</body>
</html>
