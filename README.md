# SelaTeduh

> Tak apa, ambil jeda.

Ruang teduh digital bagi remaja dan anak muda untuk bernapas, mengenali rasa, dan merawat diri tanpa tuntutan sempurna. Dibangun dengan Laravel 13, Tailwind CSS v4, dan Alpine.js, mengikuti desain Figma SelaTeduh.

## Fitur

| Halaman | Rute | Keterangan |
| --- | --- | --- |
| Beranda | `/` | Hero, cek perasaan cepat, tiga pilihan jeda, ringkasan mood 7 hari, artikel pilihan, sumber bantuan |
| Jelajahi | `/jelajahi` | Perpustakaan rasa: pencarian, filter kategori, urutan, pilihan redaksi, detail artikel |
| Latihan Napas | `/latihan` | Panduan napas interaktif (4-7-8, 4-4-4, 4-4) dengan animasi lingkaran |
| Jurnal | `/jurnal` | Editor jurnal dengan pemantik, Layar Tenang, arsip, pencarian, unduh `.txt` (perlu login) |
| Mood | `/mood` | Catat mood + pemicu, ritme minggu ini, riwayat dengan edit/hapus (perlu login) |

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
# atur DB_* di .env (MySQL atau SQLite)
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
