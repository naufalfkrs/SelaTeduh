@extends('layouts.app')

@section('title', 'Catatan Mood & Pola Perasaan — SelaTeduh')

@section('content')
@php use App\Support\Mood; @endphp

<div class="container-page pt-8">
    <!-- Top Bar -->
    <div class="flex flex-wrap items-center justify-between gap-3 text-xs text-muted">
        <a href="{{ route('home') }}" class="inline-flex items-center gap-1.5 font-medium transition hover:text-primary">
            <x-icon name="arrow-left" class="h-3.5 w-3.5" /> Kembali ke Aktivitas &amp; Beranda
        </a>
        <span class="chip bg-white text-muted ring-1 ring-line">
            <span class="h-1.5 w-1.5 rounded-full bg-primary"></span>
            @auth Tersimpan Privat di Akunmu @else Mode Tamu • Tersimpan untuk Peramban Ini @endauth • Tanpa Pelacakan Data
        </span>
    </div>

    <!-- Judul -->
    <div class="mt-8 mb-8 flex flex-col justify-between gap-6 md:flex-row md:items-end">
        <div>
            <span class="eyebrow flex items-center gap-2"><span class="h-px w-6 bg-muted"></span> Catatan Mood &amp; Riwayat Kesejahteraan</span>
            <h1 class="heading-xl mt-2">Kenali pola perasaanmu.</h1>
            <p class="mt-2 max-w-xl font-serif text-lg italic text-muted">
                Catatan kecil untuk melihat kembali bagaimana harimu berjalan. Fluktuasi rasa adalah hal yang sangat manusiawi.
            </p>
        </div>
        <div class="flex max-w-xs items-start gap-3 rounded-2xl bg-secondary p-4 text-xs leading-relaxed text-muted">
            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-white text-primary"><x-icon name="sprout" class="h-4 w-4" /></span>
            Tidak ada skor, tidak ada target kesempurnaan. Setiap emosi berhak memiliki ruang jeda.
        </div>
    </div>

    @include('partials.guest-note')

    <div class="grid grid-cols-1 items-start gap-6 lg:grid-cols-12">
        <!-- Kolom Kiri -->
        <div class="space-y-6 lg:col-span-8">

            <!-- 1. Form Catatan Mood -->
            <div x-data="{
                    selectedMood: 'Tenang',
                    selectedTriggers: [],
                    note: '',
                    toggleTrigger(item) {
                        this.selectedTriggers = this.selectedTriggers.includes(item)
                            ? this.selectedTriggers.filter(i => i !== item)
                            : [...this.selectedTriggers, item];
                    },
                    resetForm() { this.selectedMood = 'Tenang'; this.selectedTriggers = []; this.note = ''; this.customTime = false; },

                    // Waktu pencatatan
                    clock: '',
                    customTime: @js(old('logged_at') !== null),
                    loggedAt: @js(old('logged_at', '')),
                    maxTime: '',
                    submitting: false,
                    pad(n) { return String(n).padStart(2, '0'); },
                    localInputValue(d) { return `${d.getFullYear()}-${this.pad(d.getMonth() + 1)}-${this.pad(d.getDate())}T${this.pad(d.getHours())}:${this.pad(d.getMinutes())}`; },
                    tick() {
                        const now = new Date();
                        this.clock = `${this.pad(now.getHours())}.${this.pad(now.getMinutes())}`;
                        this.maxTime = this.localInputValue(now);
                    },
                    enableCustomTime() { this.loggedAt = this.maxTime; this.customTime = true; },
                    init() { this.tick(); setInterval(() => this.tick(), 15000); }
                 }"
                 class="card p-5 sm:p-7">
                <div class="flex flex-wrap items-start justify-between gap-2">
                    <div>
                        <span class="eyebrow">Langkah Sederhana</span>
                        <h2 class="heading-md mt-1">Bagaimana rasamu saat ini?</h2>
                    </div>
                    <span class="chip bg-canvas text-muted ring-1 ring-line">{{ now()->locale('id')->isoFormat('dddd, D MMM YYYY') }}</span>
                </div>

                <form method="POST" action="{{ route('mood.store') }}" class="mt-5" x-on:submit="submitting = true">
                    @csrf
                    <input type="hidden" name="mood" x-bind:value="selectedMood">
                    <template x-for="trigger in selectedTriggers" :key="trigger">
                        <input type="hidden" name="triggers[]" x-bind:value="trigger">
                    </template>

                    <p class="mb-2.5 text-xs text-muted">Pilih ungkapan yang paling mendekati hatimu:</p>
                    <div class="grid grid-cols-3 gap-2.5 sm:grid-cols-5" role="radiogroup" aria-label="Pilihan perasaan">
                        @foreach (Mood::OPTIONS as $label => $mood)
                            <button type="button" role="radio"
                                    x-on:click="selectedMood = @js($label)"
                                    x-bind:aria-checked="selectedMood === @js($label)"
                                    x-bind:class="selectedMood === @js($label) ? 'bg-primary text-white ring-primary shadow-[0_10px_24px_-14px_rgba(53,84,66,0.9)]' : 'bg-canvas text-ink ring-line hover:bg-secondary'"
                                    class="flex cursor-pointer flex-col items-center justify-center gap-2 rounded-2xl px-2 py-4 text-xs font-semibold ring-1 transition">
                                <x-icon :name="$mood['icon']" class="h-5 w-5" />
                                <span>{{ $label }}</span>
                                <span class="h-1 w-1 rounded-full" x-bind:class="selectedMood === @js($label) ? 'bg-white' : 'bg-transparent'"></span>
                            </button>
                        @endforeach
                    </div>
                    @error('mood') <p class="mt-1.5 text-xs text-danger">{{ $message }}</p> @enderror

                    <p class="mt-6 mb-2.5 text-xs text-muted">Pemicu atau tema hari ini (opsional):</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach (Mood::TRIGGERS as $trigger)
                            <button type="button" x-on:click="toggleTrigger(@js($trigger))"
                                    x-bind:class="selectedTriggers.includes(@js($trigger)) ? 'bg-tertiary-soft text-tertiary-dark ring-tertiary' : 'bg-secondary text-ink ring-transparent hover:ring-line'"
                                    class="cursor-pointer rounded-full px-3.5 py-1.5 text-xs font-medium ring-1 transition">
                                {{ $trigger }}
                            </button>
                        @endforeach
                    </div>

                    <label for="note" class="mt-6 mb-2 block font-serif text-base italic text-muted">Apa yang sedang singgah di pikiranmu?</label>
                    <textarea id="note" name="note" rows="3" x-model="note" maxlength="500" class="field resize-none"
                              placeholder="Tuliskan sepatah dua patah kata jika ingin mencatat penyebabnya... (opsional, hanya untuk dirimu sendiri)"></textarea>
                    <div class="mt-1 flex justify-between text-[11px] text-subtle">
                        @error('note') <span class="text-danger">{{ $message }}</span> @else <span></span> @enderror
                        <span><span x-text="note.length">0</span>/500</span>
                    </div>
                    @error('logged_at') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror

                    <div class="mt-5 flex flex-wrap items-center justify-between gap-3 border-t border-line pt-4">
                        <!-- Waktu pencatatan: real-time, bisa disesuaikan manual (PRD Modul 2) -->
                        <div class="flex flex-wrap items-center gap-2 text-xs text-subtle">
                            <x-icon name="clock" class="h-3.5 w-3.5" />
                            <span>Waktu dicatat:</span>
                            <template x-if="!customTime">
                                <span class="flex items-center gap-2">
                                    <span class="font-semibold text-ink" x-text="'Sekarang, ' + clock + ' WIB'"></span>
                                    <button type="button" x-on:click="enableCustomTime()" class="cursor-pointer text-primary underline underline-offset-2 hover:text-primary-dark">Ubah</button>
                                </span>
                            </template>
                            <template x-if="customTime">
                                <span class="flex items-center gap-2">
                                    <input type="datetime-local" name="logged_at" x-model="loggedAt" x-bind:max="maxTime" required
                                           aria-label="Sesuaikan waktu pencatatan"
                                           class="rounded-lg border border-line bg-canvas px-2 py-1 text-xs text-ink focus:border-primary focus:outline-none">
                                    <button type="button" x-on:click="customTime = false" class="cursor-pointer text-muted underline underline-offset-2 hover:text-ink">Pakai waktu sekarang</button>
                                </span>
                            </template>
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="button" x-on:click="resetForm()" class="btn-ghost">Kosongkan</button>
                            <button type="submit" class="btn-primary" x-bind:disabled="submitting" x-bind:class="submitting && 'opacity-60'">
                                <x-icon name="check" class="h-3.5 w-3.5" />
                                <span x-text="submitting ? 'Menyimpan...' : 'Simpan Catatan'">Simpan Catatan</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- 2. Ritme Minggu Ini -->
            <div class="card-soft p-5 sm:p-7">
                <div class="flex flex-wrap items-start justify-between gap-2">
                    <div>
                        <h2 class="heading-md">Ritme Minggu Ini</h2>
                        <p class="mt-1 max-w-md text-xs leading-relaxed text-muted">
                            Bukan untuk dinilai, diukur, atau mencapai skor tertentu, melainkan memberi ruang penerimaan atas apa yang kamu lalui.
                        </p>
                    </div>
                    <span class="chip bg-white text-muted">
                        {{ now()->startOfWeek()->locale('id')->isoFormat('D') }} – {{ now()->endOfWeek()->locale('id')->isoFormat('D MMMM YYYY') }}
                    </span>
                </div>

                <div class="mt-5 grid grid-cols-4 gap-2 sm:grid-cols-7">
                    @foreach ($weekDays as $day)
                        @php $moodName = $day['log']?->mood; @endphp
                        <div class="flex flex-col items-center gap-1.5 rounded-2xl px-1 py-3 text-center {{ $day['is_today'] ? 'bg-primary text-white' : ($moodName ? 'bg-white' : 'border border-dashed border-subtle/40 bg-white/40') }}">
                            <span class="text-[10px] {{ $day['is_today'] ? 'text-white/80' : 'text-subtle' }}">{{ $day['day_name'] }}</span>
                            <span class="text-xs font-semibold {{ $day['is_today'] ? '' : 'text-ink' }}">{{ $day['day_num'] }}</span>
                            @if ($moodName)
                                <span class="flex h-7 w-7 items-center justify-center rounded-full {{ $day['is_today'] ? 'bg-white/20 text-white' : Mood::get($moodName, 'soft') }}">
                                    <x-icon :name="Mood::get($moodName, 'icon')" class="h-3.5 w-3.5" />
                                </span>
                            @else
                                <span class="flex h-7 w-7 items-center justify-center rounded-full {{ $day['is_today'] ? 'bg-white/20' : '' }} text-xs">—</span>
                            @endif
                            <span class="text-[10px] font-medium {{ $day['is_today'] ? '' : 'text-muted' }}">{{ $moodName ?? ($day['is_today'] ? 'Hari ini' : ($day['is_future'] ? 'Nanti' : 'Kosong')) }}</span>
                        </div>
                    @endforeach
                </div>

                <div class="mt-4 flex items-start gap-3 rounded-2xl bg-white p-4">
                    <x-icon name="help" class="mt-0.5 h-4 w-4 shrink-0 text-primary" />
                    <div class="text-xs leading-relaxed text-muted">
                        <p class="mb-0.5 font-semibold text-ink">Catatan Lembut Pekan Ini</p>
                        {{ $weeklyNarrative }}
                    </div>
                </div>
            </div>

            <!-- 3. Catatan & Jejak Rasa Terdahulu -->
            <div>
                <div class="mb-4 flex flex-wrap items-end justify-between gap-3">
                    <div>
                        <h2 class="heading-md">Catatan &amp; Jejak Rasa Terdahulu</h2>
                        <p class="text-xs text-muted">Tersimpan aman dan privat untukmu.</p>
                    </div>
                    <div class="flex gap-1.5">
                        @foreach (['semua' => 'Semua', 'hanya_mood' => 'Hanya Mood', 'ada_catatan' => 'Ada Catatan'] as $key => $label)
                            <a href="{{ route('mood.index', ['filter' => $key]) }}" class="pill-tab {{ $filter === $key ? 'pill-tab-active' : 'pill-tab-idle' }}">{{ $label }}</a>
                        @endforeach
                    </div>
                </div>

                <div class="space-y-3">
                    @forelse ($moodLogs as $log)
                        <div x-data="{ editing: false }" class="card p-5">
                            <div x-show="!editing">
                                <div class="flex flex-wrap items-center justify-between gap-2">
                                    <div class="flex flex-wrap items-center gap-1.5">
                                        <span class="chip {{ Mood::get($log->mood, 'soft', 'bg-secondary text-primary') }}">
                                            <x-icon :name="Mood::get($log->mood, 'icon', 'circle')" class="h-3.5 w-3.5" /> {{ $log->mood }}
                                        </span>
                                        @foreach ($log->triggers ?? [] as $trig)
                                            <span class="chip bg-canvas py-0.5 text-[11px] text-muted ring-1 ring-line">{{ $trig }}</span>
                                        @endforeach
                                    </div>
                                    <div class="flex items-center gap-3 text-[11px] text-subtle">
                                        <span class="flex items-center gap-1">
                                            <x-icon name="clock" class="h-3 w-3" />
                                            {{ $log->logged_date->locale('id')->isoFormat('ddd, D MMM') }} • {{ ($log->logged_at ?? $log->created_at)->format('H:i') }} WIB
                                        </span>
                                        <button type="button" x-on:click="editing = true" class="cursor-pointer hover:text-primary" aria-label="Ubah catatan"><x-icon name="edit" class="h-3.5 w-3.5" /></button>
                                        <form method="POST" action="{{ route('mood.destroy', $log) }}" onsubmit="return confirm('Hapus catatan perasaan ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="cursor-pointer hover:text-danger" aria-label="Hapus catatan"><x-icon name="trash" class="h-3.5 w-3.5" /></button>
                                        </form>
                                    </div>
                                </div>

                                @if ($log->note)
                                    <p class="mt-3 text-sm leading-relaxed text-ink">"{{ $log->note }}"</p>
                                @else
                                    <p class="mt-3 text-xs italic text-subtle">(Hanya check-in cepat tanpa catatan teks)</p>
                                @endif
                            </div>

                            <div x-show="editing" x-cloak>
                                <form method="POST" action="{{ route('mood.update', $log) }}" class="space-y-3">
                                    @csrf
                                    @method('PUT')
                                    <div>
                                        <label class="mb-1 block text-xs font-semibold text-primary">Ubah Perasaan</label>
                                        <select name="mood" class="rounded-xl border border-line bg-canvas px-3 py-2 text-xs">
                                            @foreach (Mood::labels() as $m)
                                                <option value="{{ $m }}" @selected($log->mood === $m)>{{ $m }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @foreach ($log->triggers ?? [] as $trig)
                                        <input type="hidden" name="triggers[]" value="{{ $trig }}">
                                    @endforeach
                                    <div>
                                        <label class="mb-1 block text-xs font-semibold text-primary">Catatan</label>
                                        <textarea name="note" rows="2" class="field">{{ $log->note }}</textarea>
                                    </div>
                                    <div class="flex items-center justify-end gap-2">
                                        <button type="button" x-on:click="editing = false" class="btn-ghost">Batal</button>
                                        <button type="submit" class="btn-primary">Simpan Perubahan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="card p-10 text-center">
                            <p class="font-serif text-lg text-ink">Belum ada catatan pada filter ini.</p>
                            <p class="mt-1 text-xs text-muted">Mulai dari check-in singkat di atas kapan pun kamu siap.</p>
                        </div>
                    @endforelse
                </div>

                <div class="mt-6">{{ $moodLogs->links() }}</div>
            </div>
        </div>

        <!-- Sidebar -->
        <aside class="space-y-5 lg:col-span-4">
            <div class="card p-5">
                <span class="eyebrow flex items-center gap-1.5"><x-icon name="sprout" class="h-3.5 w-3.5" /> Jejak Pemulihan Diri</span>
                <h3 class="mt-1 font-serif text-xl text-ink">Aktivitas yang Telah Selesai</h3>
                <p class="text-xs text-muted">Jeda mindful yang telah kamu ambil untuk merawat diri minggu ini.</p>

                <div class="mt-4 space-y-2.5">
                    @foreach ($recentBreathing as $breath)
                        <div class="flex items-start gap-3 rounded-2xl bg-canvas p-3 ring-1 ring-line">
                            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-secondary text-primary"><x-icon name="wind" class="h-4 w-4" /></span>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center justify-between gap-2 text-[10px] text-subtle">
                                    <span class="font-semibold text-muted">Latihan Napas</span><span>{{ $breath->created_at->locale('id')->isoFormat('D MMM, HH:mm') }}</span>
                                </div>
                                <p class="truncate text-[13px] font-semibold text-ink">{{ $breath->rhythm_name }}</p>
                                <p class="text-[11px] text-muted">{{ $breath->cycles }} siklus • {{ $breath->duration }}</p>
                            </div>
                        </div>
                    @endforeach

                    @foreach ($recentJournals as $journal)
                        <div class="flex items-start gap-3 rounded-2xl bg-canvas p-3 ring-1 ring-line">
                            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-tertiary-soft text-tertiary-dark"><x-icon name="pen" class="h-4 w-4" /></span>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center justify-between gap-2 text-[10px] text-subtle">
                                    <span class="font-semibold text-muted">Jurnal Refleksi</span><span>{{ $journal->created_at->locale('id')->isoFormat('D MMM, HH:mm') }}</span>
                                </div>
                                <p class="truncate text-[13px] font-semibold text-ink">"{{ $journal->title ?: Str::limit($journal->content, 35) }}"</p>
                                <p class="text-[11px] text-muted">{{ $journal->word_count }} kata tersimpan</p>
                            </div>
                        </div>
                    @endforeach

                    @if ($recentBreathing->isEmpty() && $recentJournals->isEmpty())
                        <p class="py-2 text-xs italic text-subtle">Belum ada sesi latihan napas atau jurnal terbaru.</p>
                    @endif
                </div>

                <div class="mt-4 space-y-2">
                    <a href="{{ route('exercise.index') }}" class="flex items-center justify-between rounded-xl bg-secondary px-4 py-2.5 text-xs font-semibold text-primary transition hover:bg-secondary-dark">
                        Mulai Latihan Napas Baru <x-icon name="arrow-right" class="h-3.5 w-3.5" />
                    </a>
                    <a href="{{ route('journal.index') }}" class="flex items-center justify-between rounded-xl bg-secondary px-4 py-2.5 text-xs font-semibold text-primary transition hover:bg-secondary-dark">
                        Buka Lembar Jurnal Lengkap <x-icon name="arrow-right" class="h-3.5 w-3.5" />
                    </a>
                </div>
            </div>

            <figure class="card overflow-hidden">
                <img src="https://images.unsplash.com/photo-1600210492486-724fe5c67fb0?auto=format&fit=crop&w=700&q=80"
                     alt="Teras rumah yang teduh dengan kursi dan tanaman" class="h-40 w-full object-cover">
                <figcaption class="p-5">
                    <span class="eyebrow">Pengingat Hari Ini</span>
                    <p class="mt-2 font-serif text-lg italic leading-snug text-ink">"Tidak apa melangkah perlahan. Istirahat juga bagian dari perjalanan."</p>
                </figcaption>
            </figure>

            <div class="rounded-[var(--radius-card)] bg-secondary p-5 text-xs leading-relaxed text-muted">
                <p class="mb-1.5 flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-[0.12em] text-primary"><x-icon name="shield" class="h-3.5 w-3.5" /> Batas Ruang Kami</p>
                Pola perasaan di SelaTeduh dihadirkan murni sebagai cermin refleksi mandiri, <strong class="text-ink">bukan alat diagnosis klinis atau penegakan psikologis</strong>. Jika rasa resah atau lelah terasa berat dan berkepanjangan, jangan ragu untuk bercerita kepada konselor terdekat atau mengakses
                <a href="{{ route('page.help') }}" class="font-semibold text-primary underline underline-offset-2">layanan pendampingan resmi</a>.
            </div>
        </aside>
    </div>
</div>
@endsection
