@extends('layouts.app')

@section('title', 'Jelajahi — Perpustakaan Rasa SelaTeduh')

@section('content')
@php
    $showFeatured = $featuredArticle && ! $search && $category === 'Semua Bacaan';
    $sortOptions = ['terhangat' => 'Rekomendasi Terhangat', 'terbaru' => 'Terbaru', 'tercepat' => 'Bacaan Tercepat'];
    $listed = $articles->values();
@endphp

<div class="container-page pt-10">
    <!-- Judul -->
    <div class="flex flex-col justify-between gap-6 lg:flex-row lg:items-end">
        <div class="max-w-2xl">
            <span class="chip bg-white text-muted ring-1 ring-line">
                <span class="h-1.5 w-1.5 rounded-full bg-primary"></span>
                Perpustakaan Rasa &amp; Edukasi Mandiri
            </span>
            <h1 class="heading-xl mt-5">Memahami diri, satu langkah demi satu.</h1>
            <p class="mt-4 max-w-xl text-[15px] leading-relaxed text-muted">
                Kumpulan panduan jernih dan catatan penuh empati untuk membantumu mengenali perasaan, meredakan riuh di kepala, dan menemukan cara merawat diri tanpa tuntutan sempurna.
            </p>
        </div>
        <div class="flex max-w-xs items-start gap-3 rounded-2xl bg-secondary p-4">
            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-white text-primary"><x-icon name="shield-check" class="h-4 w-4" /></span>
            <div class="text-xs leading-relaxed text-muted">
                <p class="font-semibold text-ink">Tinjauan Praktisi Terpercaya</p>
                Disusun bersama psikolog anak &amp; remaja • Bebas jargon medis yang membingungkan.
            </div>
        </div>
    </div>

    <!-- Pencarian & Urutan -->
    <form method="GET" action="{{ route('resource.index') }}" x-data class="mt-10">
        <input type="hidden" name="kategori" value="{{ $category }}">
        <div class="flex flex-col gap-3 md:flex-row md:items-center">
            <label class="relative block flex-1">
                <span class="sr-only">Cari bacaan</span>
                <x-icon name="search" class="pointer-events-none absolute top-1/2 left-4 h-4 w-4 -translate-y-1/2 text-subtle" />
                <input type="search" name="q" value="{{ $search }}" x-ref="search"
                       x-on:keydown.window.slash="if (document.activeElement.tagName !== 'INPUT' && document.activeElement.tagName !== 'TEXTAREA') { $event.preventDefault(); $refs.search.focus(); }"
                       placeholder="Cari topik, rasa, atau kata kunci (misal: 'sulit tidur', 'ujian', 'teman')..."
                       class="w-full rounded-full border border-line bg-white py-3 pr-12 pl-11 text-sm text-ink placeholder:text-subtle focus:border-primary focus:outline-none">
                <kbd class="absolute top-1/2 right-4 hidden -translate-y-1/2 rounded-md bg-canvas px-1.5 py-0.5 text-[10px] text-subtle ring-1 ring-line sm:block">/</kbd>
            </label>
            <label class="flex items-center gap-2 text-xs text-muted">
                Urutkan:
                <select name="urutkan" x-on:change="$el.form.submit()"
                        class="cursor-pointer rounded-full border border-line bg-white px-4 py-2.5 text-xs font-semibold text-ink focus:border-primary focus:outline-none">
                    @foreach ($sortOptions as $value => $label)
                        <option value="{{ $value }}" @selected($sort === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </label>
        </div>
    </form>

    <!-- Tab Kategori -->
    <div class="mt-5 flex flex-wrap items-center justify-between gap-3">
        <div class="flex flex-wrap gap-2">
            @foreach ($categories as $cat)
                <a href="{{ route('resource.index', array_filter(['kategori' => $cat === 'Semua Bacaan' ? null : $cat, 'q' => $search, 'urutkan' => $sort !== 'terhangat' ? $sort : null])) }}"
                   class="pill-tab {{ $category === $cat ? 'pill-tab-active' : 'pill-tab-idle' }}">
                    {{ $cat }}
                    <span class="{{ $category === $cat ? 'text-white/70' : 'text-subtle' }}">({{ $categoryCounts[$cat] ?? 0 }})</span>
                </a>
            @endforeach
        </div>
        <span class="flex items-center gap-1.5 text-xs text-subtle"><span class="h-1.5 w-1.5 rounded-full bg-emerald-600"></span> Diperbarui mingguan</span>
    </div>

    <!-- Pilihan Redaksi -->
    @if ($showFeatured)
        <article class="mt-8 grid grid-cols-1 items-center gap-8 rounded-[1.75rem] bg-secondary p-6 sm:p-8 lg:grid-cols-12">
            <div class="lg:col-span-7">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="chip-peach text-[11px] font-semibold uppercase tracking-wide">
                        <x-icon name="sparkles" class="h-3 w-3" /> Pilihan Redaksi Hari Ini • {{ $featuredArticle->read_time }} menit baca
                    </span>
                    <span class="text-xs text-muted">{{ $featuredArticle->category }}</span>
                </div>
                <h2 class="mt-4 font-serif text-3xl leading-tight text-ink sm:text-[38px]">
                    <a href="{{ route('resource.show', $featuredArticle->slug) }}" class="hover:text-primary">{{ $featuredArticle->title }}</a>
                </h2>
                <p class="mt-4 text-sm leading-relaxed text-muted">{{ $featuredArticle->summary }}</p>

                <div class="mt-6 flex flex-wrap items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <span class="flex h-9 w-9 items-center justify-center rounded-full bg-white text-primary"><x-icon name="heart" class="h-4 w-4" /></span>
                        <div class="text-xs">
                            <p class="font-semibold text-ink">{{ $featuredArticle->reviewer ?? 'Ditinjau Tim SelaTeduh' }}</p>
                            <p class="text-subtle">Disusun dengan empati untuk usia 15–24 tahun</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('resource.show', $featuredArticle->slug) }}" class="btn-primary">Baca Selengkapnya <x-icon name="arrow-right" class="h-3.5 w-3.5" /></a>
                        <a href="{{ route('resource.show', $featuredArticle->slug) }}" class="flex h-10 w-10 items-center justify-center rounded-full bg-white text-primary ring-1 ring-line hover:bg-canvas" aria-label="Simpan bacaan">
                            <x-icon name="bookmark" class="h-4 w-4" />
                        </a>
                    </div>
                </div>
            </div>
            <figure class="relative lg:col-span-5">
                <img src="{{ $featuredArticle->image_path ?: 'https://images.unsplash.com/photo-1513694203232-719a280e022f?auto=format&fit=crop&w=900&q=80' }}"
                     alt="Ilustrasi untuk {{ $featuredArticle->title }}"
                     class="aspect-[4/3] w-full rounded-2xl object-cover">
                <figcaption class="chip absolute bottom-3 left-3 bg-white/90 text-[11px] text-muted backdrop-blur">
                    <x-icon name="moon" class="h-3 w-3" /> Refleksi Malam Hari
                </figcaption>
            </figure>
        </article>
    @endif

    <!-- Jelajahi Topik Lainnya -->
    <section class="mt-12">
        <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
            <h2 class="flex items-center gap-2 font-serif text-2xl text-ink">
                {{ $search ? 'Hasil pencarian "'.$search.'"' : ($showFeatured ? 'Jelajahi Topik Lainnya' : $category) }}
                <span class="chip-soft py-0.5 font-sans text-[11px]">{{ $listed->count() }} Catatan</span>
            </h2>
            <span class="flex items-center gap-1.5 text-xs text-subtle"><x-icon name="clock" class="h-3.5 w-3.5" /> Rata-rata 3–6 menit baca</span>
        </div>

        @if ($listed->isEmpty())
            <div class="card p-10 text-center">
                <p class="font-serif text-lg text-ink">Belum ada bacaan yang cocok.</p>
                <p class="mt-1 text-xs text-muted">Coba kata kunci lain atau kembali ke <a href="{{ route('resource.index') }}" class="font-semibold text-primary underline">semua bacaan</a>.</p>
            </div>
        @else
            @php $lead = $listed->first(); $rest = $listed->slice(1); @endphp

            <!-- Kartu utama dengan gambar -->
            <article class="card grid grid-cols-1 gap-5 p-4 sm:grid-cols-12 sm:p-5">
                <div class="relative sm:col-span-4">
                    <img src="{{ $lead->image_path ?: 'https://images.unsplash.com/photo-1456513080510-7bf3a84b82f8?auto=format&fit=crop&w=700&q=80' }}"
                         alt="Ilustrasi untuk {{ $lead->title }}" class="aspect-[4/3] h-full w-full rounded-2xl object-cover">
                    <span class="chip absolute top-3 left-3 bg-white/90 text-[11px] text-ink">{{ $lead->category }}</span>
                </div>
                <div class="flex flex-col sm:col-span-8">
                    <p class="text-[11px] text-subtle">{{ $lead->reviewer ?? 'Ditinjau Tim SelaTeduh' }} • <x-icon name="clock" class="inline h-3 w-3" /> {{ $lead->read_time }} menit baca</p>
                    <h3 class="mt-1.5 font-serif text-2xl leading-snug text-ink">
                        <a href="{{ route('resource.show', $lead->slug) }}" class="hover:text-primary">{{ $lead->title }}</a>
                    </h3>
                    <p class="mt-2 mb-4 grow text-[13px] leading-relaxed text-muted">{{ $lead->summary }}</p>
                    <div class="flex items-center justify-between gap-3 border-t border-line pt-3">
                        <span class="chip bg-canvas py-0.5 text-[11px] text-muted ring-1 ring-line">#{{ Str::studly($lead->category) }}</span>
                        <a href="{{ route('resource.show', $lead->slug) }}" class="link-arrow text-xs"><x-icon name="book-open" class="h-3.5 w-3.5" /> Baca ringkas <x-icon name="arrow-right" class="h-3 w-3" /></a>
                    </div>
                </div>
            </article>

            <div class="mt-5 grid grid-cols-1 gap-5 md:grid-cols-2">
                @foreach ($rest as $item)
                    <article class="card flex flex-col p-5">
                        <div class="flex items-center justify-between gap-2 text-[11px]">
                            <span class="{{ $loop->index % 3 === 1 ? 'chip-peach' : 'chip-soft' }} py-0.5 text-[11px]">{{ $item->category }}</span>
                            <span class="flex items-center gap-1 text-subtle"><x-icon name="clock" class="h-3 w-3" /> {{ $item->read_time }} menit</span>
                        </div>
                        <h3 class="mt-3 font-serif text-xl leading-snug text-ink">
                            <a href="{{ route('resource.show', $item->slug) }}" class="hover:text-primary">{{ $item->title }}</a>
                        </h3>
                        <p class="mt-2 mb-4 line-clamp-3 grow text-[13px] leading-relaxed text-muted">{{ $item->summary }}</p>
                        <p class="mb-3 flex items-center gap-1.5 text-[11px] text-subtle"><span class="h-1 w-1 rounded-full bg-subtle"></span> {{ $item->reviewer ?? 'Ditinjau Tim SelaTeduh' }}</p>
                        <div class="flex items-center justify-between gap-3 border-t border-line pt-3">
                            <span class="chip bg-canvas py-0.5 text-[11px] text-muted ring-1 ring-line">#{{ Str::studly($item->category) }}</span>
                            <a href="{{ route('resource.show', $item->slug) }}" class="link-arrow text-xs">Baca Catatan <x-icon name="arrow-right" class="h-3 w-3" /></a>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </section>

    <!-- Ruang Pendampingan Nyata -->
    <section id="bantuan" class="mt-14 scroll-mt-24 overflow-hidden rounded-[1.75rem] bg-secondary p-6 sm:p-10">
        <div class="grid grid-cols-1 items-center gap-8 lg:grid-cols-12">
            <div class="lg:col-span-5">
                <span class="chip bg-white text-primary text-[11px] font-semibold uppercase tracking-wide">
                    <x-icon name="heart-handshake" class="h-3.5 w-3.5" /> Ruang Pendampingan Nyata
                </span>
                <h2 class="heading-lg mt-4">Merasa bacaan ini belum cukup membantumu?</h2>
                <p class="mt-3 text-sm leading-relaxed text-muted">
                    Tak apa jika tulisan dan artikel belum bisa meredakan bebanmu sepenuhnya. Terkadang, kita butuh teman bicara yang nyata dan berlisensi. Akses bantuan resmi di Indonesia selalu terbuka dan siap mendengarkan tanpa menghakimi.
                </p>
            </div>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:col-span-7">
                <div class="flex flex-col rounded-2xl bg-white p-5">
                    <span class="text-[10px] font-bold uppercase tracking-[0.12em] text-subtle">Layanan Bebas Pulsa</span>
                    <h3 class="mt-2 font-serif text-xl text-ink">SEJIWA (KemenPPPA)</h3>
                    <p class="mt-1.5 mb-4 grow text-xs leading-relaxed text-muted">Saluran pendampingan resmi ramah anak &amp; remaja Indonesia.</p>
                    <a href="tel:119" class="btn-primary w-full"><x-icon name="phone" class="h-3.5 w-3.5" /> 119 ekstensi 8</a>
                </div>
                <div class="flex flex-col rounded-2xl bg-white p-5">
                    <span class="text-[10px] font-bold uppercase tracking-[0.12em] text-subtle">Konseling Terjangkau</span>
                    <h3 class="mt-2 font-serif text-xl text-ink">Yayasan Pulih</h3>
                    <p class="mt-1.5 mb-4 grow text-xs leading-relaxed text-muted">Bimbingan psikologis profesional dengan biaya yang bersahabat.</p>
                    <a href="https://yayasanpulih.org" target="_blank" rel="noopener" class="btn-secondary w-full"><x-icon name="external" class="h-3.5 w-3.5" /> Kunjungi Layanan</a>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
