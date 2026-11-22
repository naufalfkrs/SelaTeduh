@extends('layouts.app')

@section('title', 'Catatan Mood & Pola Perasaan — SelaTeduh')

@section('content')
@php use App\Support\Mood; @endphp

<div class="container-page pt-8">
    <!-- Top Bar -->
    <div class="flex flex-wrap items-center justify-end gap-3 text-xs text-muted">
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

            <!-- 1. Catatan Mood Hari Ini (diisi dari Cek Perasaan di Beranda; halaman ini hanya riwayat) -->
            @if ($todayLog)
                @php $todayTime = $todayLog->logged_at ?? $todayLog->created_at; @endphp
                <div id="catatan-hari-ini" class="card scroll-mt-24 p-5 sm:p-7">
                    <div class="flex flex-wrap items-start justify-between gap-2">
                        <div>
                            <span class="eyebrow">Catatan Hari Ini</span>
                            <h2 class="heading-md mt-1">Rasamu hari ini sudah tercatat.</h2>
                        </div>
                        <span class="chip bg-canvas text-muted ring-1 ring-line">{{ now()->locale('id')->isoFormat('dddd, D MMM YYYY') }}</span>
                    </div>

                    <div class="mt-5 flex flex-wrap items-center gap-4 rounded-2xl bg-secondary p-5">
                        <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full {{ Mood::get($todayLog->mood, 'soft', 'bg-white text-primary') }}">
                            <x-icon :name="Mood::get($todayLog->mood, 'icon', 'circle')" class="h-6 w-6" />
                        </span>
                        <div class="min-w-0 flex-1">
                            <p class="font-serif text-xl text-ink">{{ $todayLog->mood }}</p>
                            <p class="flex items-center gap-1 text-xs text-muted"><x-icon name="clock" class="h-3 w-3" /> Dicatat pukul {{ $todayTime->format('H.i') }} WIB</p>
                        </div>
                    </div>

                    @if (! empty($todayLog->triggers))
                        <div class="mt-4 flex flex-wrap gap-1.5">
                            @foreach ($todayLog->triggers as $trig)
                                <span class="chip bg-tertiary-soft py-0.5 text-[11px] text-tertiary-dark">{{ $trig }}</span>
                            @endforeach
                        </div>
                    @endif

                    @if ($todayLog->note)
                        <p class="mt-4 text-sm leading-relaxed text-ink">"{{ $todayLog->note }}"</p>
                    @endif

                    <p class="mt-5 border-t border-line pt-4 text-xs leading-relaxed text-subtle">
                        Catatan rasa cukup sekali sehari dan tidak bisa diubah. Terima kasih sudah menyapa dirimu hari ini, sampai jumpa besok.
                    </p>
                </div>
            @else
                <div id="catatan-hari-ini" class="card flex scroll-mt-24 flex-col items-start gap-4 p-5 sm:flex-row sm:items-center sm:justify-between sm:p-7">
                    <div>
                        <span class="eyebrow">Catatan Hari Ini</span>
                        <h2 class="heading-md mt-1">Kamu belum mencatat rasa hari ini.</h2>
                        <p class="mt-1 text-xs leading-relaxed text-muted">Catat rasamu lewat Cek Perasaan di Beranda. Cukup sekali sehari, nanti otomatis muncul di riwayat ini.</p>
                    </div>
                    <a href="{{ route('home') }}#cek-perasaan" class="btn-primary shrink-0">Catat di Beranda <x-icon name="arrow-right" class="h-3.5 w-3.5" /></a>
                </div>
            @endif

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
                        @php $isToday = $log->logged_date->isToday(); @endphp
                        <div class="card p-5 {{ $isToday ? 'ring-1 ring-primary-soft' : '' }}">
                            <div>
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
                                    </div>
                                </div>

                                @if ($log->note)
                                    <p class="mt-3 text-sm leading-relaxed text-ink">"{{ $log->note }}"</p>
                                @else
                                    <p class="mt-3 text-xs italic text-subtle">(Hanya check-in cepat tanpa catatan teks)</p>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="card p-10 text-center">
                            <p class="font-serif text-lg text-ink">Belum ada catatan pada filter ini.</p>
                            <p class="mt-1 text-xs text-muted">Mulai dari Cek Perasaan di Beranda kapan pun kamu siap.</p>
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
