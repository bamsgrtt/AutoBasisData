<?php

namespace App\Http\Controllers;

use App\Models\Execution;
use App\Models\Practical;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\View\View;

class PracticalController extends Controller
{
    public function dashboard(): View
    {
        $practicals = Practical::withCount('tasks')->latest()->take(5)->get();
        $stats = [
            'total' => Practical::count(),
            'draft' => Practical::where('status', 'draft')->count(),
            'in_progress' => Practical::where('status', 'in_progress')->count(),
            'completed' => Practical::where('status', 'completed')->count(),
        ];

        return view('dashboard', compact('practicals', 'stats'));
    }

    public function index(): View
    {
        $practicals = Practical::withCount('tasks')->latest()->get();

        return view('practicals.index', compact('practicals'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'instructions' => 'required|string',
            'selected_database' => 'nullable|string|max:255',
        ]);

        $user = User::first() ?? User::factory()->create([
            'name' => 'Default Lecturer',
            'email' => 'admin@autobasisdata.local',
        ]);

        $practical = Practical::create([
            'name' => $validated['name'],
            'instructions' => $validated['instructions'],
            'selected_database' => $validated['selected_database'] ?: session('selected_database'),
            'user_id' => $user->id,
            'status' => 'draft',
        ]);

        // ponytail: parser regex sederhana split nomor atau baris baru; ganti AI parser jika instruksi kompleks
        $lines = preg_split('/\r\n|\r|\n/', $validated['instructions']);
        $order = 1;

        foreach ($lines as $line) {
            $cleaned = trim(preg_replace('/^\d+[\.\)\-]\s*/', '', trim($line)));
            if ($cleaned === '') {
                continue;
            }

            Task::create([
                'practical_id' => $practical->id,
                'title' => 'Tugas '.$order,
                'description' => $cleaned,
                'order' => $order++,
            ]);
        }

        return redirect()->route('practicals.show', $practical)
            ->with('success', 'Instruksi praktikum berhasil disimpan.');
    }

    public function show(Practical $practical): View
    {
        $practical->load(['tasks.executions' => fn ($q) => $q->latest()]);

        if ($practical->selected_database && ! session('selected_database')) {
            session(['selected_database' => $practical->selected_database]);
        }

        return view('practicals.workspace', compact('practical'));
    }

    public function saveExecution(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'task_id' => 'required|exists:tasks,id',
            'sql_command' => 'required|string',
            'output' => 'nullable|string',
            'is_correct' => 'boolean',
        ]);

        $execution = Execution::create([
            'task_id' => $validated['task_id'],
            'sql_command' => $validated['sql_command'],
            'output' => $validated['output'] ?? null,
            'is_correct' => $validated['is_correct'] ?? false,
            'executed_at' => now(),
        ]);

        return response()->json(['status' => 'saved', 'execution' => $execution]);
    }

    public function destroy(Practical $practical): RedirectResponse
    {
        if ($practical->status !== 'draft') {
            return redirect()->back()->with('error', 'Hanya praktikum dengan status draft yang dapat dihapus.');
        }

        $practical->delete();

        return redirect()->route('practicals.index')
            ->with('success', 'Draft praktikum berhasil dihapus.');
    }
}
