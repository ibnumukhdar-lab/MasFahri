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
        <button type="button" @click="bukaKonfirmasi()" x-show="!isFinished"
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
        <div class="shrink-0 flex justify-between items-center gap-2 pb-2 mb-2 border-b border-slate-100">
          <span class="bg-indigo-50 text-indigo-700 text-[10px] sm:text-[11px] font-black px-2 py-0.5 rounded-lg">
            Soal No. <span x-text="currentIndex + 1"></span> dari <span x-text="questions.length"></span>
          </span>

          {{-- Tandai dulu (bendera) --}}
          <button type="button" @click="toggleTanda()"
                  class="flex items-center gap-1.5 px-2 py-1 rounded-lg text-[10px] sm:text-[11px] font-bold border transition cursor-pointer"
                  :class="ditandai(currentQuestion?.id) ? 'bg-amber-500 border-amber-500 text-white' : 'bg-white border-amber-300 text-amber-700 hover:bg-amber-50'">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
              <path d="M4 21V4h9l-1 3h8v9h-8l1 3H4z"></path>
            </svg>
            <span x-text="ditandai(currentQuestion?.id) ? 'Hapus tanda' : 'Tandai dulu'"></span>
          </button>
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

          <button type="button" @click="maju()" x-show="currentIndex < questions.length - 1"
                  class="bg-indigo-600 hover:bg-indigo-700 disabled:opacity-40 text-white font-bold px-4 py-2 rounded-lg text-[11px] transition cursor-pointer">
            Selanjutnya &rarr;
          </button>

          <button type="button" @click="bukaKonfirmasi()" x-show="currentIndex === questions.length - 1"
                  class="text-white font-bold px-4 py-2 rounded-lg text-[11px] transition cursor-pointer shadow-sm"
                  :class="semuaTerjawab ? 'bg-emerald-600 hover:bg-emerald-700' : 'bg-slate-900 hover:bg-black'">
            <span x-text="semuaTerjawab ? 'Selesaikan ✓' : 'Selesaikan'"></span>
          </button>
        </div>
      </div>

      {{-- Layar hasil --}}
      <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm text-center flex-1 min-h-0 overflow-y-auto" x-show="isFinished" x-cloak>
        <div class="text-4xl mb-2">✅</div>
        <h2 class="text-lg font-extrabold text-slate-900 mb-1">Pengerjaan Selesai!</h2>
        <p class="text-[12.5px] text-slate-600 mb-4 leading-relaxed">
          Jawaban Anda sudah direkam dan diteruskan ke panitia.<br>
          Nilai tidak ditampilkan di sini — hasil rekap dipegang panitia.
        </p>
        <div class="rounded-xl p-3.5 mb-4 text-left" style="background:#eef6f2;border:1px solid #cfe6da">
          <p class="text-[9px] font-black uppercase tracking-wide text-emerald-800 mb-0.5">Tahap berikutnya</p>
          <p class="text-[13px] font-bold text-slate-800">Tes Baca Tulis Al-Qur'an</p>
          <p class="text-[11.5px] text-slate-600">Jadwal ditetapkan panitia — pantau dasbor dan WhatsApp.</p>
        </div>
        <a href="{{ route('spmb.pendaftar.tes') }}"
           class="inline-block bg-slate-900 hover:bg-black text-white font-extrabold py-2.5 px-5 rounded-xl text-[11px] uppercase tracking-wider">
          Kembali ke Daftar Paket
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
                    class="w-full aspect-square rounded-lg text-[10px] font-bold transition flex items-center justify-center border cursor-pointer relative"
                    :class="kelasPalet(idx, q.id)">
              <span x-text="idx + 1"></span>
              <span x-show="ditandai(q.id)" class="absolute -top-0.5 -right-0.5 w-2 h-2 rounded-full bg-amber-800 border border-white"></span>
            </button>
          </template>
        </div>
        <div class="hidden lg:block mt-2 pt-2 border-t border-slate-100 space-y-0.5 text-[10px] text-slate-500 font-medium shrink-0">
          <div class="flex items-center gap-2"><span class="w-2.5 h-2.5 rounded bg-emerald-500"></span><span>Sudah dijawab</span></div>
          <div class="flex items-center gap-2"><span class="w-2.5 h-2.5 rounded bg-slate-200"></span><span>Belum dijawab</span></div>
          <div class="flex items-center gap-2"><span class="w-2.5 h-2.5 rounded bg-amber-500"></span><span>Ditandai (perlu dicek)</span></div>
          <div class="flex items-center gap-2"><span class="w-2.5 h-2.5 rounded border-2 border-indigo-600"></span><span>Sedang dikerjakan</span></div>
        </div>
      </div>
    </div>
  </main>

  {{-- ================= KONFIRMASI SELESAI ================= --}}
  <div x-show="konfirmasi" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4"
       style="background:rgba(15,23,42,.55)" @keydown.escape.window="tutupKonfirmasi()">
    <div class="bg-white rounded-2xl w-full max-w-md p-5 shadow-2xl max-h-[92dvh] overflow-y-auto" @click.outside="tutupKonfirmasi()">
      <div class="text-center">
        <p class="text-[10px] font-black uppercase tracking-wider text-slate-400">Konfirmasi</p>
        <h2 class="text-lg font-extrabold text-slate-900 mt-0.5">Selesaikan tes ini?</h2>
      </div>

      <div class="bg-rose-50 border border-rose-200 rounded-xl py-3 text-center mt-4">
        <p class="text-[10px] font-black uppercase tracking-wide text-rose-500">Sisa waktu</p>
        <p class="text-2xl font-black text-rose-700" x-text="formatTimer()"></p>
      </div>

      <div class="grid grid-cols-3 gap-2 mt-3 text-center">
        <div class="bg-slate-50 border border-slate-200 rounded-xl py-2">
          <p class="text-[9px] font-bold uppercase text-slate-400">Terjawab</p>
          <p class="text-base font-black text-emerald-600"><span x-text="jumlahTerjawab"></span>/<span x-text="questions.length"></span></p>
        </div>
        <div class="bg-slate-50 border border-slate-200 rounded-xl py-2">
          <p class="text-[9px] font-bold uppercase text-slate-400">Belum</p>
          <p class="text-base font-black" :class="jumlahKosong > 0 ? 'text-rose-600' : 'text-slate-400'" x-text="jumlahKosong"></p>
        </div>
        <div class="bg-slate-50 border border-slate-200 rounded-xl py-2">
          <p class="text-[9px] font-bold uppercase text-slate-400">Ditandai</p>
          <p class="text-base font-black" :class="jumlahTanda > 0 ? 'text-amber-600' : 'text-slate-400'" x-text="jumlahTanda"></p>
        </div>
      </div>

      <template x-if="jumlahKosong > 0">
        <p class="mt-3 text-[12px] leading-relaxed text-rose-700 bg-rose-50 border border-rose-200 rounded-lg px-3 py-2">
          Masih ada <b x-text="jumlahKosong"></b> soal belum dijawab. Anda masih bisa menutup jendela ini dan mengecek ulang.
        </p>
      </template>

      <template x-if="jumlahTanda > 0">
        <div class="mt-3 bg-amber-50 border border-amber-300 rounded-xl px-3 py-2.5">
          <p class="text-[12px] font-bold text-amber-900 mb-1.5">
            Masih ada <span x-text="jumlahTanda"></span> soal bertanda. Lepaskan tanda dulu bila sudah yakin.
          </p>
          <div class="flex flex-wrap gap-1 mb-2">
            <template x-for="n in daftarTanda" :key="n">
              <button type="button" @click="lompat(n.index); tutupKonfirmasi()"
                      class="w-6 h-6 rounded-md bg-amber-500 text-white text-[10px] font-bold flex items-center justify-center cursor-pointer"
                      x-text="n.nomor"></button>
            </template>
          </div>
          <button type="button" @click="lepasSemuaTanda()"
                  class="w-full bg-white border border-amber-400 text-amber-800 font-bold py-1.5 rounded-lg text-[11px] cursor-pointer hover:bg-amber-100">
            Lepas semua tanda
          </button>
        </div>
      </template>

      <p class="mt-3 text-[11px] leading-relaxed text-slate-500 text-center">
        Periksa kembali jawaban Anda. Setelah diselesaikan, jawaban tidak bisa diubah.
      </p>

      <div class="grid grid-cols-2 gap-2 mt-4">
        <button type="button" @click="tutupKonfirmasi()"
                class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold py-2.5 rounded-xl text-[12px] cursor-pointer">
          Cek ulang jawaban
        </button>
        <button type="button" @click="kirim(false)" :disabled="jumlahTanda > 0"
                class="text-white font-extrabold py-2.5 rounded-xl text-[12px] cursor-pointer disabled:opacity-40 disabled:cursor-not-allowed"
                :class="jumlahTanda > 0 ? 'bg-slate-400' : 'bg-emerald-600 hover:bg-emerald-700'">
          Yakin selesaikan
        </button>
      </div>
      <p x-show="jumlahTanda > 0" x-cloak class="mt-1.5 text-[10.5px] text-amber-700 text-center font-semibold">
        Tombol aktif setelah semua tanda dilepas.
      </p>
    </div>
  </div>

  <script>
    function cbtApp() {
      return {
        questions: @json($soal),
        answers: {},
        tanda: {},
        currentIndex: 0,
        sisaDetik: @json($sisaDetik),
        violations: 0,
        isFinished: false,
        konfirmasi: false,
        hasil: {},
        timer: null,

        init() {
          // tanda (bendera) disimpan per paket supaya tidak hilang saat halaman dimuat ulang
          try {
            this.tanda = JSON.parse(localStorage.getItem('cbt_tanda_{{ $paket->id }}') || '{}') || {};
          } catch (e) {
            this.tanda = {};
          }

          // jawaban juga disimpan sementara di perangkat, supaya tidak hilang bila halaman ter-refresh
          try {
            const simpan = JSON.parse(localStorage.getItem('cbt_jawab_{{ $paket->id }}') || '{}') || {};
            this.answers = Object.keys(simpan).length ? simpan : {};
          } catch (e) {
            this.answers = {};
          }

          this.timer = setInterval(() => {
            if (this.sisaDetik > 0) {
              this.sisaDetik--;
            } else {
              clearInterval(this.timer);
              this.konfirmasi = false;
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
        get jumlahTerjawab() { return Object.keys(this.answers).length; },
        get jumlahKosong() { return Math.max(0, this.questions.length - this.jumlahTerjawab); },
        get semuaTerjawab() { return this.jumlahKosong === 0; },
        get jumlahTanda() {
          return this.questions.filter(q => this.ditandai(q.id)).length;
        },
        get daftarTanda() {
          return this.questions
            .map((q, i) => ({ id: q.id, index: i, nomor: i + 1 }))
            .filter(x => this.ditandai(x.id));
        },

        formatTimer() {
          const m = Math.floor(this.sisaDetik / 60);
          const s = this.sisaDetik % 60;
          return `${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}`;
        },

        pilih(huruf) {
          this.answers[this.currentQuestion.id] = huruf;
          try { localStorage.setItem('cbt_jawab_{{ $paket->id }}', JSON.stringify(this.answers)); } catch (e) {}
        },
        mundur() { if (this.currentIndex > 0) this.currentIndex--; },
        maju() { if (this.currentIndex < this.questions.length - 1) this.currentIndex++; },
        lompat(idx) { this.currentIndex = idx; },

        // ---------- bendera / tandai dulu ----------
        ditandai(id) { return !!this.tanda[id]; },
        toggleTanda() {
          const id = this.currentQuestion.id;
          const baru = { ...this.tanda };
          if (baru[id]) { delete baru[id]; } else { baru[id] = true; }
          this.tanda = baru;
          this.simpanTanda();
        },
        lepasSemuaTanda() {
          this.tanda = {};
          this.simpanTanda();
        },
        simpanTanda() {
          try { localStorage.setItem('cbt_tanda_{{ $paket->id }}', JSON.stringify(this.tanda)); } catch (e) {}
        },

        kelasOpsi(huruf) {
          return this.answers[this.currentQuestion.id] === huruf
            ? 'border-indigo-600 bg-indigo-50'
            : 'border-slate-200 bg-white hover:border-indigo-300 hover:bg-indigo-50/40';
        },

        kelasPalet(idx, id) {
          const tanda = this.ditandai(id);
          if (idx === this.currentIndex) {
            return tanda
              ? 'border-amber-500 bg-amber-100 text-amber-800 ring-2 ring-amber-300'
              : 'border-indigo-600 text-indigo-700 bg-white';
          }
          if (tanda) return 'bg-amber-400 border-amber-500 text-amber-900';
          return this.answers[id] !== undefined
            ? 'bg-emerald-500 border-emerald-500 text-white'
            : 'bg-slate-200 border-slate-200 text-slate-600';
        },

        bukaKonfirmasi() { this.konfirmasi = true; },
        tutupKonfirmasi() { this.konfirmasi = false; },

        async kirim(otomatis) {
          if (this.isFinished) return;
          this.konfirmasi = false;
          clearInterval(this.timer);

          try {
            const res = await fetch('{{ route('spmb.pendaftar.tes.simpan') }}', {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
              },
              body: JSON.stringify({ jawaban: this.answers, pelanggaran: this.violations, otomatis, paket_id: {{ $paket->id }} }),
            });

            const data = await res.json().catch(() => ({}));

            if (res.ok || res.status === 409) {
              this.hasil = { pelanggaran: data.pelanggaran ?? this.violations };
              this.isFinished = true;
              try {
                localStorage.removeItem('cbt_tanda_{{ $paket->id }}');
                localStorage.removeItem('cbt_jawab_{{ $paket->id }}');
              } catch (e) {}
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
