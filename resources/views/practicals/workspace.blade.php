@extends('layouts.app')

@section('title', $practical->name . ' - Workspace')

@section('content')
{{-- ═══════════════════════════════════════════════════════════ --}}
{{-- BAGIAN ATAS: Header praktikum                              --}}
{{-- ═══════════════════════════════════════════════════════════ --}}
<div class="bg-white rounded shadow p-5 mb-4">
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('practicals.index') }}" class="text-xs text-blue-600 hover:underline">← Kembali ke Daftar</a>
            <h1 class="text-2xl font-bold mt-1">{{ $practical->name }}</h1>
        </div>
        <div class="flex items-center gap-3">
            <span class="text-xs uppercase font-semibold px-3 py-1 rounded
                {{ $practical->status === 'completed' ? 'bg-green-100 text-green-700' :
                   ($practical->status === 'in_progress' ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-100 text-gray-600') }}">
                {{ $practical->status }}
            </span>
            {{-- Tombol pengaturan koneksi (modal trigger) --}}
            <button onclick="document.getElementById('db-modal').classList.remove('hidden')"
                    class="text-xs bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold px-3 py-1.5 rounded flex items-center gap-1">
                ⚙ Koneksi DB
            </button>
        </div>
    </div>
    <div class="text-xs text-gray-500 mt-2 flex gap-4">
        <span>Database: <strong class="text-gray-700 font-mono" id="active-db">{{ $practical->selected_database ?? session('selected_database') ?? 'Belum dipilih' }}</strong></span>
        <span>Tugas: <strong>{{ $practical->tasks->count() }}</strong></span>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════ --}}
{{-- MODAL: Konfigurasi koneksi MySQL                           --}}
{{-- ═══════════════════════════════════════════════════════════ --}}
<div id="db-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md p-6 relative">
        <button onclick="document.getElementById('db-modal').classList.add('hidden')"
                class="absolute top-3 right-3 text-gray-400 hover:text-gray-700 text-lg">✕</button>
        <h3 class="font-bold text-lg mb-4">Konfigurasi Koneksi MySQL</h3>

        <form id="db-config-form" class="space-y-3">
            @csrf
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-xs font-semibold text-gray-600">Host</label>
                    <input type="text" name="host" value="127.0.0.1" class="border rounded w-full py-1.5 px-2 text-sm" required>
                </div>
                <div>
                    <label class="text-xs font-semibold text-gray-600">Port</label>
                    <input type="number" name="port" value="3306" class="border rounded w-full py-1.5 px-2 text-sm" required>
                </div>
            </div>
            <div>
                <label class="text-xs font-semibold text-gray-600">Username</label>
                <input type="text" name="username" value="root" class="border rounded w-full py-1.5 px-2 text-sm" required>
            </div>
            <div>
                <label class="text-xs font-semibold text-gray-600">Password</label>
                <input type="password" name="password" class="border rounded w-full py-1.5 px-2 text-sm">
            </div>
            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 rounded text-sm">
                Test Koneksi & Tampilkan Database
            </button>
        </form>

        <div id="db-test-result" class="mt-3 text-sm"></div>

        <form id="db-save-form" class="mt-3 hidden">
            @csrf
            <label class="text-xs font-semibold text-gray-600">Pilih Database:</label>
            <select name="selected_database" id="db-select" class="border rounded w-full py-1.5 px-2 text-sm mt-1" required>
                <option value="">-- Pilih --</option>
            </select>
            <button type="submit" class="w-full mt-2 bg-green-600 hover:bg-green-700 text-white font-semibold py-2 rounded text-sm">
                Simpan & Gunakan
            </button>
        </form>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════ --}}
{{-- BAGIAN INSTRUKSI: Accordion teks asli                      --}}
{{-- ═══════════════════════════════════════════════════════════ --}}
@if ($practical->instructions)
<details class="bg-white rounded shadow mb-4" open>
    <summary class="px-5 py-3 cursor-pointer font-bold text-sm text-gray-700 hover:text-blue-600 select-none">
        📋 Instruksi Praktikum
    </summary>
    <div class="px-5 pb-4">
        <div class="bg-gray-50 border rounded p-4 font-mono text-xs whitespace-pre-wrap text-gray-800 max-h-60 overflow-y-auto">{{ $practical->instructions }}</div>
    </div>
</details>
@endif

