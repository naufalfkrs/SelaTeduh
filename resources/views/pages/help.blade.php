@extends('layouts.app')

@section('title', 'Panduan Mencari Bantuan — SelaTeduh')

@section('content')
<div class="container-page pt-10">

    <section class="max-w-3xl">
        <span class="eyebrow">Panduan Mencari Bantuan</span>
        <h1 class="heading-xl mt-3">Meminta bantuan adalah langkah yang berani.</h1>
        <p class="mt-4 text-[15px] leading-relaxed text-muted">
            Kamu tidak harus menunggu sampai semuanya terasa terlalu berat. Berikut pilihan bantuan yang bisa kamu hubungi, dari yang paling mendesak sampai teman untuk bercerita.
        </p>
    </section>

    <!-- Kontak Darurat -->
    <section id="darurat" class="mt-10 scroll-mt-24 rounded-[1.75rem] bg-primary p-6 text-white sm:p-10">
        <span class="text-[11px] font-bold uppercase tracking-[0.12em] text-white/70">Kontak Darurat</span>
        <h2 class="mt-2 font-serif text-3xl">Jika kamu atau temanmu dalam bahaya sekarang</h2>
        <p class="mt-2 max-w-2xl text-sm leading-relaxed text-white/80">
            Jangan sendirian. Hubungi salah satu layanan berikut, atau datangi IGD rumah sakit/puskesmas terdekat bersama orang yang kamu percaya.
        </p>
        <div class="mt-8 grid grid-cols-1 gap-4 md:grid-cols-3">
            <a href="tel:112" class="rounded-2xl bg-white/10 p-5 ring-1 ring-white/20 transition hover:bg-white/15">
                <x-icon name="phone" class="h-5 w-5" />
                <p class="mt-3 font-serif text-2xl">112</p>
                <p class="mt-1 text-xs leading-relaxed text-white/75">Layanan panggilan darurat nasional (gratis, 24 jam).</p>
            </a>
            <a href="tel:119" class="rounded-2xl bg-white/10 p-5 ring-1 ring-white/20 transition hover:bg-white/15">
                <x-icon name="phone" class="h-5 w-5" />
                <p class="mt-3 font-serif text-2xl">119 ext. 8</p>
                <p class="mt-1 text-xs leading-relaxed text-white/75">Layanan SEJIWA: konseling psikologis gratis untuk anak &amp; remaja.</p>
            </a>
            <div class="rounded-2xl bg-white/10 p-5 ring-1 ring-white/20">
                <x-icon name="heart-handshake" class="h-5 w-5" />
                <p class="mt-3 font-serif text-2xl">Fasilitas Kesehatan</p>
                <p class="mt-1 text-xs leading-relaxed text-white/75">IGD rumah sakit atau puskesmas terdekat untuk kondisi darurat medis.</p>
            </div>
        </div>
    </section>

    <!-- Langkah -->
    <section id="langkah" class="mt-14 scroll-mt-24">
        <span class="eyebrow">Langkah Sederhana</span>
        <h2 class="heading-lg mt-2">Bingung harus mulai dari mana?</h2>
        <ol class="mt-8 grid grid-cols-1 gap-4 md:grid-cols-4">
            @foreach ([
                ['Kenali rasamu', 'Coba sebutkan apa yang kamu rasakan. Kamu bisa memakai Catatan Mood untuk membantu.'],
                ['Tenangkan tubuh', 'Jika napas terasa sesak, coba Latihan Napas 4-4 selama satu sampai dua menit.'],
                ['Ceritakan ke orang tepercaya', 'Teman, keluarga, guru BK, atau dosen wali bisa menjadi tempat cerita pertama.'],
                ['Hubungi profesional', 'Jika rasa berat tidak kunjung reda, hubungi psikolog atau layanan di halaman ini.'],
            ] as $i => [$title, $desc])
                <li class="card p-5">
                    <span class="flex h-8 w-8 items-center justify-center rounded-full bg-secondary font-serif text-primary">{{ $i + 1 }}</span>
                    <h3 class="mt-3 font-semibold text-ink">{{ $title }}</h3>
                    <p class="mt-1 text-sm leading-relaxed text-muted">{{ $desc }}</p>
                </li>
            @endforeach
        </ol>
    </section>

    <!-- Teman Sebaya -->
    <section id="sebaya" class="mt-14 scroll-mt-24 grid grid-cols-1 items-center gap-6 rounded-[1.75rem] bg-tertiary-soft p-6 sm:p-10 md:grid-cols-2">
        <div>
            <span class="eyebrow">Teman Sebaya</span>
            <h2 class="heading-lg mt-2">Bercerita kepada yang seumuran</h2>
            <p class="mt-3 text-sm leading-relaxed text-muted">
                Konselor sebaya adalah teman seusiamu yang sudah dilatih untuk mendengarkan tanpa menghakimi. Banyak sekolah dan kampus memiliki program ini.
            </p>
        </div>
        <ul class="space-y-3 text-sm leading-relaxed text-muted">
            <li class="flex gap-2"><x-icon name="check" class="mt-0.5 h-4 w-4 shrink-0 text-tertiary-dark" /> Tanyakan ke guru BK tentang program konselor sebaya di sekolahmu.</li>
            <li class="flex gap-2"><x-icon name="check" class="mt-0.5 h-4 w-4 shrink-0 text-tertiary-dark" /> Mahasiswa bisa menghubungi unit layanan konseling kampus.</li>
            <li class="flex gap-2"><x-icon name="check" class="mt-0.5 h-4 w-4 shrink-0 text-tertiary-dark" /> Konselor sebaya akan membantu merujukmu ke profesional bila diperlukan.</li>
        </ul>
    </section>

    <!-- Layanan pendampingan -->
    <section class="mt-14">
        @include('resources.partials.help-banner')
    </section>
</div>
@endsection
