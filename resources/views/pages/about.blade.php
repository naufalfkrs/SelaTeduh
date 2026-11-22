@extends('layouts.app')

@section('title', 'Tentang SelaTeduh — Ruang Jeda Digital')

@section('content')
<div class="container-page pt-10">

    <!-- Tentang -->
    <section id="tentang" class="grid grid-cols-1 items-center gap-10 lg:grid-cols-2">
        <div>
            <span class="eyebrow">Tentang SelaTeduh</span>
            <h1 class="heading-xl mt-3">Ruang jeda untuk bernapas dan mengenali rasa.</h1>
            <p class="mt-4 max-w-xl text-[15px] leading-relaxed text-muted">
                SelaTeduh adalah platform kesejahteraan mental mandiri bagi remaja dan anak muda. Di sini kamu bisa berlatih napas,
                menulis jurnal reflektif, mencatat mood, dan membaca bacaan yang menenangkan, tanpa skor, tanpa streak yang menekan,
                dan tanpa tuntutan untuk selalu baik-baik saja.
            </p>
            <div class="mt-6 flex flex-wrap gap-3">
                <a href="{{ route('home') }}#cek-perasaan" class="btn-primary">Mulai dari sini</a>
                <a href="{{ route('page.help') }}" class="btn-secondary">Panduan mencari bantuan</a>
            </div>
        </div>
        <div class="card-soft p-6 sm:p-8">
            <p class="eyebrow">Untuk siapa SelaTeduh dibuat?</p>
            <ul class="mt-4 space-y-4 text-sm leading-relaxed text-muted">
                <li class="flex gap-3">
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-white text-primary"><x-icon name="book-open" class="h-4 w-4" /></span>
                    <span><strong class="text-ink">Siswa &amp; mahasiswa (15–22 tahun)</strong> yang sedang lelah dengan tugas, ekspektasi, dan burnout belajar.</span>
                </li>
                <li class="flex gap-3">
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-white text-primary"><x-icon name="moon" class="h-4 w-4" /></span>
                    <span><strong class="text-ink">Pekerja muda (23–28 tahun)</strong> yang sering overthinking di malam hari dan butuh cara rileks yang sederhana.</span>
                </li>
                <li class="flex gap-3">
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-white text-primary"><x-icon name="heart-handshake" class="h-4 w-4" /></span>
                    <span><strong class="text-ink">Siapa pun yang sedang kewalahan</strong> dan butuh akses cepat ke layanan bantuan yang aman dan terpercaya.</span>
                </li>
            </ul>
        </div>
    </section>

    <!-- Filosofi -->
    <section id="filosofi" class="mt-20 scroll-mt-24">
        <span class="eyebrow">Filosofi Kami</span>
        <h2 class="heading-lg mt-2 max-w-2xl">Tak apa, ambil jeda.</h2>
        <p class="mt-3 max-w-2xl text-sm leading-relaxed text-muted">
            Kami percaya merawat diri tidak harus rumit atau sempurna. Langkah kecil yang dilakukan dengan lembut sudah cukup berarti.
        </p>
        <div class="mt-8 grid grid-cols-1 gap-4 md:grid-cols-3">
            @foreach ([
                ['icon' => 'heart', 'title' => 'Tanpa Penghakiman', 'desc' => 'Tidak ada emosi "baik" atau "buruk". Setiap rasa divalidasi sebagai hal yang manusiawi.'],
                ['icon' => 'lock', 'title' => 'Privasi Utama', 'desc' => 'Catatanmu milikmu. Bisa dipakai tanpa akun, dan bisa disimpan di peramban saja.'],
                ['icon' => 'shield-check', 'title' => 'Pendampingan Krisis', 'desc' => 'Akses cepat ke layanan bantuan profesional Indonesia seperti SEJIWA dan Yayasan Pulih.'],
            ] as $item)
                <div class="card p-6">
                    <span class="flex h-10 w-10 items-center justify-center rounded-full bg-secondary text-primary"><x-icon :name="$item['icon']" class="h-5 w-5" /></span>
                    <h3 class="mt-4 font-serif text-xl text-ink">{{ $item['title'] }}</h3>
                    <p class="mt-2 text-sm leading-relaxed text-muted">{{ $item['desc'] }}</p>
                </div>
            @endforeach
        </div>
    </section>

    <!-- Nilai Bersama -->
    <section id="nilai" class="mt-20 scroll-mt-24 rounded-[1.75rem] bg-secondary p-6 sm:p-10">
        <span class="eyebrow">Nilai Bersama</span>
        <h2 class="heading-lg mt-2 max-w-2xl">Yang kami jaga di setiap fitur</h2>
        <div class="mt-8 grid grid-cols-1 gap-x-10 gap-y-6 md:grid-cols-2">
            @foreach ([
                ['Tanpa skor & streak', 'Tidak ada angka, peringkat, atau target harian yang membuatmu merasa gagal saat sedang lelah.'],
                ['Tanpa gamifikasi yang memicu cemas', 'Tidak ada lencana atau hitungan beruntun. Kamu boleh datang kapan saja kamu siap.'],
                ['Tanpa profil emosi untuk iklan', 'Data perasaanmu tidak dijual dan tidak dipakai untuk iklan bertarget.'],
                ['Bahasa yang lembut', 'Setiap kalimat ditulis untuk menemani, bukan menilai atau mendiagnosis.'],
            ] as [$title, $desc])
                <div class="flex gap-3">
                    <x-icon name="leaf" class="mt-1 h-4 w-4 shrink-0 text-primary" />
                    <div>
                        <h3 class="font-semibold text-ink">{{ $title }}</h3>
                        <p class="mt-1 text-sm leading-relaxed text-muted">{{ $desc }}</p>
                    </div>
                </div>
            @endforeach
        </div>
        <p class="mt-8 text-xs leading-relaxed text-muted">
            Baca juga <a href="{{ route('page.privacy') }}" class="font-semibold text-primary underline underline-offset-2">Privasi &amp; Etika</a>
            untuk mengetahui bagaimana catatanmu disimpan.
        </p>
    </section>
</div>
@endsection
