<?php

namespace App\Http\Controllers;

use App\Models\JournalEntry;
use App\Models\MoodLog;
use Carbon\Carbon;
use Illuminate\Http\Request;

class JournalController extends Controller
{
    private array $prompts = [
        'Apa satu beban pikiran yang paling ingin kamu taruh dan istirahatkan malam ini?',
        'Hal kecil apa hari ini yang membuat dadamu terasa sedikit lebih lapang?',
        'Jika perasaanmu saat ini bisa berbicara, apa yang paling ingin ia sampaikan padamu?',
        'Apa satu hal yang tidak perlu kamu selesaikan hari ini dan boleh menunggu esok?',
        'Kalimat lembut apa yang ingin kamu ucapkan untuk dirimu sendiri setelah melewati hari ini?',
    ];

    public function index(Request $request)
    {
        $search = $request->query('q');
        $filter = $request->query('filter', 'semua'); // semua | minggu_ini | ada_pemantik

        // Ambil pemantik acak (berubah jika klik "Ganti Pemantik")
        $currentPrompt = $request->boolean('bebas')
            ? null
            : $this->prompts[array_rand($this->prompts)];

        // Ambil mood terakhir hari ini untuk badge konteks di atas editor
        $todayMood = $this->scopeOwner(MoodLog::query())
            ->whereDate('logged_date', Carbon::today())
            ->latest()
            ->first();

        // Query Sidebar: Lembar Terdahulu
        $entriesQuery = $this->scopeOwner(JournalEntry::query())->latest();

        if ($search) {
            $entriesQuery->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        if ($filter === 'minggu_ini') {
            $entriesQuery->where('created_at', '>=', Carbon::now()->startOfWeek());
        } elseif ($filter === 'ada_pemantik') {
            $entriesQuery->whereNotNull('prompt')->where('prompt', '!=', '');
        }

        $entries = $entriesQuery->get();
        $totalEntries = $this->scopeOwner(JournalEntry::query())->count();

        // Jika pengguna mengklik "Baca" pada salah satu lembar terdahulu
        $selectedEntry = null;
        if ($request->filled('baca')) {
            $selectedEntry = $this->scopeOwner(JournalEntry::query())
                ->where('id', $request->query('baca'))
                ->first();
        }

        return view('journal.index', compact(
            'currentPrompt',
            'todayMood',
            'entries',
            'totalEntries',
            'search',
            'filter',
            'selectedEntry'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'prompt'  => 'nullable|string|max:255',
            'title'   => 'nullable|string|max:150',
            'content' => 'required|string|max:20000',
        ]);

        $plainText = trim(strip_tags($validated['content']));
        $wordCount = $plainText === '' ? 0 : count(preg_split('/\s+/', $plainText));

        JournalEntry::create([
            ...$this->ownerAttributes(),
            'prompt'     => $validated['prompt'] ?? null,
            'title'      => $validated['title'] ?? null,
            'content'    => $validated['content'],
            'word_count' => $wordCount,
        ]);

        return redirect()->route('journal.index')
            ->with('success', 'Tulisanmu sudah disimpan di ruang privat.');
    }

    public function destroy(JournalEntry $journalEntry)
    {
        $this->authorizeOwnership($journalEntry);
        $journalEntry->delete();

        return redirect()->route('journal.index')
            ->with('success', 'Lembar jurnal berhasil dihapus.');
    }

    public function exportTxt()
    {
        $entries = $this->scopeOwner(JournalEntry::query())->latest()->get();

        $lines = [
            '========================================',
            'SALINAN JURNAL REFLEKSI - SELATEDUH',
            'Diunduh pada: ' . Carbon::now()->locale('id')->isoFormat('D MMMM YYYY, HH:mm') . ' WIB',
            '========================================',
            '',
        ];

        foreach ($entries as $entry) {
            $lines[] = 'Tanggal : ' . $entry->created_at->locale('id')->isoFormat('dddd, D MMMM YYYY • HH:mm') . ' WIB';
            if ($entry->title) {
                $lines[] = 'Judul   : ' . $entry->title;
            }
            if ($entry->prompt) {
                $lines[] = 'Pemantik: "' . $entry->prompt . '"';
            }
            $lines[] = '----------------------------------------';
            $lines[] = $entry->content;
            $lines[] = '';
            $lines[] = '========================================';
            $lines[] = '';
        }

        $filename = 'Jurnal-SelaTeduh-' . Carbon::today()->format('Y-m-d') . '.txt';

        return response()->streamDownload(function () use ($lines) {
            echo implode("\n", $lines);
        }, $filename, ['Content-Type' => 'text/plain; charset=UTF-8']);
    }
}