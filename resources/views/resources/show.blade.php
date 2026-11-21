@extends('layouts.app')

@section('title', $article->title . ' — SelaTeduh')

@section('content')
@php
    $paragraphs = preg_split("/\n\s*\n/", trim($article->content));
@endphp

<article class="container-page pt-8">
    <div class="flex flex-wrap items-center justify-between gap-3 text-xs text-muted">
        <a href="{{ route('resource.index') }}" class="inline-flex items-center gap-1.5 font-medium transition hover:text-primary">
            <x-icon name="arrow-left" class="h-3.5 w-3.5" /> Kembali ke Perpustakaan Rasa
        </a>
        <span class="chip bg-white text-muted ring-1 ring-line">
            <span class="h-1.5 w-1.5 rounded-full bg-primary"></span> {{ $article->category }}
        </span>
    </div>

    <header class="mx-auto mt-10 max-w-3xl text-center">
        <span class="chip-soft"><x-icon name="clock" class="h-3 w-3" /> {{ $article->read_time }} menit baca</span>
        <h1 class="mt-5 font-serif text-4xl leading-tight text-ink sm:text-5xl">{{ $article->title }}</h1>
        <p class="mx-auto mt-4 max-w-2xl font-serif text-lg italic leading-relaxed text-muted">{{ $article->summary }}</p>
        <div class="mt-6 flex items-center justify-center gap-3 text-xs">
            <span class="flex h-9 w-9 items-center justify-center rounded-full bg-secondary text-primary"><x-icon name="heart" class="h-4 w-4" /></span>
            <div class="text-left">
                <p class="font-semibold text-ink">{{ $article->reviewer ?? 'Ditinjau Tim SelaTeduh' }}</p>
                <p class="text-subtle">Diperbarui {{ $article->updated_at?->locale('id')->isoFormat('D MMMM YYYY') }}</p>
            </div>
        </div>
    </header>

    @if ($article->image_path)
        <img src="{{ $article->image_path }}" alt="Ilustrasi untuk {{ $article->title }}"
             class="mx-auto mt-10 aspect-[16/8] w-full max-w-4xl rounded-[1.75rem] object-cover">
    @endif

    <div class="mx-auto mt-10 max-w-2xl space-y-5 text-[16px] leading-[1.85] text-ink">
        @foreach ($paragraphs as $paragraph)
            @php $lines = preg_split("/\n/", $paragraph); @endphp
            @if (count($lines) > 1 && preg_match('/^(\d+\.|•|-)\s/', $lines[1] ?? ''))
                <p>{{ $lines[0] }}</p>
                <ul class="space-y-2 rounded-2xl bg-secondary p-5 text-[15px]">
                    @foreach (array_slice($lines, 1) as $line)
                        <li class="flex gap-2.5"><x-icon name="leaf" class="mt-1.5 h-3.5 w-3.5 shrink-0 text-primary" /><span>{{ preg_replace('/^(\d+\.|•|-)\s*/', '', $line) }}</span></li>
                    @endforeach
                </ul>
            @else
                <p class="whitespace-pre-line">{{ $paragraph }}</p>
            @endif
        @endforeach

        <div class="!mt-10 flex items-start gap-3 rounded-2xl border border-line bg-white p-4 text-xs leading-relaxed text-muted">
            <x-icon name="info" class="mt-0.5 h-4 w-4 shrink-0 text-primary" />
            Bacaan ini bersifat edukasi umum dan bukan pengganti konsultasi profesional. Jika yang kamu rasakan terasa berat dan berkepanjangan, bicarakan dengan konselor atau psikolog terdekat.
        </div>

        <div class="flex flex-wrap items-center justify-center gap-3 pt-2">
            <a href="{{ route('journal.index') }}" class="btn-primary"><x-icon name="pen" class="h-3.5 w-3.5" /> Tuliskan refleksimu</a>
            <a href="{{ route('exercise.index') }}" class="btn-secondary"><x-icon name="wind" class="h-3.5 w-3.5" /> Ambil napas sejenak</a>
        </div>
    </div>
</article>

@if ($relatedArticles->isNotEmpty())
    <section class="container-page mt-16">
        <h2 class="heading-md mb-5">Bacaan terkait</h2>
        <div class="grid grid-cols-1 gap-5 md:grid-cols-3">
            @foreach ($relatedArticles as $related)
                @include('resources.partials.card', ['article' => $related])
            @endforeach
        </div>
    </section>
@endif

<section class="container-page mt-16">
    @include('resources.partials.help-banner')
</section>
@endsection
