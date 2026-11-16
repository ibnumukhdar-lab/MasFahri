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
</style>
</head>
<body>
    <div id="slide" class="tampak"></div>
    <div id="jam"></div>
    <div id="kosong" hidden>
        <div class="logo">🏫</div>
        <div>Belum ada slide yang dipasang.<br>Aktifkan lewat menu <b>Display TV</b> di panel <b>/kelola</b>.</div>
    </div>

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
})();
</script>
</body>
</html>
