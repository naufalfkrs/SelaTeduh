@extends('layouts.app')

@section('title', 'Ambil Jeda Sejenak — Latihan Napas SelaTeduh')

@section('content')
<div x-data="{
        rhythms: @js($rhythms),
        completeUrl: @js(route('exercise.complete')),
        selectedIndex: 0,
        status: 'idle', // idle | running | paused | finished
        phase: 'ready', // ready | inhale | hold | exhale | done
        secondsLeft: 4,
        currentCycle: 1,
        elapsedSeconds: 0,
        timer: null,
        sessionSaved: false,

        init() {
            this.secondsLeft = this.currentRhythm.inhale;
        },

        get currentRhythm() {
            return this.rhythms[this.selectedIndex];
        },

        get phaseLabel() {
            return {
                ready: 'Bersiap',
                inhale: 'Tarik Napas',
                hold: 'Tahan',
                exhale: 'Hembuskan',
                done: 'Selesai'
            }[this.phase];
        },

        get circleTransform() {
            if (this.phase === 'inhale' || this.phase === 'hold') {
                return 'transform: scale(1.12);';
            }
            return 'transform: scale(1);';
        },

        get formattedDuration() {
            const mins = String(Math.floor(this.elapsedSeconds / 60)).padStart(2, '0');
            const secs = String(this.elapsedSeconds % 60).padStart(2, '0');
            return `${mins}:${secs}`;
        },

        selectRhythm(index) {
            this.stopTimer();
            this.selectedIndex = index;
            this.status = 'idle';
            this.phase = 'ready';
            this.currentCycle = 1;
            this.elapsedSeconds = 0;
            this.secondsLeft = this.currentRhythm.inhale;
            this.sessionSaved = false;
        },

        toggleSession() {
            if (this.status === 'running') {
                this.pauseSession();
                return;
            }

            if (this.status === 'idle' || this.status === 'finished') {
                this.currentCycle = 1;
                this.elapsedSeconds = 0;
                this.phase = 'inhale';
                this.secondsLeft = this.currentRhythm.inhale;
                this.sessionSaved = false;
            }

            this.status = 'running';
            this.timer = setInterval(() => this.tick(), 1000);
        },

        pauseSession() {
            this.status = 'paused';
            this.stopTimer();
        },

        endSession() {
            this.stopTimer();
            this.status = 'idle';
            this.phase = 'ready';
            this.currentCycle = 1;
            this.elapsedSeconds = 0;
            this.secondsLeft = this.currentRhythm.inhale;
        },

        stopTimer() {
            if (this.timer) {
                clearInterval(this.timer);
                this.timer = null;
            }
        },

        tick() {
            this.elapsedSeconds++;

            if (this.secondsLeft > 1) {
                this.secondsLeft--;
                return;
            }

            // Perpindahan fase napas
            if (this.phase === 'inhale') {
                if (this.currentRhythm.hold > 0) {
                    this.phase = 'hold';
                    this.secondsLeft = this.currentRhythm.hold;
                } else {
                    this.phase = 'exhale';
                    this.secondsLeft = this.currentRhythm.exhale;
                }
            } else if (this.phase === 'hold') {
                this.phase = 'exhale';
                this.secondsLeft = this.currentRhythm.exhale;
            } else if (this.phase === 'exhale') {
                if (this.currentCycle < this.currentRhythm.cycles) {
                    this.currentCycle++;
                    this.phase = 'inhale';
                    this.secondsLeft = this.currentRhythm.inhale;
                } else {
                    this.finishSession();
                }
            }
        },

        finishSession() {
            this.stopTimer();
            this.status = 'finished';
            this.phase = 'done';
            this.secondsLeft = 0;

            if (!this.sessionSaved) {
                this.sessionSaved = true;
                fetch(this.completeUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        rhythm_name: this.currentRhythm.name,
                        cycles: this.currentRhythm.cycles,
                        duration: this.formattedDuration
                    })
                });
            }
        }
     }"
     class="container-page pt-8 pb-4">

    <!-- Top Bar -->
    <div class="flex flex-wrap items-center justify-between gap-3 text-xs text-muted">
        <a href="{{ route('home') }}#pilihan-jeda" class="inline-flex items-center gap-1.5 font-medium transition hover:text-primary">
            <x-icon name="arrow-left" class="h-3.5 w-3.5" /> Kembali ke Latihan
        </a>
        <span class="chip bg-white text-muted ring-1 ring-line">
            <span class="h-1.5 w-1.5 rounded-full bg-primary"></span>
            Latihan Mandiri • <span x-text="'Ritme ' + currentRhythm.name">Ritme Rileks (4-7-8)</span>
        </span>
    </div>

    <!-- Judul -->
    <div class="mx-auto mt-10 max-w-lg text-center">
        <span class="eyebrow">Ruang Jeda Mandiri</span>
        <h1 class="heading-xl mt-3">Ambil jeda sejenak.</h1>
        <p class="mt-3 text-sm leading-relaxed text-muted">
            Ikuti ritmenya dengan nyaman. Tak ada target yang perlu dicapai, dan kamu bebas berhenti kapan saja.
        </p>
    </div>

    <!-- Kartu Visualizer Napas -->
    <div class="relative mx-auto mt-10 max-w-2xl overflow-hidden rounded-[1.75rem] bg-gradient-to-br from-secondary via-[#F1F3EC] to-tertiary-soft px-6 py-12 text-center ring-1 ring-line sm:py-14">
        <div class="relative mx-auto flex h-64 w-64 items-center justify-center sm:h-72 sm:w-72">
            <div x-bind:style="circleTransform" class="breath-ring absolute inset-0 rounded-full bg-[#EDE6DA]/80"></div>
            <div x-bind:style="circleTransform" class="breath-ring absolute inset-5 rounded-full bg-[#F4EEE5] ring-1 ring-[#E4DACB]"></div>

            <div class="relative z-10 flex h-44 w-44 flex-col items-center justify-center rounded-full bg-white px-4 shadow-[0_10px_30px_-15px_rgba(41,47,42,0.35)] sm:h-48 sm:w-48">
                <span class="text-[10px] font-semibold uppercase tracking-[0.16em] text-subtle">Napas Teduh</span>
                <p x-text="phaseLabel" class="mt-1 font-serif text-3xl italic text-ink">Bersiap</p>
                <p x-show="phase !== 'done'" class="mt-1 text-xs text-muted">
                    <span x-text="secondsLeft" class="font-semibold text-ink">4</span> detik
                </p>
                <p x-show="phase === 'done'" x-cloak class="mt-1 text-xs font-medium text-primary">Terima kasih sudah jeda</p>
                <x-icon name="sprout" class="mt-2 h-4 w-4 text-primary" />
            </div>
        </div>

        <div class="mt-8 space-y-1.5 text-xs text-muted">
            <div class="flex items-center justify-center gap-2">
                <span>Siklus:</span>
                <span class="inline-flex items-center gap-1.5">
                    <template x-for="n in currentRhythm.cycles" :key="n">
                        <span x-bind:class="n <= currentCycle ? 'bg-primary' : 'bg-subtle/40'" class="h-1.5 w-1.5 rounded-full transition-colors"></span>
                    </template>
                </span>
                <span class="font-semibold text-ink" x-text="`${currentCycle} dari ${currentRhythm.cycles}`">1 dari 4</span>
            </div>
            <p>Durasi: <span x-text="formattedDuration" class="font-semibold text-ink">00:00</span> • Ritme disarankan 2–3 menit</p>
        </div>

        <div class="mt-7 flex flex-wrap items-center justify-center gap-3">
            <button type="button" x-on:click="toggleSession()" class="btn-primary min-w-56 py-3">
                <x-icon name="play" class="h-3.5 w-3.5" x-show="status !== 'running'" />
                <x-icon name="pause" class="h-3.5 w-3.5" x-show="status === 'running'" x-cloak />
                <span x-text="status === 'running' ? 'Jeda Sesi' : (status === 'paused' ? 'Lanjutkan' : 'Mulai Sesi')">Mulai Sesi</span>
            </button>
            <button type="button" x-on:click="endSession()" class="btn-ghost">
                <x-icon name="square" class="h-3.5 w-3.5" /> Akhiri
            </button>
        </div>

        <p x-show="sessionSaved" x-cloak class="mt-4 flex items-center justify-center gap-1.5 text-xs font-medium text-primary">
            <x-icon name="check" class="h-3.5 w-3.5" /> Sesi ini telah dicatat ke jejak pemulihan dirimu.
        </p>
    </div>

    <!-- Pilihan Ritme -->
    <div class="mx-auto mt-8 max-w-2xl text-center">
        <div class="flex flex-wrap items-center justify-center gap-2">
            <span class="mr-1 text-xs text-muted">Pilihan ritme:</span>
            <template x-for="(rhythm, idx) in rhythms" :key="rhythm.id">
                <button type="button" x-on:click="selectRhythm(idx)"
                        x-bind:class="selectedIndex === idx ? 'pill-tab-active' : 'pill-tab-idle'"
                        class="pill-tab" x-text="rhythm.name"></button>
            </template>
        </div>
        <p x-text="currentRhythm.description" class="mx-auto mt-3 max-w-md text-xs text-muted"></p>
    </div>

    <!-- Edukasi -->
    <div class="mx-auto mt-10 flex max-w-2xl items-start gap-3.5 rounded-2xl bg-secondary p-5 sm:p-6">
        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-white text-primary"><x-icon name="heart-handshake" class="h-4 w-4" /></span>
        <div>
            <h2 class="text-sm font-semibold text-ink">Mengapa mengambil jeda itu berharga?</h2>
            <p class="mt-1 text-[13px] leading-relaxed text-muted">
                Ketika pikiran terasa penuh atau dunia di sekitarmu terlalu cepat, mengalirkan oksigen secara sadar memberi sinyal pada sistem saraf bahwa saat ini kamu berada di tempat yang aman.
            </p>
        </div>
    </div>

    <p class="mx-auto mt-6 max-w-lg text-center text-[11px] leading-relaxed text-subtle">
        Latihan pernapasan sederhana ini bertujuan membantu relaksasi dan penenangan tubuh mandiri. Bukan pengganti intervensi medis, terapi, atau penanganan psikologis profesional.
    </p>
</div>
@endsection
