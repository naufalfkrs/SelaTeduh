@extends('layouts.app')

@section('title', 'SelaTeduh — Kamu nggak harus baik-baik saja setiap saat')

@section('content')
@php use App\Support\Mood; @endphp

<!-- 1. Hero -->
<section class="relative overflow-hidden">
    <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(60%_80%_at_0%_100%,#F6E3D5_0%,transparent_60%),radial-gradient(50%_70%_at_100%_0%,#E1EBDD_0%,transparent_60%)]"></div>

    <div class="container-page relative grid grid-cols-1 items-center gap-10 pt-12 pb-16 lg:grid-cols-12 lg:pt-16 lg:pb-20">
        <div class="lg:col-span-7">
            <span class="chip bg-white text-muted ring-1 ring-line">
                <span class="h-1.5 w-1.5 rounded-full bg-primary"></span>
                Jeda sejenak bersama SelaTeduh
            </span>

            <h1 class="heading-xl mt-5 max-w-xl">Kamu nggak harus baik-baik saja setiap saat.</h1>

            <p class="mt-5 max-w-lg text-[15px] leading-relaxed text-muted">
                Pelan-pelan, kenali apa yang kamu rasakan. Mulai dari satu jeda kecil hari ini tanpa tuntutan untuk terburu-buru menyembuhkan segalanya.
            </p>

            <div class="mt-7 flex flex-wrap items-center gap-3">
                <a href="#cek-perasaan" class="btn-primary px-6 py-3">Mulai dari sini</a>
                <a href="{{ route('resource.index') }}#bantuan" class="btn-secondary px-6 py-3">Jelajahi sumber bantuan</a>
            </div>

            <p class="mt-6 flex items-center gap-2 text-xs text-subtle">
                <x-icon name="sprout" class="h-4 w-4 text-primary" />
                Ruang santun tanpa algoritma yang menuntut produktivitasmu.
            </p>
        </div>

        <div class="lg:col-span-5">
            <figure class="rounded-[1.5rem] bg-white p-3 shadow-[0_20px_50px_-30px_rgba(41,47,42,0.35)] ring-1 ring-line">
                <div class="aspect-[4/3] overflow-hidden rounded-2xl bg-secondary">
                    <img src="https://images.unsplash.com/photo-1517842645767-c639042777db?auto=format&fit=crop&w=900&q=80"
                         alt="Buku catatan dan secangkir teh hangat di atas meja kayu"
                         class="h-full w-full object-cover">
                </div>
                <figcaption class="flex items-end justify-between gap-3 px-2 pt-3 pb-1">
                    <span class="font-serif text-sm italic text-muted">"Ruang tenang untuk menuntun apa yang sedang terjadi."</span>
                    <span class="shrink-0 text-[10px] font-semibold tracking-[0.14em] text-subtle">SELATEDUH</span>
                </figcaption>
            </figure>
        </div>
    </div>
</section>

<!-- 2. Pemeriksaan Diri Mandiri -->
<section id="cek-perasaan" class="scroll-mt-20 bg-secondary/50 py-16">
    <div class="container-page">
        <div class="mx-auto max-w-2xl text-center">
            <span class="eyebrow">Pemeriksaan Diri Mandiri</span>
            <h2 class="heading-lg mt-2">Bagaimana perasaanmu hari ini?</h2>
            <p class="mt-2 text-sm text-muted">
                Pilih yang paling mendekati apa yang ada di hatimu. Tidak ada jawaban yang salah dan tak perlu disembunyikan.
            </p>
        </div>

        <div x-data="{
                selectedMood: 'Tenang',
                selectedTriggers: [],
                toggleTrigger(item) {
                    this.selectedTriggers = this.selectedTriggers.includes(item)
                        ? this.selectedTriggers.filter(i => i !== item)
                        : [...this.selectedTriggers, item];
                },
                note: '',
                saveLocal: false,
                saving: false,
                status: null,   // null | 'error'
                message: '',
                storeKey: 'selateduh:mood-lokal',
                // Check-in cukup sekali: setelah tersimpan, form diganti ringkasan
                done: @js($todayLog ? ['mood' => $todayLog->mood, 'time' => ($todayLog->logged_at ?? $todayLog->created_at)->format('H.i'), 'where' => 'server'] : null),
                nowTime() { const d = new Date(); return String(d.getHours()).padStart(2, '0') + '.' + String(d.getMinutes()).padStart(2, '0'); },
                init() {
                    try { this.saveLocal = localStorage.getItem('selateduh:simpan-peramban') === '1'; } catch (e) {}
                    // Check-in hari ini yang disimpan di peramban saja
                    if (!this.done) {
                        try {
                            const last = JSON.parse(localStorage.getItem(this.storeKey) || '[]')[0];
                            if (last && new Date(last.logged_at).toDateString() === new Date().toDateString()) {
                                const d = new Date(last.logged_at);
                                this.done = { mood: last.mood, time: String(d.getHours()).padStart(2, '0') + '.' + String(d.getMinutes()).padStart(2, '0'), where: 'local' };
                            }
                        } catch (e) {}
                    }
                    this.$watch('saveLocal', v => { try { localStorage.setItem('selateduh:simpan-peramban', v ? '1' : '0'); } catch (e) {} });
                },
                async submit() {
                    this.saving = true;
                    this.status = null;

                    // Opsi privat: simpan murni di peramban, tidak dikirim ke server
                    if (this.saveLocal) {
                        try {
                            const items = JSON.parse(localStorage.getItem(this.storeKey) || '[]');
                            items.unshift({ mood: this.selectedMood, triggers: this.selectedTriggers, note: this.note, logged_at: new Date().toISOString() });
                            localStorage.setItem(this.storeKey, JSON.stringify(items.slice(0, 100)));
                            this.done = { mood: this.selectedMood, time: this.nowTime(), where: 'local' };
                            this.note = '';
                        } catch (e) {
                            this.status = 'error';
                            this.message = 'Peramban ini tidak mengizinkan penyimpanan lokal.';
                        }
                        this.saving = false;
                        return;
                    }

                    try {
                        const res = await fetch(@js(route('mood.store')), {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content'),
                            },
                            body: JSON.stringify({ mood: this.selectedMood, triggers: this.selectedTriggers, note: this.note || null }),
                        });
                        const data = await res.json();
                        // 409 = hari ini sudah tercatat (mis. dari tab lain): tampilkan catatan yang ada
                        if (!res.ok && res.status !== 409) throw new Error(data.message || 'Gagal menyimpan');
                        this.done = { mood: data.mood, time: data.time || this.nowTime(), where: 'server' };
                        this.note = '';
                    } catch (e) {
                        this.status = 'error';
                        this.message = e.message || 'Catatan belum tersimpan. Coba lagi sebentar lagi, ya.';
                    } finally {
                        this.saving = false;
                    }
                }
             }"
             class="card mx-auto mt-8 max-w-2xl p-6 sm:p-7">
            <!-- Sudah check-in hari ini -->
            <div x-show="done" x-cloak x-transition class="text-center">
                <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-secondary text-primary"><x-icon name="check" class="h-6 w-6" /></span>
                <p class="mt-4 font-serif text-xl text-ink">Terima kasih sudah menyapa dirimu hari ini.</p>
                <p class="mt-1 text-sm text-muted">
                    Kamu mencatat rasa <strong class="text-ink" x-text="done && done.mood"></strong>
                    pukul <span x-text="done && done.time"></span> WIB
                    <span x-show="done && done.where === 'local'">(tersimpan di peramban ini saja)</span>.
                </p>
                <p class="mx-auto mt-3 max-w-md text-xs leading-relaxed text-subtle">
                    Check-in ini langsung masuk ke riwayat mood-mu. Catatan rasa cukup sekali sehari; sampai jumpa besok.
                </p>
                <div class="mt-5 flex flex-wrap items-center justify-center gap-3">
                    <a href="{{ route('mood.index') }}" class="btn-primary">Lihat Riwayat Mood <x-icon name="arrow-right" class="h-3.5 w-3.5" /></a>
                </div>
            </div>

            <form method="POST" action="{{ route('mood.store') }}" x-show="!done" x-on:submit.prevent="submit()">
                @csrf
                <input type="hidden" name="mood" x-bind:value="selectedMood">

                <div class="flex flex-wrap justify-center gap-2" role="radiogroup" aria-label="Pilihan perasaan">
                    @foreach (Mood::OPTIONS as $label => $mood)
                        <button type="button" role="radio"
                                x-on:click="selectedMood = @js($label)"
                                x-bind:aria-checked="selectedMood === @js($label)"
                                x-bind:class="selectedMood === @js($label) ? 'bg-primary text-white ring-primary' : 'bg-canvas text-ink ring-line hover:bg-secondary'"
                                class="inline-flex cursor-pointer items-center gap-2 rounded-full px-4 py-2 text-[13px] font-medium ring-1 transition">
                            <span class="h-2 w-2 rounded-full {{ $mood['dot'] }}" x-bind:class="selectedMood === @js($label) && 'ring-2 ring-white/70'"></span>
                            {{ $label }}
                        </button>
                    @endforeach
                </div>

                <!-- Tag pemicu (PRD Modul 2), opsional -->
                <p class="mt-5 mb-2 text-center text-xs text-muted">Pemicu atau tema hari ini (opsional):</p>
                <div class="flex flex-wrap justify-center gap-2">
                    @foreach (Mood::TRIGGERS as $trigger)
                        <button type="button" x-on:click="toggleTrigger(@js($trigger))"
                                x-bind:aria-pressed="selectedTriggers.includes(@js($trigger))"
                                x-bind:class="selectedTriggers.includes(@js($trigger)) ? 'bg-tertiary-soft text-tertiary-dark ring-tertiary' : 'bg-canvas text-ink ring-line hover:bg-secondary'"
                                class="cursor-pointer rounded-full px-3 py-1 text-xs font-medium ring-1 transition">
                            {{ $trigger }}
                        </button>
                    @endforeach
                </div>

                <textarea name="note" rows="3" maxlength="500" x-model="note" class="field mt-5 resize-none"
                          placeholder="Tuliskan sepatah dua patah kata jika ingin... (opsional)"></textarea>
                <p class="mt-1 text-right text-[11px] text-subtle"><span x-text="note.length">0</span>/500 · waktu dicatat otomatis · cukup sekali sehari</p>

                <label class="mt-3 flex cursor-pointer items-center gap-2 text-xs text-muted">
                    <input type="checkbox" x-model="saveLocal" class="h-4 w-4 rounded border-line accent-[#355442]">
                    Simpan di peramban saja (tidak dikirim ke server)
                </label>

                <div class="mt-4 flex flex-wrap items-center gap-4">
                    <button type="submit" class="btn-primary" x-bind:disabled="saving" x-bind:class="saving && 'opacity-60'">
                        <span x-text="saving ? 'Menyimpan...' : 'Simpan dalam ruang privat'">Simpan dalam ruang privat</span>
                    </button>
                    <a href="#pilihan-jeda" class="text-[13px] text-muted underline underline-offset-4 hover:text-ink">Lewati untuk sekarang</a>
                </div>
            </form>

            <!-- Status banner (tanpa muat ulang halaman) -->
            <div x-show="status === 'error' && !done" x-cloak x-transition role="alert"
                 class="mt-4 rounded-2xl bg-[#FBEFE7] px-4 py-3 text-xs font-medium text-[#9A5B36]">
                <span x-text="message"></span>
            </div>

            <p class="mt-5 flex items-start gap-2 text-xs leading-relaxed text-subtle">
                <x-icon name="lock" class="mt-0.5 h-3.5 w-3.5 shrink-0" />
                @auth Catatanmu tersimpan privat di akunmu. @else Tanpa akun pun bisa: catatanmu tersimpan sebagai catatan tamu untuk peramban ini. @endauth
                SelaTeduh tidak mendiagnosis atau menghakimi kondisimu.
            </p>
        </div>
    </div>
