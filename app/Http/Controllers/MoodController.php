<?php

namespace App\Http\Controllers;

use App\Models\JournalEntry;
use App\Models\MoodLog;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MoodController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->query('filter', 'semua'); // semua | hanya_mood | ada_catatan

        // 1. Kalender Ritme Minggu Ini (Senin - Minggu)
        $startOfWeek = Carbon::now()->startOfWeek(Carbon::MONDAY);
        $endOfWeek = Carbon::now()->endOfWeek(Carbon::SUNDAY);

        $weeklyLogs = $this->scopeOwner(MoodLog::query())
            ->whereBetween('logged_date', [$startOfWeek->toDateString(), $endOfWeek->toDateString()])
            ->orderByDesc('created_at')
            ->get();

        $groupedByDate = $weeklyLogs->groupBy(fn ($log) => $log->logged_date->format('Y-m-d'));

        $weekDays = collect();
        for ($i = 0; $i < 7; $i++) {
            $date = $startOfWeek->copy()->addDays($i);
            $key = $date->format('Y-m-d');
            $weekDays->push([
                'day_name' => $date->locale('id')->isoFormat('ddd'),
                'day_num'  => $date->format('d'),
                'is_today' => $date->isToday(),
                'is_future' => $date->isFuture() && ! $date->isToday(),
                'log'      => $groupedByDate->has($key) ? $groupedByDate[$key]->first() : null,
            ]);
        }

        // 2. Narasi lembut otomatis berdasarkan pola mood minggu ini
        $weeklyNarrative = $this->generateWeeklyNarrative($weeklyLogs);

        // 3. Daftar riwayat catatan terdahulu + filter tab
        $logsQuery = $this->scopeOwner(MoodLog::query())
            ->orderByDesc('logged_date')
            ->orderByDesc('created_at');

        if ($filter === 'hanya_mood') {
            $logsQuery->where(fn ($q) => $q->whereNull('note')->orWhere('note', ''));
        } elseif ($filter === 'ada_catatan') {
            $logsQuery->whereNotNull('note')->where('note', '!=', '');
        }

        $moodLogs = $logsQuery->paginate(10)->withQueryString();

        // 4. Widget Sidebar: Aktivitas yang Telah Selesai (Jurnal & Latihan Napas terbaru)
        $recentJournals = $this->scopeOwner(JournalEntry::query())
            ->latest()
            ->take(2)
            ->get();

        $recentBreathing = collect(session('completed_breathing', []))->take(2);

        return view('mood.index', compact(
            'weekDays',
            'weeklyNarrative',
            'moodLogs',
            'filter',
            'recentJournals',
            'recentBreathing'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'mood'        => 'required|in:Tenang,Biasa Saja,Lelah,Resah,Kewalahan',
            'triggers'    => 'nullable|array',
            'triggers.*'  => 'string|max:50',
            'note'        => 'nullable|string|max:2000',
            'logged_date' => 'nullable|date',
        ]);

        MoodLog::create([
            ...$this->ownerAttributes(),
            'mood'        => $validated['mood'],
            'triggers'    => $validated['triggers'] ?? [],
            'note'        => $validated['note'] ?? null,
            'logged_date' => $validated['logged_date'] ?? Carbon::today()->toDateString(),
        ]);

        return back()->with('success', 'Catatan rasamu hari ini sudah tersimpan dengan aman.');
    }

    public function update(Request $request, MoodLog $moodLog)
    {
        $this->authorizeOwnership($moodLog);

        $validated = $request->validate([
            'mood'       => 'required|in:Tenang,Biasa Saja,Lelah,Resah,Kewalahan',
            'triggers'   => 'nullable|array',
            'triggers.*' => 'string|max:50',
            'note'       => 'nullable|string|max:2000',
        ]);

        $moodLog->update([
            'mood'     => $validated['mood'],
            'triggers' => $validated['triggers'] ?? [],
            'note'     => $validated['note'] ?? null,
        ]);

        return back()->with('success', 'Catatan perasaan berhasil diperbarui.');
    }

    public function destroy(MoodLog $moodLog)
    {
        $this->authorizeOwnership($moodLog);
        $moodLog->delete();

        return back()->with('success', 'Catatan perasaan telah dihapus.');
    }

    private function generateWeeklyNarrative($weeklyLogs): string
    {
        if ($weeklyLogs->isEmpty()) {
            return 'Belum ada catatan pekan ini. Tidak apa-apa, mulailah menyapa dirimu saat kamu sudah siap.';
        }

        $heavyCount = $weeklyLogs->whereIn('mood', ['Lelah', 'Resah', 'Kewalahan'])->count();
        $calmCount = $weeklyLogs->whereIn('mood', ['Tenang', 'Biasa Saja'])->count();

        if ($heavyCount > 0 && $calmCount > 0) {
            return 'Pekan ini dinamikamu cukup terasa—ada momen lelah atau resah, namun kamu juga berhasil menemukan ruang tenang kembali. Tubuh dan pikiranmu sudah berupaya sebaik mungkin.';
        }

        if ($heavyCount > $calmCount) {
            return 'Pekan ini terasa cukup padat dan menguras energi. Sangat wajar jika ritmemu melambat; beri izin pada dirimu untuk beristirahat tanpa rasa bersalah.';
        }

        return 'Ritmemu pekan ini cenderung stabil dan tenang. Terus rawat jeda-jeda kecil yang membuat hatimu merasa lapang.';
    }
}