{{-- ═══════════════════════════════════════════════════════════ --}}
{{-- BAGIAN PENGERJAAN: Setiap tugas + SQL editor + terminal    --}}
{{-- ═══════════════════════════════════════════════════════════ --}}
<div class="space-y-4 mb-6">
    @forelse ($practical->tasks as $task)
    <div class="bg-white rounded shadow" id="task-{{ $task->id }}">
        <div class="px-5 py-3 border-b flex items-center justify-between">
            <div class="flex items-center gap-3">
                <span class="text-xs font-bold bg-gray-200 text-gray-700 px-2 py-0.5 rounded">#{{ $task->order }}</span>
                <span class="font-medium text-gray-800 text-sm">{{ $task->description }}</span>
            </div>
            <span class="task-status text-xs font-semibold px-2 py-0.5 rounded
                {{ $task->executions->where('is_correct', true)->count() > 0 ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}"
                id="status-{{ $task->id }}">
                {{ $task->executions->where('is_correct', true)->count() > 0 ? '✓ Selesai' : 'Belum' }}
            </span>
        </div>

        <div class="px-5 py-4">
            {{-- SQL Editor --}}
            <div class="flex gap-2 mb-2">
                <textarea name="sql" rows="2" placeholder="Tulis query SQL di sini..."
                          id="sql-{{ $task->id }}"
                          class="flex-1 border rounded py-2 px-3 font-mono text-sm focus:outline-none focus:ring-1 focus:ring-blue-400 resize-y">{{ $task->executions->first()->sql_command ?? '' }}</textarea>
                <button onclick="executeTask({{ $task->id }})"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 rounded text-sm whitespace-nowrap self-start">
                    ▶ Run
                </button>
            </div>

            {{-- Terminal output --}}
            <div class="terminal-box" id="terminal-{{ $task->id }}">
                @if ($task->executions->first())
                    <span class="muted">-- Eksekusi terakhir: {{ $task->executions->first()->executed_at?->diffForHumans() ?? '' }}</span>
                @else
                    <span class="muted">-- Ketik query lalu klik Run</span>
                @endif
            </div>

            {{-- Riwayat eksekusi (accordion) --}}
            @if ($task->executions->count() > 0)
            <details class="mt-2">
                <summary class="text-xs text-gray-500 cursor-pointer hover:text-blue-600 select-none">
                    Riwayat eksekusi ({{ $task->executions->count() }})
                </summary>
                <div class="mt-1 max-h-40 overflow-y-auto">
                    @foreach ($task->executions as $exec)
                    <div class="flex items-center gap-3 text-xs py-1 border-b border-gray-100">
                        <span class="{{ $exec->is_correct ? 'text-green-600' : 'text-red-500' }} font-semibold">
                            {{ $exec->is_correct ? '✓' : '✗' }}
                        </span>
                        <code class="text-gray-600 truncate flex-1">{{ \Illuminate\Support\Str::limit($exec->sql_command, 60) }}</code>
                        <span class="text-gray-400">{{ $exec->executed_at?->format('H:i:s') }}</span>
                    </div>
                    @endforeach
                </div>
            </details>
            @endif
        </div>
    </div>
    @empty
        <div class="bg-white rounded shadow p-8 text-center text-gray-500 text-sm">
            Tidak ada tugas. Pastikan instruksi praktikum berisi baris-baris tugas.
        </div>
    @endforelse
</div>

{{-- ═══════════════════════════════════════════════════════════ --}}
{{-- BAGIAN BAWAH: Ringkasan progres                            --}}
{{-- ═══════════════════════════════════════════════════════════ --}}
@if ($practical->tasks->count() > 0)
<div class="bg-white rounded shadow p-5">
    @php
        $totalTasks = $practical->tasks->count();
        $completedTasks = $practical->tasks->filter(fn($t) => $t->executions->where('is_correct', true)->count() > 0)->count();
        $pct = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;
    @endphp
    <div class="flex items-center justify-between mb-2">
        <h3 class="font-bold text-sm text-gray-700">Progres Pengerjaan</h3>
        <span class="text-sm font-bold {{ $pct === 100 ? 'text-green-600' : 'text-gray-600' }}">{{ $completedTasks }}/{{ $totalTasks }} ({{ $pct }}%)</span>
    </div>
    <div class="w-full bg-gray-200 rounded-full h-2">
        <div class="h-2 rounded-full {{ $pct === 100 ? 'bg-green-500' : 'bg-blue-500' }}" style="width: {{ $pct }}%"></div>
    </div>
</div>
@endif

{{-- ═══════════════════════════════════════════════════════════ --}}
{{-- JAVASCRIPT                                                 --}}
{{-- ═══════════════════════════════════════════════════════════ --}}
<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
const headers = { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json', 'Accept': 'application/json' };

// ── Koneksi DB: test ──
document.getElementById('db-config-form').onsubmit = async function(e) {
    e.preventDefault();
    const resultDiv = document.getElementById('db-test-result');
    resultDiv.innerHTML = '<span class="text-gray-500">Testing...</span>';

    const fd = new FormData(this);
    try {
        const res = await fetch('{{ route("database.config.test") }}', { method: 'POST', body: fd, headers: { 'X-Requested-With': 'XMLHttpRequest' } });
        const data = await res.json();
        if (data.status === 'success') {
            resultDiv.innerHTML = '<span class="text-green-600 font-semibold">Koneksi berhasil!</span>';
            const select = document.getElementById('db-select');
            select.innerHTML = '<option value="">-- Pilih --</option>';
            data.databases.forEach(db => select.innerHTML += `<option value="${db}">${db}</option>`);
            document.getElementById('db-save-form').classList.remove('hidden');
        } else {
            resultDiv.innerHTML = `<span class="text-red-600">Error: ${data.message}</span>`;
        }
    } catch (err) {
        resultDiv.innerHTML = `<span class="text-red-600">${err.message}</span>`;
    }
};

// ── Koneksi DB: save ──
document.getElementById('db-save-form').onsubmit = async function(e) {
    e.preventDefault();
    const db = document.getElementById('db-select').value;
    if (!db) return;
    const fd = new FormData(this);
    await fetch('{{ route("database.save") }}', { method: 'POST', body: fd, headers: { 'X-Requested-With': 'XMLHttpRequest' } });
    document.getElementById('active-db').innerText = db;
    document.getElementById('db-modal').classList.add('hidden');
};

// ── Execute SQL per task ──
async function executeTask(taskId) {
    const textarea = document.getElementById('sql-' + taskId);
    const terminal = document.getElementById('terminal-' + taskId);
    const sql = textarea.value.trim();

    if (!sql) { terminal.innerHTML = '<span class="error">Query kosong.</span>'; return; }

    terminal.innerHTML = '<span class="muted">Executing...</span>';

    try {
        // Execute query
        const fd = new FormData();
        fd.append('sql_command', sql);
        fd.append('_token', csrfToken);
        const res = await fetch('{{ route("sql.execute") }}', { method: 'POST', body: fd, headers: { 'X-Requested-With': 'XMLHttpRequest' } });
        const data = await res.json();

        if (data.error) {
            terminal.innerHTML = `<span class="error">ERROR: ${data.error}</span>`;
            // Save failed execution
            await fetch('{{ route("execution.save") }}', {
                method: 'POST', headers,
                body: JSON.stringify({ task_id: taskId, sql_command: sql, output: data.error, is_correct: false })
            });
        } else {
            const rows = data.result;
            let html = `<span class="success">OK — ${rows.length} row(s) returned</span>\n`;

            if (rows.length > 0) {
                const keys = Object.keys(rows[0]);
                html += '<table><thead><tr>' + keys.map(k => `<th>${k}</th>`).join('') + '</tr></thead><tbody>';
                rows.forEach(row => {
                    html += '<tr>' + keys.map(k => `<td>${row[k] ?? 'NULL'}</td>`).join('') + '</tr>';
                });
                html += '</tbody></table>';
            }
            terminal.innerHTML = html;

            // Save successful execution
            await fetch('{{ route("execution.save") }}', {
                method: 'POST', headers,
                body: JSON.stringify({ task_id: taskId, sql_command: sql, output: JSON.stringify(rows), is_correct: true })
            });

            // Update status badge
            document.getElementById('status-' + taskId).className = 'task-status text-xs font-semibold px-2 py-0.5 rounded bg-green-100 text-green-700';
            document.getElementById('status-' + taskId).innerText = '✓ Selesai';
        }
    } catch (err) {
        terminal.innerHTML = `<span class="error">Request failed: ${err.message}</span>`;
    }
}
</script>
@endsection