</section>

<!-- 3. Pilihan Jeda -->
<section id="pilihan-jeda" class="container-page scroll-mt-20 py-16">
    <span class="eyebrow">Pilihan Jeda</span>
    <h2 class="heading-lg mt-2">Tiga cara kecil untuk menemani harimu</h2>
    <p class="mt-2 max-w-lg text-sm text-muted">
        Tanpa target skor atau deretan misi harian yang melelahkan. Cukup pilih apa yang terasa pas untuk saat ini.
    </p>

    <div class="mt-8 grid grid-cols-1 gap-5 md:grid-cols-2">
        <!-- Latihan Napas -->
        <article class="card-soft relative flex flex-col overflow-hidden p-6 sm:p-7">
            <div class="pointer-events-none absolute -top-10 -right-10 h-40 w-40 rounded-full bg-white/40"></div>
            <div class="relative flex items-center justify-between">
                <span class="flex h-10 w-10 items-center justify-center rounded-full bg-white text-primary"><x-icon name="wind" class="h-5 w-5" /></span>
                <span class="chip bg-white/70 text-muted">3 menit</span>
            </div>
            <h3 class="heading-md relative mt-5">Latihan Napas</h3>
            <p class="relative mt-1.5 text-sm leading-relaxed text-muted">
                Ambil jeda dan ikuti ritme napasmu untuk meredakan ketegangan fisik serta dada yang terasa sesak.
            </p>

            <div class="relative mt-5 flex items-center gap-4 rounded-2xl bg-white p-4">
                <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-secondary ring-4 ring-secondary/60">
                    <span class="flex h-7 w-7 items-center justify-center rounded-full bg-primary text-white"><x-icon name="leaf" class="h-3.5 w-3.5" /></span>
                </span>
                <div>
                    <p class="font-serif text-[15px] italic text-ink">4 detik tarik napas • 4 detik tahan • 4 detik hembuskan</p>
                    <p class="mt-0.5 text-xs text-muted">Teknik kotak pernapasan untuk menenangkan sistem saraf simpatik.</p>
                </div>
            </div>

            <a href="{{ route('exercise.index') }}" class="link-arrow relative mt-6">Mulai Latihan Napas <x-icon name="arrow-right" class="h-3.5 w-3.5" /></a>
        </article>

        <!-- Jurnal Refleksi -->
        <article class="card-soft flex flex-col p-6 sm:p-7">
            <div class="flex items-center justify-between">
                <span class="flex h-10 w-10 items-center justify-center rounded-full bg-white text-primary"><x-icon name="pen" class="h-5 w-5" /></span>
                <span class="chip bg-white/70 text-muted">Reflektif</span>
            </div>
            <h3 class="heading-md mt-5">Jurnal Refleksi</h3>
            <p class="mt-1.5 text-sm leading-relaxed text-muted">
                Tuliskan apa yang sedang ada di pikiranmu tanpa perlu cemas akan tata bahasa atau kerapian.
            </p>

            <div class="mt-5 rounded-2xl bg-white p-4">
                <span class="text-[11px] font-medium text-subtle">Topik Hari Ini</span>
                <p class="mt-1 font-serif text-lg italic leading-snug text-ink">"Apa hal kecil yang membuatmu bertahan dan merasa hangat minggu ini?"</p>
                <p class="mt-2 text-xs text-muted">Tidak perlu panjang. Satu kalimat saja sudah berharga.</p>
            </div>

            <a href="{{ route('journal.index') }}" class="link-arrow mt-6">Buka Lembar Jurnal <x-icon name="arrow-right" class="h-3.5 w-3.5" /></a>
        </article>
    </div>

    <!-- Catatan Mood -->
    <article class="card mt-5 flex flex-col justify-between gap-6 p-6 sm:p-7 md:flex-row md:items-center">
        <div class="max-w-md">
            <span class="eyebrow flex items-center gap-1.5"><x-icon name="calendar" class="h-3.5 w-3.5" /> Langkah Bertahap</span>
            <h3 class="heading-md mt-2">Catatan Mood</h3>
            <p class="mt-1.5 text-sm leading-relaxed text-muted">
                Kenali perubahan perasaanmu dari waktu ke waktu. Bukan untuk mengejar hari-hari serba bahagia, tapi memahami ritme hatimu yang alami.
            </p>
            <a href="{{ route('mood.index') }}" class="link-arrow mt-4">Lihat Pola Moodmu <x-icon name="arrow-right" class="h-3.5 w-3.5" /></a>
        </div>

        <div class="w-full rounded-2xl bg-canvas p-4 ring-1 ring-line md:w-auto">
            <div class="mb-3 flex items-center justify-between gap-6 text-[11px]">
                <span class="font-semibold text-ink">7 Hari Terakhir</span>
                <span class="text-subtle">{{ $recordedDays }} hari tercatat</span>
            </div>
            <div class="grid grid-cols-7 gap-2 text-center">
                @foreach ($lastSevenDays as $day)
                    <div class="flex flex-col items-center gap-1.5">
                        <span class="text-[10px] {{ $day['is_today'] ? 'font-bold text-primary' : 'text-subtle' }}">{{ $day['day_short'] }}</span>
                        @if ($day['mood'])
                            <span title="{{ $day['mood'] }}" class="flex h-8 w-8 items-center justify-center rounded-full {{ Mood::get($day['mood'], 'solid') }}">
                                <x-icon :name="Mood::get($day['mood'], 'icon')" class="h-3.5 w-3.5" />
                            </span>
                        @elseif ($day['is_today'])
                            <span title="Belum dicatat" class="flex h-8 w-8 items-center justify-center rounded-full bg-white text-[9px] font-semibold text-primary ring-1 ring-primary/40">Kini</span>
                        @else
                            <span title="Belum dicatat" class="h-8 w-8 rounded-full border border-dashed border-subtle/50 bg-white"></span>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </article>
