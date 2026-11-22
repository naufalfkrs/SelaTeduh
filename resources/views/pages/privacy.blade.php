@extends('layouts.app')

@section('title', 'Privasi & Etika — SelaTeduh')

@section('content')
<div class="container-page pt-10">

    <!-- Privasi & Etika -->
    <section id="privasi" class="max-w-3xl">
        <span class="eyebrow">Privasi &amp; Etika</span>
        <h1 class="heading-xl mt-3">Ceritamu sepenuhnya milikmu.</h1>
        <p class="mt-4 text-[15px] leading-relaxed text-muted">
            SelaTeduh dibuat untuk menemani, bukan mengawasi. Halaman ini menjelaskan apa saja yang disimpan, di mana disimpan, dan apa yang tidak akan pernah kami lakukan.
        </p>
    </section>

    <!-- Keamanan Data -->
    <section id="keamanan" class="mt-14 scroll-mt-24">
        <span class="eyebrow">Keamanan Data</span>
        <h2 class="heading-lg mt-2">Di mana catatanmu disimpan?</h2>
        <div class="mt-8 grid grid-cols-1 gap-4 md:grid-cols-3">
            <div class="card p-6">
                <span class="chip-soft"><x-icon name="user" class="h-3.5 w-3.5" /> Tanpa akun</span>
                <h3 class="mt-4 font-serif text-xl text-ink">Mode Tamu</h3>
                <p class="mt-2 text-sm leading-relaxed text-muted">
                    Kamu bisa langsung mencatat tanpa daftar. Catatan tamu ditandai kode acak di cookie peramban, sehingga hanya bisa dibuka dari peramban yang sama.
                    Jika cookie dihapus, catatan tamu tidak bisa dibuka lagi.
                </p>
            </div>
            <div class="card p-6">
                <span class="chip-soft"><x-icon name="lock" class="h-3.5 w-3.5" /> Dengan akun</span>
                <h3 class="mt-4 font-serif text-xl text-ink">Akun Pribadi</h3>
                <p class="mt-2 text-sm leading-relaxed text-muted">
                    Akun bersifat opsional. Dengan akun, catatan bisa dibuka lintas perangkat. Saat kamu daftar atau masuk, catatan tamumu otomatis dipindahkan ke akunmu.
                </p>
            </div>
            <div class="card p-6">
                <span class="chip-peach"><x-icon name="shield" class="h-3.5 w-3.5" /> Paling privat</span>
                <h3 class="mt-4 font-serif text-xl text-ink">Peramban Saja</h3>
                <p class="mt-2 text-sm leading-relaxed text-muted">
                    Pilih "Simpan di peramban saja" saat check-in di Beranda agar catatan tidak dikirim ke server sama sekali. Draf jurnal juga disimpan otomatis di peramban.
                </p>
            </div>
        </div>

        <div class="mt-6 grid grid-cols-1 gap-x-10 gap-y-4 rounded-[var(--radius-card)] bg-secondary p-6 text-sm leading-relaxed text-muted md:grid-cols-2">
            <p class="flex gap-2"><x-icon name="check" class="mt-0.5 h-4 w-4 shrink-0 text-primary" /> Hanya kamu yang bisa membaca, mengubah, dan menghapus catatanmu.</p>
            <p class="flex gap-2"><x-icon name="check" class="mt-0.5 h-4 w-4 shrink-0 text-primary" /> Tulisan jurnal disaring agar tidak bisa menyisipkan skrip berbahaya.</p>
            <p class="flex gap-2"><x-icon name="check" class="mt-0.5 h-4 w-4 shrink-0 text-primary" /> Data perasaanmu tidak dijual dan tidak dipakai untuk iklan bertarget.</p>
            <p class="flex gap-2"><x-icon name="check" class="mt-0.5 h-4 w-4 shrink-0 text-primary" /> Kamu bisa mengunduh salinan jurnal (.txt) kapan saja dari halaman Jurnal.</p>
        </div>
    </section>

    <!-- Batasan Layanan -->
    <section id="batasan" class="mt-14 scroll-mt-24 flex flex-col gap-4 rounded-[1.75rem] bg-primary p-6 text-white sm:p-10 md:flex-row md:items-center md:justify-between">
        <div class="max-w-2xl">
            <span class="text-[11px] font-bold uppercase tracking-[0.12em] text-white/70">Batasan Layanan</span>
            <h2 class="mt-2 font-serif text-3xl">SelaTeduh bukan pengganti tenaga profesional.</h2>
            <p class="mt-3 text-sm leading-relaxed text-white/80">
                SelaTeduh adalah kawan refleksi mandiri. Kami tidak mendiagnosis dan tidak memberikan terapi. Jika kamu merasa tidak aman atau berada dalam krisis,
                segera hubungi layanan darurat atau tenaga profesional.
            </p>
        </div>
        <a href="{{ route('page.help') }}#darurat" class="btn shrink-0 bg-white text-primary hover:bg-secondary">
            Lihat kontak darurat <x-icon name="arrow-right" class="h-3.5 w-3.5" />
        </a>
    </section>
</div>
@endsection
