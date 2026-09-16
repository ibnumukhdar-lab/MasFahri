# Situs SMA IT Arafah Boarding School — smaitarafah.sch.id

Situs resmi SMA Islam Terpadu Arafah Boarding School (Sampit, Kotawaringin Timur).
Sejak **16 September 2026** situs ini berjalan dengan **Laravel 12** menggantikan
WordPress. Kode WordPress lama tidak dihapus — diarsipkan di server
(`~/wordpress-arsip-smaita-20260916`, beserta database `u8151173_wp91`).

## Isi aplikasi

| Bagian | Keterangan |
|---|---|
| Halaman publik | beranda, tentang, visi & misi, kurikulum, tahfizh, diniyah, ekskul, galeri, berita (+kategori & pencarian), SPMB, pengumuman kelulusan, alumni, tools guru |
| Modul SPMB | formulir pendaftaran **menyimpan ke database** + nomor otomatis (`SPMB-2026-0001`) dan halaman cek status (di WordPress dulu hanya membuka WhatsApp tanpa arsip) |
| Modul kelulusan | cek NISN dari tabel sendiri (dulu menanyakan Google Apps Script/Sheet) |
| Akun | registrasi alumni, login, profil; verifikasi alumni oleh admin |
| Panel admin | Filament v5 di `/kelola` — postingan, halaman, kategori, galeri, pendaftaran SPMB, kelulusan (+impor CSV), tautan guru, pengguna |
| Alamat lama | 17 alamat WordPress yang tidak dipakai lagi dialihkan permanen (301) — daftar di `config/smaita.php` → `alihkan` |

## Teknologi

- Laravel 12.69 + Filament 5.8, PHP 8.2, Tailwind CDN + Palet navy `#1f3a5f`
- Basis data: SQLite (`database/database.sqlite`) — satu berkas, mudah dicadangkan
- Media: berkas nyata di `public/media/unggahan` (15 MB, 46 berkas)

## Menjalankan di komputer

```
D:\smaita-web\JALANKAN.cmd          # menyalakan http://localhost:8070 + alamat HP
```
Panel admin: http://localhost:8070/kelola

## Perintah penting

```bash
php artisan smaita:impor "path/smaita-export.json" --media   # impor konten WordPress (idempoten, by wp_id)
php artisan smaita:pengguna "path/smaita-export.json" --admin-email=...   # impor akun (sandi acak, sekali tampil)
php artisan smaita:media-lokal [--jalan]                     # pindahkan gambar lama ke berkas lokal
php artisan smaita:kelulusan "path/kelulusan.csv" --tahun=2025/2026 --jalan
php artisan filament:assets                                  # publikasikan aset panel (wajib setelah pasang/perbarui)
```

## Peta produksi (Hostinger akun u8151173, ssh `nizhom`)

- Aplikasi: `~/smaita-web` (di LUAR docroot), basis data SQLite di `~/smaita-web/database/database.sqlite`
- Docroot: `~/public_html/smaitarafah.sch.id` — berisi `index.php` pembungkus yang memanggil `~/smaita-web/public/index.php`, `.htaccess` (aset nyata dilayani langsung, sisanya ke Laravel), serta salinan nyata `css/`, `js/`, `fonts/`, `media/` (Hostinger tidak bisa menyajikan berkas dari luar docroot)
- Arsip WordPress: `~/wordpress-arsip-smaita-20260916` + database `u8151173_wp91` (UTUH — jangan dihapus sebelum yakin)
- Gambar lama untuk konten hasil impor diambil dari arsip dengan `php ~/salin-media-arsip.php`
- Cron deploy WordPress (`deploy-masfahri.sh`) sudah DIMATIKAN agar tidak menimpa docroot baru; cadangan crontab: `~/crontab.bak-20260916`

## Setelah mengubah kode

```bash
# dari PC
cd D:\smaita-web\app && tar czf /d/smaita-web/smaita-laravel.tgz --exclude=./vendor --exclude=./node_modules --exclude=./.env --exclude=./database/database.sqlite .
scp /d/smaita-web/smaita-laravel.tgz nizhom:'~/'
ssh nizhom 'cd ~/smaita-web && tar xzf ~/smaita-laravel.tgz && php artisan config:clear && php artisan view:clear'
# jika ada kelas/berkas baru atau dependensi baru:
ssh nizhom 'cd ~/smaita-web && composer install --no-dev --no-interaction && php artisan migrate --force && php artisan config:cache && php artisan view:cache && php artisan filament:assets'
# salin aset & media ke docroot bila berubah:
ssh nizhom 'cp -R ~/smaita-web/public/css ~/smaita-web/public/js ~/smaita-web/public/fonts ~/smaita-web/public/media ~/public_html/smaitarafah.sch.id/'
```

## Data yang masih perlu diisi

- **Data kelulusan** (ekspor Google Sheet → CSV → menu Kelulusan di panel, atau `smaita:kelulusan`)
- Sandi pengguna: akun guru/musyrif/alumni dibuat dengan sandi acak, daftar ada di `storage/app/sandi-sementara.txt` (server) — minta pengguna menggantinya
