# SelaTeduh

> Tak apa, ambil jeda.

Ruang teduh digital bagi remaja dan anak muda untuk bernapas, mengenali rasa, dan merawat diri tanpa tuntutan sempurna. Dibangun dengan Laravel 13, Tailwind CSS v4, dan Alpine.js, mengikuti desain Figma SelaTeduh.

## Fitur

| Halaman | Rute | Keterangan |
| --- | --- | --- |
| Beranda | `/` | Hero, cek perasaan cepat, tiga pilihan jeda, ringkasan mood 7 hari, artikel pilihan, sumber bantuan |
| Jelajahi | `/jelajahi` | Perpustakaan rasa: pencarian, filter kategori, urutan, pilihan redaksi, detail artikel |
| Latihan Napas | `/latihan` | Panduan napas interaktif (4-7-8, 4-4-4, 4-4) dengan animasi lingkaran |
| Jurnal | `/jurnal` | Editor jurnal dengan pemantik, Layar Tenang, auto-save draf tiap 5 detik (LocalStorage), arsip, pencarian, unduh `.txt` |
| Mood | `/mood` | Catat mood + pemicu + waktu (bisa disesuaikan), ritme minggu ini, narasi lembut otomatis, riwayat dengan edit/hapus |
| Tentang / Privasi / Bantuan | `/tentang`, `/privasi`, `/bantuan` | Halaman info dari footer: filosofi & nilai, keamanan data & batasan layanan, kontak darurat & teman sebaya |

## Tanpa login (mode tamu)

Sesuai PRD, **semua fitur bisa dipakai tanpa akun**:

- Setiap pengunjung mendapat token anonim di cookie terenkripsi (`AppHttpMiddlewareEnsureGuestToken`).
- Catatan tamu disimpan dengan `user_id = null` dan `guest_token`, sehingga hanya bisa dibuka dari peramban yang sama.
- Saat tamu **daftar/masuk**, catatan tamunya otomatis dipindahkan ke akun tersebut.
- Cek Perasaan di Beranda tersimpan tanpa muat ulang halaman, dengan opsi **"Simpan di peramban saja"** (murni LocalStorage, tidak dikirim ke server).
- Akun bersifat opsional, berguna agar catatan bisa dibuka lintas perangkat.

## Struktur (MVC Laravel)

- **Route**: `routes/web.php`
- **Controller**: `Home`, `Mood`, `Journal`, `Exercise`, `Resource`, `Auth`
- **Model**: `User`, `MoodLog`, `JournalEntry`, `BreathingLog`, `Article`
- **View**: Blade + Alpine.js untuk interaksi kecil di peramban (tanpa Livewire)

## Catatan terhadap PRD

- PRD menulis Laravel 11 / Livewire 3; proyek ini memakai **Laravel 13 + Alpine.js** (PRD mengizinkan "Livewire 3 / Alpine.js").
- Warna mengikuti frame *Design System* di Figma (#355442 dst.), yang sedikit berbeda dari palet contoh di PRD (#2D4030 dst.).
- Kolom `mood_logs.logged_date` tetap dipakai untuk kalender, ditambah `logged_at` (timestamp) sesuai PRD.

## Design tokens

Diambil dari frame *Design System* di Figma dan didefinisikan di `resources/css/app.css`:

- **Primary** `#355442` · **Secondary** `#E8EEE4` · **Tertiary** `#E9C5AD` · **Neutral** `#292F2A`
- Headline: **Newsreader** · Body & label: **Plus Jakarta Sans**
- Ikon garis ada di komponen `<x-icon name="..." />`

## Menjalankan secara lokal

```bash
composer install
cp .env.example .env
php artisan key:generate
# atur DB_* di .env (MySQL atau SQLite); zona waktu default Asia/Jakarta (APP_TIMEZONE)
php artisan migrate --seed   # termasuk ArticleSeeder & akun test@example.com / password
npm install
npm run build                # atau: npm run dev
php artisan serve
```

## Tes

```bash
php artisan test
```

## Catatan

SelaTeduh adalah kawan refleksi mandiri, bukan pengganti penanganan medis, diagnosis, atau terapi profesional. Dalam situasi krisis, hubungi layanan SEJIWA (119 ext. 8) atau fasilitas kesehatan terdekat.
