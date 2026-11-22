@extends('layouts.app')

@section('title', 'Ruang Ceritamu — Jurnal Refleksi SelaTeduh')

@section('content')
<div x-data="{
        quietMode: false,
        title: @js(old('title', '')),
        content: @js(old('content', '')),
        get wordCount() {
            const trimmed = this.content.trim();
            return trimmed === '' ? 0 : trimmed.split(/\s+/).length;
        },
        draftKey: 'selateduh:jurnal-draf',
        draftSavedAt: null,
        submitting: false,
        init() {
            // Pulihkan draf dari LocalStorage jika editor masih kosong
            try {
                const draft = JSON.parse(localStorage.getItem(this.draftKey) || 'null');
                if (draft && this.content === '' && this.title === '') {
                    this.title = draft.title || '';
                    this.content = draft.content || '';
                    this.draftSavedAt = draft.savedAt || null;
                }
            } catch (e) {}

            // Auto-save draf setiap 5 detik (PRD Modul 3)
            setInterval(() => this.saveDraft(), 5000);

            // Layar Tenang menyembunyikan header & footer situs
            this.$watch('quietMode', on => document.body.classList.toggle('quiet-mode', on));
        },
        saveDraft() {
            if (this.submitting) return;
            try {
                if (this.title.trim() === '' && this.content.trim() === '') {
                    localStorage.removeItem(this.draftKey);
                    this.draftSavedAt = null;
                    return;
                }
                const savedAt = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
                localStorage.setItem(this.draftKey, JSON.stringify({ title: this.title, content: this.content, savedAt }));
                this.draftSavedAt = savedAt;
            } catch (e) {}
        },
        clearDraft() {
            try { localStorage.removeItem(this.draftKey); } catch (e) {}
        },
        clearEditor() {
            if (this.content.trim() === '' || confirm('Kosongkan tulisan yang sedang diketik?')) {
                this.title = '';
                this.content = '';
                this.clearDraft();
                this.draftSavedAt = null;
            }
        }
     }"
     class="container-page pt-8">

    <!-- Top Bar -->
    <div x-show="!quietMode" class="flex flex-wrap items-center justify-between gap-3 text-xs text-muted">
        <a href="{{ route('mood.index') }}" class="inline-flex items-center gap-1.5 font-medium transition hover:text-primary">
            <x-icon name="arrow-left" class="h-3.5 w-3.5" /> Kembali ke Latihan &amp; Aktivitas
        </a>
        <span class="chip bg-white text-muted ring-1 ring-line">
            <span class="h-1.5 w-1.5 rounded-full bg-primary"></span>
            @auth Ruang Privat • Tersimpan di Akunmu @else Mode Tamu • Tersimpan untuk Peramban Ini @endauth
        </span>
    </div>

    <!-- Judul -->
    <div x-show="!quietMode" class="mt-8 mb-8">
        <span class="eyebrow">Jurnal Reflektif Harian</span>
        <h1 class="heading-xl mt-2">Ruang ceritamu.</h1>
        <p class="mt-2 max-w-xl font-serif text-lg italic text-muted">
            Nggak perlu merangkai kata dengan sempurna. Mulai dari apa yang kamu rasakan saat ini.
        </p>
    </div>

    <div x-show="!quietMode">@include('partials.guest-note')</div>

    <div class="grid grid-cols-1 items-start gap-6 lg:grid-cols-12">
        <!-- Kolom Kiri -->
        <div x-bind:class="quietMode ? 'lg:col-span-10 lg:col-start-2 pt-4' : 'lg:col-span-8'" class="space-y-5">

            @if ($selectedEntry)
                <!-- Mode Baca Lembar Terdahulu -->
                <article class="card p-6 sm:p-8">
                    <div class="flex flex-wrap items-center justify-between gap-2 border-b border-line pb-4">
                        <div class="flex flex-wrap items-center gap-2 text-xs text-muted">
                            <span class="chip-soft">Arsip Lembar</span>
                            <span>{{ $selectedEntry->created_at->locale('id')->isoFormat('dddd, D MMMM YYYY • HH:mm') }} WIB</span>
                        </div>
                        <a href="{{ route('journal.index', request()->except('baca')) }}" class="link-arrow text-xs">
                            <x-icon name="pen" class="h-3.5 w-3.5" /> Tulis Lembar Baru
                        </a>
                    </div>

                    @if ($selectedEntry->prompt)
                        <div class="mt-4 rounded-2xl bg-tertiary-soft p-4 text-sm text-ink">
                            <span class="mb-1 block text-[10px] font-bold uppercase tracking-[0.12em] text-tertiary-dark">Pemantik saat itu</span>
                            <span class="font-serif italic">"{{ $selectedEntry->prompt }}"</span>
                        </div>
                    @endif

                    <h2 class="heading-md mt-5">{{ $selectedEntry->title ?: 'Tanpa Judul' }}</h2>
                    <div class="mt-4 whitespace-pre-line text-[15px] leading-relaxed text-ink">{{ $selectedEntry->content }}</div>

                    <div class="mt-6 flex items-center justify-between border-t border-line pt-4 text-xs text-subtle">
                        <span>{{ $selectedEntry->word_count }} kata</span>
                        <form method="POST" action="{{ route('journal.destroy', $selectedEntry) }}" onsubmit="return confirm('Hapus lembar jurnal ini secara permanen?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex cursor-pointer items-center gap-1 font-medium text-danger hover:underline">
                                <x-icon name="trash" class="h-3.5 w-3.5" /> Hapus Lembar Ini
                            </button>
                        </form>
                    </div>
                </article>
            @else
                <!-- Pemantik Hari Ini -->
                @if ($currentPrompt)
                    <div x-show="!quietMode" class="rounded-[var(--radius-card)] bg-tertiary-soft p-5 sm:p-6">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div class="flex items-start gap-3">
                                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-white text-tertiary-dark"><x-icon name="leaf" class="h-4 w-4" /></span>
                                <div>
                                    <span class="text-[10px] font-bold uppercase tracking-[0.12em] text-tertiary-dark">Pemantik Hari Ini <span class="font-medium normal-case tracking-normal">• Mengundang jeda</span></span>
                                    <p class="mt-1 font-serif text-xl leading-snug text-ink">"{{ $currentPrompt }}"</p>
                                </div>
                            </div>
                            <a href="{{ route('journal.index') }}" class="chip shrink-0 bg-white text-primary ring-1 ring-line hover:bg-canvas">
                                <x-icon name="refresh" class="h-3.5 w-3.5" /> Ganti Pemantik
                            </a>
                        </div>
                        <div class="mt-4 flex flex-wrap items-center justify-between gap-2 border-t border-tertiary/40 pt-3 text-xs text-muted">
                            <span>Tema pemantik berganti lembut sesuai ritmemu.</span>
                            <a href="{{ route('journal.index', ['bebas' => 1]) }}" class="inline-flex items-center gap-1 font-semibold text-tertiary-dark underline underline-offset-2">
                                Tulis Bebas Tanpa Pemantik <x-icon name="arrow-up-right" class="h-3 w-3" />
                            </a>
                        </div>
                    </div>
                @else
                    <div x-show="!quietMode" class="flex flex-wrap items-center justify-between gap-2 rounded-2xl bg-white px-4 py-3 text-xs text-muted ring-1 ring-line">
                        <span>Mode menulis bebas aktif (tanpa pertanyaan pemantik).</span>
                        <a href="{{ route('journal.index') }}" class="link-arrow text-xs">Tampilkan Pemantik <x-icon name="arrow-right" class="h-3 w-3" /></a>
                    </div>
                @endif

                <!-- Editor -->
                <div class="card p-5 sm:p-7">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <div class="flex flex-wrap items-center gap-2 text-xs">
                            <span class="chip bg-canvas text-muted ring-1 ring-line">
                                <x-icon name="calendar" class="h-3.5 w-3.5" /> Hari ini, {{ now()->locale('id')->isoFormat('D MMMM YYYY') }}
                            </span>
                            @if ($todayMood)
                                <span class="chip-soft">
                                    <span class="h-1.5 w-1.5 rounded-full bg-primary"></span> Rasa: {{ $todayMood->mood }}
                                </span>
                            @endif
                        </div>
                        <button type="button" x-on:click="quietMode = !quietMode" class="chip cursor-pointer text-muted hover:text-ink">
                            <x-icon name="maximize" class="h-3.5 w-3.5" x-show="!quietMode" />
                            <x-icon name="minimize" class="h-3.5 w-3.5" x-show="quietMode" x-cloak />
                            <span x-text="quietMode ? 'Keluar Layar Tenang' : 'Layar Tenang'">Layar Tenang</span>
                        </button>
                    </div>

                    <form method="POST" action="{{ route('journal.store') }}" class="mt-5" x-on:submit="submitting = true; clearDraft()">
                        @csrf
                        <input type="hidden" name="prompt" value="{{ $currentPrompt }}">

                        <input type="text" name="title" x-model="title" maxlength="150"
                               placeholder="Beri judul jika mau, atau lewati saja..."
                               class="w-full border-0 bg-transparent pb-3 font-serif text-2xl text-ink placeholder:text-subtle/80 focus:outline-none">
                        @error('title') <p class="text-xs text-danger">{{ $message }}</p> @enderror

                        <div class="my-2 border-t border-dashed border-line"></div>

                        <textarea name="content" rows="12" x-model="content" required
                                  placeholder="Apa yang sedang singgah di dadamu? Tidak ada kalimat yang keliru di sini..."
                                  class="w-full resize-y border-0 bg-transparent pt-2 text-[15px] leading-relaxed text-ink placeholder:text-subtle/80 focus:outline-none"></textarea>
                        @error('content') <p class="text-xs text-danger">{{ $message }}</p> @enderror

                        <div class="mt-3 flex flex-wrap items-center justify-between gap-2 text-[11px] text-subtle">
                            <span class="flex items-center gap-1.5"><span class="h-1.5 w-1.5 rounded-full bg-emerald-600"></span> Ruang privat aktif — hanya kamu yang bisa membacanya</span>
                            <span><span x-text="wordCount">0</span> kata • <span x-text="draftSavedAt ? 'draf tersimpan ' + draftSavedAt : 'draf tersimpan otomatis'">draf tersimpan otomatis</span></span>
                        </div>

                        <div class="mt-4 flex flex-wrap items-center justify-between gap-3 border-t border-line pt-4">
                            <div class="flex flex-wrap items-center gap-2">
                                <button type="submit" class="btn-primary"><x-icon name="bookmark" class="h-3.5 w-3.5" /> Simpan Entri</button>
                                <button type="button" x-on:click="clearEditor()" class="btn-secondary">Kosongkan Halaman</button>
                            </div>
                            <span class="flex items-center gap-1.5 text-xs text-subtle"><x-icon name="lock" class="h-3.5 w-3.5" /> Tanpa iklan, tanpa analitik emosi</span>
                        </div>
                    </form>
                </div>
            @endif

            <!-- Jaminan Privasi -->
            <div id="privasi" x-show="!quietMode" class="flex scroll-mt-24 items-start gap-3 rounded-[var(--radius-card)] bg-secondary p-5">
                <x-icon name="shield-check" class="mt-0.5 h-5 w-5 shrink-0 text-primary" />
                <div class="text-[13px] leading-relaxed text-muted">
                    <p class="font-semibold text-ink">Jaminan Keheningan &amp; Privasi Penuh</p>
                    @auth Tulisanmu tersimpan khusus di ruang akun pribadimu. @else Tulisanmu tersimpan sebagai catatan tamu yang hanya bisa dibuka dari peramban ini. Draf yang sedang kamu ketik juga disimpan otomatis di peramban (LocalStorage). @endauth SelaTeduh tidak membuat profil analitik emosi dari kata-katamu dan tidak pernah membagikannya kepada siapa pun. Ceritamu sepenuhnya milikmu.
                </div>
            </div>

            <!-- Banner Jeda Sejenak -->
            <div x-show="!quietMode" class="flex flex-col gap-6 overflow-hidden rounded-[1.5rem] bg-primary p-6 text-white sm:flex-row sm:items-center sm:p-8">
                <div class="flex-1">
                    <span class="text-[10px] font-bold uppercase tracking-[0.16em] text-white/60">Jeda Sejenak</span>
                    <h3 class="mt-2 font-serif text-3xl leading-tight">Setelah bercerita, istirahatkan jemarimu.</h3>
                    <p class="mt-2 max-w-md text-[13px] leading-relaxed text-white/75">
                        Jika dadamu terasa sedikit lebih longgar, biarkan tubuhmu bersandar. Tarik napas tiga kali sebelum kembali menyapa dunia luar.
                    </p>
                    <a href="{{ route('exercise.index') }}" class="btn mt-5 bg-white px-5 text-primary hover:bg-secondary">
                        Tarik Napas Sejenak <x-icon name="arrow-right" class="h-3.5 w-3.5" />
                    </a>
                </div>
                <img src="https://images.unsplash.com/photo-1456513080510-7bf3a84b82f8?auto=format&fit=crop&w=600&q=80"
                     alt="Buku terbuka di atas meja yang tenang"
                     class="h-40 w-full rounded-2xl object-cover sm:h-36 sm:w-56">
            </div>
        </div>

        <!-- Sidebar: Lembar Terdahulu -->
        <aside x-show="!quietMode" class="space-y-5 lg:col-span-4">
            <div class="card p-5">
                <div class="flex items-start justify-between gap-2">
                    <div>
                        <h2 class="font-serif text-xl text-ink">Lembar Terdahulu</h2>
                        <p class="text-xs text-muted">Jejak jeda yang pernah kamu tulis</p>
                    </div>
                    <span class="chip-soft shrink-0">{{ $totalEntries }} catatan</span>
                </div>

                <form method="GET" action="{{ route('journal.index') }}" class="mt-4">
                    @if ($filter !== 'semua') <input type="hidden" name="filter" value="{{ $filter }}"> @endif
                    <label class="relative block">
                        <span class="sr-only">Cari lembar jurnal</span>
                        <x-icon name="search" class="pointer-events-none absolute top-1/2 left-3 h-3.5 w-3.5 -translate-y-1/2 text-subtle" />
                        <input type="text" name="q" value="{{ $search }}" placeholder="Cari potongan rasa atau kata..."
                               class="w-full rounded-xl border border-line bg-canvas py-2 pr-3 pl-9 text-xs text-ink placeholder:text-subtle focus:border-primary focus:bg-white focus:outline-none">
                    </label>
                </form>

                <div class="mt-3 flex flex-wrap gap-1.5">
                    @foreach (['semua' => 'Semua', 'minggu_ini' => 'Minggu Ini', 'ada_pemantik' => 'Ada Pemantik'] as $key => $label)
                        <a href="{{ route('journal.index', array_filter(['filter' => $key, 'q' => $search])) }}"
                           class="pill-tab px-3 py-1 text-[11px] {{ $filter === $key ? 'pill-tab-active' : 'pill-tab-idle' }}">{{ $label }}</a>
                    @endforeach
                </div>

                <div class="mt-4 max-h-[460px] space-y-3 overflow-y-auto pr-1">
                    @forelse ($entries as $entry)
                        <div class="rounded-2xl p-3.5 ring-1 transition {{ $selectedEntry?->id === $entry->id ? 'bg-secondary ring-primary/40' : 'bg-white ring-line hover:ring-primary/30' }}">
                            <div class="flex items-center justify-between gap-2 text-[10px] text-subtle">
                                <span>{{ $entry->created_at->locale('id')->calendar(null, ['sameDay' => '[Hari ini] • HH:mm', 'lastDay' => '[Kemarin], D MMM • HH:mm', 'lastWeek' => 'D MMM • HH:mm', 'sameElse' => 'D MMM YYYY • HH:mm']) }}</span>
                                @if ($entry->prompt) <span class="chip-peach py-0.5 text-[10px]">Pemantik</span> @endif
                            </div>
                            <h3 class="mt-1 line-clamp-1 font-serif text-base text-ink">{{ $entry->title ?: Str::limit($entry->content, 40) }}</h3>
                            <p class="mt-1 line-clamp-2 text-xs leading-relaxed text-muted">{{ $entry->content }}</p>
                            <div class="mt-3 flex items-center justify-between border-t border-line pt-2 text-[11px]">
                                <span class="text-subtle">{{ $entry->word_count }} kata</span>
                                <div class="flex items-center gap-3">
                                    <a href="{{ route('journal.index', array_merge(request()->query(), ['baca' => $entry->id])) }}" class="font-semibold text-primary hover:underline">Baca</a>
                                    <form method="POST" action="{{ route('journal.destroy', $entry) }}" onsubmit="return confirm('Hapus catatan jurnal ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="cursor-pointer text-subtle hover:text-danger" aria-label="Hapus lembar"><x-icon name="trash" class="h-3.5 w-3.5" /></button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="py-8 text-center text-xs text-subtle">Belum ada lembar jurnal yang cocok. Mulailah menulis di kolom sebelah kiri.</p>
                    @endforelse
                </div>

                @if ($totalEntries > 0)
                    <div class="mt-4 flex items-center justify-between border-t border-line pt-4 text-xs">
                        <a href="{{ route('journal.export') }}" class="inline-flex items-center gap-1.5 font-semibold text-primary hover:underline">
                            <x-icon name="download" class="h-3.5 w-3.5" /> Unduh Salinan (.txt)
                        </a>
                        <span class="text-[11px] text-subtle">Bisa dibaca tanpa internet</span>
                    </div>
                @endif
            </div>

            <div class="flex items-start gap-3 rounded-[var(--radius-card)] bg-secondary p-5">
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-white text-primary"><x-icon name="leaf" class="h-4 w-4" /></span>
                <div>
                    <h3 class="font-serif text-lg text-ink">Menyapa Diri Tanpa Paksaan</h3>
                    <p class="mt-1 text-xs leading-relaxed text-muted">
                        Bukan tentang seberapa sering atau panjang tulisanmu. Satu kata dalam seminggu pun sudah menjadi bentuk kasih sayang pada dirimu.
                    </p>
                </div>
            </div>
        </aside>
    </div>
</div>
@endsection
