<div id="bantuan" class="scroll-mt-24 rounded-[1.75rem] bg-secondary p-6 sm:p-10">
    <span class="chip bg-white text-primary">
        <x-icon name="shield-check" class="h-3.5 w-3.5" /> Ruang Aman &amp; Dukungan Nyata
    </span>
    <h2 class="heading-lg mt-4 max-w-xl">Kamu tidak perlu menanggung semuanya sendirian</h2>
    <p class="mt-3 max-w-2xl text-sm leading-relaxed text-muted">
        Jika perasaanmu terasa terlalu berat atau kamu butuh seseorang untuk diajak bicara sekarang, ada bantuan yang aman dan terpercaya di Indonesia.
    </p>

    <div class="mt-8 grid grid-cols-1 gap-4 md:grid-cols-3">
        @php
            $helpLines = [
                ['tag' => 'Resmi RI', 'icon' => 'phone', 'title' => 'Layanan SEJIWA',
                 'desc' => 'Layanan Sahabat Anak & Remaja (Telepon 119 ext. 8). Konseling gratis terpadu dari Kementerian PPPA.',
                 'link' => 'Hubungi 119 (ext. 8)', 'href' => 'tel:119'],
                ['tag' => 'Psikologis', 'icon' => 'messages', 'title' => 'Yayasan Pulih',
                 'desc' => 'Lembaga nirlaba yang berfokus pada pemulihan trauma dan konseling psikologis yang ramah anak, adil gender, dan inklusif.',
                 'link' => 'Akses Konseling Pulih', 'href' => 'https://yayasanpulih.org'],
                ['tag' => 'Teman Sebaya', 'icon' => 'users', 'title' => 'Konselor Sebaya Berlisensi',
                 'desc' => 'Sesi bercerita dan mendengarkan tanpa stigma bersama pemuda terlatih untuk mendampingi masa-masa sulitmu.',
                 'link' => 'Daftar Sesi Berjenjang', 'href' => route('resource.index').'#bantuan'],
            ];
        @endphp

        @foreach ($helpLines as $line)
            <div class="flex flex-col rounded-2xl bg-white p-5">
                <div class="flex items-start justify-between gap-3">
                    <span class="text-[10px] font-bold uppercase tracking-[0.12em] text-subtle">{{ $line['tag'] }}</span>
                    <x-icon :name="$line['icon']" class="h-4 w-4 text-primary" />
                </div>
                <h3 class="mt-2 font-serif text-xl text-ink">{{ $line['title'] }}</h3>
                <p class="mt-1.5 mb-4 grow text-xs leading-relaxed text-muted">{{ $line['desc'] }}</p>
                <a href="{{ $line['href'] }}" @if (str_starts_with($line['href'], 'http')) target="_blank" rel="noopener" @endif
                   class="inline-flex items-center gap-1 text-xs font-semibold text-primary hover:underline underline-offset-4">
                    {{ $line['link'] }} <x-icon name="arrow-up-right" class="h-3.5 w-3.5" />
                </a>
            </div>
        @endforeach
    </div>

    <div class="mt-6 flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
        <p class="flex max-w-xl items-start gap-2 text-xs leading-relaxed text-muted">
            <x-icon name="help" class="mt-0.5 h-4 w-4 shrink-0 text-primary" />
            <span><strong class="text-ink">Catatan Non-Darurat:</strong> SelaTeduh adalah ruang jeda dan edukasi diri. Untuk situasi krisis atau darurat medis, segera hubungi layanan kesehatan terdekat atau orang dewasa yang kamu percaya.</span>
        </p>
        <a href="{{ route('resource.index') }}#bantuan" class="btn-primary shrink-0">
            Pelajari Panduan Mencari Bantuan <x-icon name="arrow-right" class="h-3.5 w-3.5" />
        </a>
    </div>
</div>
