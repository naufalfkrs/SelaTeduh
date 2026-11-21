<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\MoodLog;
use Carbon\Carbon;

class HomeController extends Controller
{
    public function index()
    {
        // 7 hari terakhir untuk widget ringkasan mood di Beranda
        $startDate = Carbon::today()->subDays(6);
        $logs = $this->scopeOwner(MoodLog::query())
            ->whereDate('logged_date', '>=', $startDate)
            ->orderByDesc('created_at')
            ->get()
            ->groupBy(fn ($item) => $item->logged_date->format('Y-m-d'));

        $lastSevenDays = collect();
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $key = $date->format('Y-m-d');
            $lastSevenDays->push([
                'day_short' => $date->locale('id')->isoFormat('ddd'),
                'date_num'  => $date->format('d'),
                'is_today'  => $date->isToday(),
                'mood'      => $logs->has($key) ? $logs[$key]->first()->mood : null,
            ]);
        }

        // 3 artikel unggulan untuk Beranda
        $featuredArticles = Article::orderByDesc('is_featured')
            ->latest()
            ->take(3)
            ->get();

        $articleCount = Article::count();
        $recordedDays = $lastSevenDays->whereNotNull('mood')->count();

        return view('home', compact('lastSevenDays', 'featuredArticles', 'articleCount', 'recordedDays'));
    }
}