<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ $paket->judul }} — CBT SPMB SMA IT Arafah</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
  <style>
    body{font-family:'Plus Jakarta Sans',system-ui,sans-serif;-webkit-tap-highlight-color:transparent}
    [x-cloak]{display:none!important}
    .navy{background:#1f3a5f}
  </style>
</head>
<body class="bg-slate-100 h-[100dvh] overflow-hidden text-slate-800 flex flex-col select-none" x-data="cbtApp()">

  {{-- Header ringkas: judul, timer, pelanggaran, tombol selesai --}}
  <header class="bg-white border-b border-slate-200 px-3 py-2 z-40 shadow-sm shrink-0">
    <div class="max-w-5xl mx-auto flex justify-between items-center gap-2">
      <div class="min-w-0">
        <span class="text-[9px] sm:text-[10px] font-black uppercase tracking-wider text-slate-400 block truncate">Tes CBT SPMB · {{ $registrasi->nomor }}</span>
        <h1 class="text-xs sm:text-sm font-extrabold text-slate-900 truncate max-w-[46vw] sm:max-w-md">{{ $paket->judul }}</h1>
      </div>
      <div class="flex items-center gap-1.5 sm:gap-3 shrink-0">
        <template x-if="!isFinished">
          <div class="bg-rose-50 border border-rose-200 text-rose-700 px-2 py-1 rounded-lg font-black text-[11px] sm:text-xs flex items-center gap-1 shadow-sm">
            <span>⏳</span><span x-text="formatTimer()"></span>
          </div>
        </template>
        <template x-if="violations > 0">
          <div class="bg-amber-100 text-amber-900 border border-amber-300 text-[9px] sm:text-[10px] font-black px-2 py-1 rounded-lg flex items-center gap-1">
            <span>⚠️</span><span x-text="violations + ' Pelanggaran'"></span>
          </div>
        </template>
        <button type="button" @click="selesai()" x-show="!isFinished"
                class="bg-slate-900 hover:bg-black text-white font-bold px-3 py-1.5 rounded-lg text-[11px] transition cursor-pointer">
          Selesai &rarr;
        </button>
      </div>
    </div>
  </header>

  {{-- Ruang kerja --}}
  <main class="flex-1 min-h-0 overflow-hidden w-full max-w-5xl mx-auto p-2 sm:p-3 grid grid-cols-1 lg:grid-cols-4 gap-2 sm:gap-3 grid-rows-[minmax(0,1fr)_auto] lg:grid-rows-[minmax(0,1fr)]">

    {{-- Kartu soal --}}
    <div class="lg:col-span-3 flex flex-col min-h-0 gap-2">
      <div class="bg-white rounded-2xl p-3 sm:p-4 border border-slate-200 shadow-sm flex-1 min-h-0 flex flex-col" x-show="!isFinished">
        <div class="shrink-0 flex justify-between items-center pb-2 mb-2 border-b border-slate-100">
          <span class="bg-indigo-50 text-indigo-700 text-[10px] sm:text-[11px] font-black px-2 py-0.5 rounded-lg">
            Soal No. <span x-text="currentIndex + 1"></span> dari <span x-text="questions.length"></span>
          </span>
          <span class="text-[10px] sm:text-[11px] font-bold text-slate-400">Pilihan Ganda</span>
        </div>

        <div class="flex-1 min-h-0 overflow-y-auto pr-1">
          <p class="text-[14.5px] sm:text-[15.5px] font-semibold text-slate-900 leading-relaxed whitespace-pre-line" x-text="currentQuestion?.teks || ''"></p>

          <div class="space-y-2 mt-3">
            <template x-for="(teks, huruf) in (currentQuestion?.pilihan || {})" :key="huruf">
              <button type="button" @click="pilih(huruf)"
                      class="w-full px-3 py-2.5 rounded-xl border-2 text-left transition flex items-center gap-3 outline-none cursor-pointer"
                      :class="kelasOpsi(huruf)">
                <span class="w-7 h-7 rounded-lg font-bold text-[12px] flex items-center justify-center shrink-0 border"
                      :class="answers[currentQuestion.id] === huruf ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-slate-100 text-slate-700 border-slate-200'"
                      x-text="huruf"></span>
                <span class="text-[13.5px] font-semibold text-slate-800" x-text="teks"></span>
              </button>
            </template>
          </div>
        </div>

        <div class="shrink-0 pt-2 mt-2 border-t border-slate-100 flex justify-between items-center gap-2">
          <button type="button" @click="mundur()" :disabled="currentIndex === 0"
                  class="bg-slate-100 hover:bg-slate-200 disabled:opacity-40 text-slate-700 font-bold px-3 py-2 rounded-lg text-[11px] transition cursor-pointer">
            &larr; Sebelumnya
          </button>
          <button type="button" @click="maju()" :disabled="currentIndex === questions.length - 1"
                  class="bg-indigo-600 hover:bg-indigo-700 disabled:opacity-40 text-white font-bold px-4 py-2 rounded-lg text-[11px] transition cursor-pointer">
            Selanjutnya &rarr;
          </button>
        </div>
      </div>

      {{-- Layar hasil --}}
      <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm text-center flex-1 min-h-0 overflow-y-auto" x-show="isFinished" x-cloak>
        <div class="text-4xl mb-2">🏆</div>
        <h2 class="text-lg font-extrabold text-slate-900 mb-1">Pengerjaan Selesai!</h2>
        <p class="text-[12px] text-slate-500 mb-4">Jawaban Anda sudah direkam oleh sistem dan diteruskan ke panitia.</p>
        <div class="grid grid-cols-3 gap-2 max-w-md mx-auto mb-4">
          <div class="bg-slate-50 p-2.5 rounded-xl border border-slate-100">
            <span class="text-[9px] font-black text-slate-400 uppercase block">Skor</span>
            <span class="text-2xl font-black text-indigo-600" x-text="hasil.skor ?? '—'"></span>
          </div>
          <div class="bg-slate-50 p-2.5 rounded-xl border border-slate-100">
            <span class="text-[9px] font-black text-slate-400 uppercase block">Benar</span>
            <span class="text-2xl font-black text-emerald-600" x-text="(hasil.benar ?? '—') + '/' + (hasil.total ?? '—')"></span>
          </div>
          <div class="bg-slate-50 p-2.5 rounded-xl border border-slate-100">
            <span class="text-[9px] font-black text-slate-400 uppercase block">Pelanggaran</span>
            <span class="text-2xl font-black text-rose-600" x-text="hasil.pelanggaran ?? violations"></span>
          </div>
        </div>
        <a href="{{ route('spmb.pendaftar.tes') }}"
           class="inline-block bg-slate-900 hover:bg-black text-white font-extrabold py-2.5 px-5 rounded-xl text-[11px] uppercase tracking-wider">
          Lihat Hasil &amp; Kartu Tes
        </a>
      </div>
    </div>

    {{-- Navigasi nomor soal --}}
    <div class="lg:col-span-1 flex flex-col min-h-0">
      <div class="bg-white rounded-2xl p-2.5 sm:p-3 border border-slate-200 shadow-sm flex-1 min-h-0 flex flex-col">
        <h3 class="text-[10px] sm:text-xs font-black uppercase tracking-wider text-slate-400 mb-1.5 shrink-0">Nomor Soal</h3>
        <div class="grid grid-cols-8 sm:grid-cols-10 lg:grid-cols-4 gap-1 overflow-y-auto max-h-24 lg:max-h-none lg:flex-1 min-h-0 p-0.5">
          <template x-for="(q, idx) in questions" :key="q.id">
            <button type="button" @click="lompat(idx)"
                    class="w-full aspect-square rounded-lg text-[10px] font-bold transition flex items-center justify-center border cursor-pointer"
                    :class="kelasPalet(idx, q.id)"
                    x-text="idx + 1"></button>
          </template>
        </div>
        <div class="hidden lg:block mt-2 pt-2 border-t border-slate-100 space-y-0.5 text-[10px] text-slate-500 font-medium shrink-0">
          <div class="flex items-center gap-2"><span class="w-2.5 h-2.5 rounded bg-emerald-500"></span><span>Sudah dijawab</span></div>
          <div class="flex items-center gap-2"><span class="w-2.5 h-2.5 rounded bg-slate-200"></span><span>Belum dijawab</span></div>
          <div class="flex items-center gap-2"><span class="w-2.5 h-2.5 rounded border-2 border-indigo-600"></span><span>Sedang dikerjakan</span></div>
        </div>
      </div>
    </div>
  </main>

  <script>
    function cbtApp() {
      return {
        questions: @json($soal),
        answers: {},
        currentIndex: 0,
        sisaDetik: @json($sisaDetik),
        violations: 0,
        isFinished: false,
        hasil: {},
        timer: null,

        init() {
          this.timer = setInterval(() => {
            if (this.sisaDetik > 0) {
              this.sisaDetik--;
            } else {
              clearInterval(this.timer);
              this.kirim(true);
            }
          }, 1000);

          document.addEventListener('visibilitychange', () => {
            if (document.hidden && !this.isFinished) {
              this.violations++;
              alert('Peringatan! Anda terdeteksi berpindah tab/layar ujian. Pelanggaran dicatat panitia.');
            }
          });

          window.addEventListener('beforeunload', (e) => {
            if (!this.isFinished) {
              e.preventDefault();
              e.returnValue = '';
            }
          });
        },

        get currentQuestion() { return this.questions[this.currentIndex]; },

        formatTimer() {
          const m = Math.floor(this.sisaDetik / 60);
          const s = this.sisaDetik % 60;
          return `${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}`;
        },

        pilih(huruf) { this.answers[this.currentQuestion.id] = huruf; },
        mundur() { if (this.currentIndex > 0) this.currentIndex--; },
        maju() { if (this.currentIndex < this.questions.length - 1) this.currentIndex++; },
        lompat(idx) { this.currentIndex = idx; },

        kelasOpsi(huruf) {
          return this.answers[this.currentQuestion.id] === huruf
            ? 'border-indigo-600 bg-indigo-50'
            : 'border-slate-200 bg-white hover:border-indigo-300 hover:bg-indigo-50/40';
        },

        kelasPalet(idx, id) {
          if (idx === this.currentIndex) return 'border-indigo-600 text-indigo-700 bg-white';
          return this.answers[id] !== undefined
            ? 'bg-emerald-500 border-emerald-500 text-white'
            : 'bg-slate-200 border-slate-200 text-slate-600';
        },

        selesai() {
          const kosong = this.questions.length - Object.keys(this.answers).length;
          const pesan = kosong > 0
            ? `Masih ada ${kosong} soal belum dijawab. Kirim jawaban dan selesaikan tes sekarang?`
            : 'Kirim semua jawaban dan selesaikan tes sekarang?';
          if (confirm(pesan)) this.kirim(false);
        },

        async kirim(otomatis) {
          if (this.isFinished) return;
          clearInterval(this.timer);

          try {
            const res = await fetch('{{ route('spmb.pendaftar.tes.simpan') }}', {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
              },
              body: JSON.stringify({ jawaban: this.answers, pelanggaran: this.violations, otomatis }),
            });

            const data = await res.json().catch(() => ({}));

            if (res.ok || res.status === 409) {
              this.hasil = { skor: data.skor, benar: data.benar, total: data.total, pelanggaran: data.pelanggaran ?? this.violations };
              this.isFinished = true;
            } else if (res.status === 401) {
              alert('Sesi berakhir. Silakan masuk kembali ke Area Pendaftar.');
              window.location = '{{ route('spmb.pendaftar.masuk') }}';
            } else {
              alert('Gagal mengirim jawaban: ' + (data.galat || 'coba lagi.'));
              this.timer = setInterval(() => { if (this.sisaDetik > 0) this.sisaDetik--; }, 1000);
            }
          } catch (e) {
            alert('Koneksi bermasalah saat mengirim jawaban. Coba tekan tombol Selesai lagi.');
            this.timer = setInterval(() => { if (this.sisaDetik > 0) this.sisaDetik--; }, 1000);
          }
        },
      };
    }
  </script>
</body>
</html>