</section>

<!-- 4. Perpustakaan Rasa -->
<section class="bg-secondary/40 py-16">
    <div class="container-page">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <span class="eyebrow">Perpustakaan Rasa</span>
                <h2 class="heading-lg mt-2">Pahami apa yang kamu alami</h2>
                <p class="mt-2 max-w-md text-sm text-muted">
                    Artikel singkat yang ditulis bersama praktisi psikologi remaja, tanpa jargon yang membingungkan atau menghakimi.
                </p>
            </div>
            <a href="{{ route('resource.index') }}" class="link-arrow">Lihat Semua Bacaan ({{ $articleCount }} artikel) <x-icon name="arrow-right" class="h-3.5 w-3.5" /></a>
        </div>

        <div class="mt-8 grid grid-cols-1 gap-5 md:grid-cols-3">
            @forelse ($featuredArticles as $article)
                @include('resources.partials.card', ['article' => $article])
            @empty
                <div class="card p-8 text-center text-sm text-muted md:col-span-3">
                    Belum ada bacaan. Jalankan <code class="rounded bg-canvas px-1.5 py-0.5">php artisan db:seed</code> agar daftar bacaan muncul di sini.
                </div>
            @endforelse
        </div>
    </div>
</section>

<!-- 5. Ruang Aman & Dukungan Nyata -->
<section class="container-page pt-16">
    @include('resources.partials.help-banner')
</section>
@endsection
