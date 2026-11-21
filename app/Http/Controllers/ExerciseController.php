<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;

class ExerciseController extends Controller
{
    public function index()
    {
        $rhythms = [
            [
                'id'          => 'rileks',
                'name'        => 'Rileks (4-7-8)',
                'inhale'      => 4,
                'hold'        => 7,
                'exhale'      => 8,
                'cycles'      => 4,
                'description' => 'Membantu menurunkan ketegangan dan menenangkan sistem saraf sebelum tidur.',
            ],
            [
                'id'          => 'fokus',
                'name'        => 'Fokus Seimbang (4-4-4)',
                'inhale'      => 4,
                'hold'        => 4,
                'exhale'      => 4,
                'cycles'      => 4,
                'description' => 'Menstabilkan napas saat pikiran terasa penuh atau sulit berkonsentrasi.',
            ],
            [
                'id'          => 'sederhana',
                'name'        => 'Sederhana (4-4)',
                'inhale'      => 4,
                'hold'        => 0,
                'exhale'      => 4,
                'cycles'      => 5,
                'description' => 'Ritme ringan tanpa jeda tahan napas, cocok untuk pemula atau saat dada terasa sesak.',
            ],
        ];

        return view('exercise.index', compact('rhythms'));
    }

    public function complete(Request $request)
    {
        $validated = $request->validate([
            'rhythm_name' => 'required|string|max:100',
            'cycles'      => 'required|integer|min:1|max:50',
            'duration'    => 'required|string|max:30',
        ]);

        $completed = session('completed_breathing', []);
        array_unshift($completed, [
            'rhythm_name' => $validated['rhythm_name'],
            'cycles'      => $validated['cycles'],
            'duration'    => $validated['duration'],
            'finished_at' => Carbon::now()->locale('id')->isoFormat('D MMM, HH:mm'),
        ]);

        session(['completed_breathing' => array_slice($completed, 0, 10)]);

        return response()->json(['status' => 'ok']);
    }
}