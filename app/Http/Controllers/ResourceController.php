<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;

class ResourceController extends Controller
{
    public function index(Request $request)
    {
        $search   = $request->query('q');
        $category = $request->query('kategori', 'Semua Bacaan');
        $sort     = $request->query('urutkan', 'terhangat');

        $categories = [
            'Semua Bacaan',
            'Kecemasan',
            'Stres & Sekolah',
            'Self-care',
            'Hubungan',
        ];

        // Artikel pilihan redaksi di bagian atas
        $featuredArticle = Article::where('is_featured', true)->latest()->first()
            ?? Article::latest()->first();

        // Query daftar bacaan lainnya
        $query = Article::query();

        if ($featuredArticle && ! $search && $category === 'Semua Bacaan') {
            $query->where('id', '!=', $featuredArticle->id);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('summary', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        if ($category && $category !== 'Semua Bacaan') {
            $query->where('category', $category);
        }

        match ($sort) {
            'tercepat' => $query->orderBy('read_time', 'asc'),
            'terbaru'  => $query->latest(),
            default    => $query->orderByDesc('is_featured')->latest(),
        };

        $articles = $query->get();

        // Jumlah bacaan per kategori untuk label tab
        $categoryCounts = Article::selectRaw('category, COUNT(*) as total')
            ->groupBy('category')
            ->pluck('total', 'category');
        $categoryCounts['Semua Bacaan'] = $categoryCounts->sum();

        return view('resources.index', compact(
            'featuredArticle',
            'articles',
            'categories',
            'category',
            'search',
            'sort',
            'categoryCounts'
        ));
    }

    public function show(string $slug)
    {
        $article = Article::where('slug', $slug)->firstOrFail();

        $relatedArticles = Article::where('id', '!=', $article->id)
            ->where('category', $article->category)
            ->take(3)
            ->get();

        return view('resources.show', compact('article', 'relatedArticles'));
    }
}