@extends('layouts.app')

@section('title', $practical->name . ' - Workspace')

@section('content')
{{-- ═══════════════════════════════════════════════════════════ --}}
{{-- BAGIAN ATAS: Header praktikum                              --}}
{{-- ═══════════════════════════════════════════════════════════ --}}
<div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5 mb-4">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <a href="{{ route('practicals.index') }}" class="text-xs text-blue-600 hover:underline inline-flex items-center gap-1 mb-1">
                ← Kembali ke Daftar Praktikum
            </a>
            <h1 class="text-2xl font-bold text-gray-900">{{ $practical->name }}</h1>
        </div>
        <div class="flex items-center gap-3">
            <span class="text-xs uppercase font-semibold px-3 py-1 rounded
                {{ $practical->status === 'completed' ? 'bg-emerald-100 text-emerald-700' :
                   ($practical->status === 'in_progress' ? 'bg-amber-100 text-amber-700' : 'bg-gray-100 text-gray-600') }}">
                {{ $practical->status }}
            </span>

            {{-- Tombol pengaturan koneksi (modal trigger) --}}
            <button onclick="document.getElementById('db-modal').classList.remove('hidden')"
                    class="text-xs bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold px-3 py-1.5 rounded flex items-center gap-1.5 transition-colors">
                ⚙ Koneksi DB
            </button>
        </div>
    </div>
    <div class="text-xs text-gray-500 mt-2 flex flex-wrap gap-4">
        <span>Database: <strong class="text-gray-700 font-mono" id="active-db">{{ $practical->selected_database ?? session('selected_database') ?? 'Belum dipilih' }}</strong></span>
        <span>Total Tugas: <strong class="text-gray-700">{{ $practical->tasks->count() }}</strong></span>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════ --}}
{{-- MODAL: Konfigurasi koneksi MySQL                           --}}
{{-- ═══════════════════════════════════════════════════════════ --}}
<div id="db-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40"
     onclick="if (event.target === this) this.classList.add('hidden')">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md p-6 relative">
        <button onclick="document.getElementById('db-modal').classList.add('hidden')"
                class="absolute top-3 right-3 text-gray-400 hover:text-gray-700 text-lg">✕</button>
        <h3 class="font-bold text-lg mb-4 text-gray-800">Konfigurasi Koneksi MySQL</h3>

        <form id="db-config-form" class="space-y-3">
            @csrf
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Host</label>
                    <input type="text" name="host" value="127.0.0.1" class="border rounded w-full py-1.5 px-2 text-sm font-mono focus:outline-none focus:ring-1 focus:ring-blue-400" required>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Port</label>
                    <input type="number" name="port" value="3306" class="border rounded w-full py-1.5 px-2 text-sm font-mono focus:outline-none focus:ring-1 focus:ring-blue-400" required>
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Username</label>
                <input type="text" name="username" value="root" class="border rounded w-full py-1.5 px-2 text-sm font-mono focus:outline-none focus:ring-1 focus:ring-blue-400" required>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Password</label>
                <input type="password" name="password" placeholder="(kosong jika tanpa password)" class="border rounded w-full py-1.5 px-2 text-sm font-mono focus:outline-none focus:ring-1 focus:ring-blue-400">
            </div>
            <button type="submit" id="modal-test-btn"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 rounded text-sm transition-colors">
                Test Koneksi & Tampilkan Database
            </button>
        </form>

        <div id="db-test-result" class="mt-3 text-sm"></div>

        <form id="db-save-form" class="mt-3 hidden">
            @csrf
            <label class="block text-xs font-semibold text-gray-600 mb-1">Pilih Database:</label>
            <select name="selected_database" id="db-select" class="border rounded w-full py-1.5 px-2 text-sm font-mono focus:outline-none focus:ring-1 focus:ring-blue-400 mb-2" required>
                <option value="">-- Pilih --</option>
            </select>
            <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-2 rounded text-sm transition-colors">
                Simpan & Gunakan
            </button>
        </form>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════ --}}
{{-- BAGIAN INSTRUKSI: Accordion teks asli                      --}}
{{-- ═══════════════════════════════════════════════════════════ --}}
@if ($practical->instructions)
<details class="bg-white rounded-lg shadow-sm border border-gray-100 mb-4" open>
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
    @php
        $latestExec = $task->executions->first();
        $isCompleted = $task->executions->where('is_correct', true)->isNotEmpty();
    @endphp
    <div class="bg-white rounded-lg shadow-sm border border-gray-100" id="task-{{ $task->id }}">
        <div class="px-5 py-3 border-b flex items-center justify-between">
            <div class="flex items-center gap-3">
                <span class="text-xs font-bold bg-gray-200 text-gray-700 px-2 py-0.5 rounded">#{{ $task->order }}</span>
                <span class="font-medium text-gray-800 text-sm">{{ $task->description }}</span>
            </div>
            <span class="task-status text-xs font-semibold px-2 py-0.5 rounded
                {{ $isCompleted ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}"
                id="status-{{ $task->id }}">
                {{ $isCompleted ? '✓ Selesai' : 'Belum' }}
            </span>
        </div>

        <div class="px-5 py-4">
            {{-- SQL Editor --}}
            <div class="flex gap-2 mb-2">
                <textarea name="sql" rows="2" placeholder="Tulis query SQL di sini... (Ctrl+Enter untuk menjalankan)"
                          id="sql-{{ $task->id }}"
                          onkeydown="if ((event.ctrlKey || event.metaKey) && event.key === 'Enter') { event.preventDefault(); executeTask({{ $task->id }}); }"
                          class="auto-resize flex-1 border rounded py-2 px-3 font-mono text-sm focus:outline-none focus:ring-1 focus:ring-blue-400 resize-none min-h-[42px] max-h-[300px]">{{ $latestExec->sql_command ?? '' }}</textarea>
                <button type="button" id="btn-run-{{ $task->id }}" onclick="executeTask({{ $task->id }})"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 rounded text-sm whitespace-nowrap self-start py-2 transition-colors">
                    ▶ Run
                </button>
            </div>

            {{-- Terminal output --}}
            <div class="terminal-box" id="terminal-{{ $task->id }}">
                @if ($latestExec)
                    <div class="text-xs mb-1 {{ $latestExec->is_correct ? 'success' : 'error' }}">
                        {{ $latestExec->is_correct ? '✓ Terakhir berhasil' : '✗ Terakhir gagal' }} ({{ $latestExec->executed_at?->diffForHumans() }})
                    </div>
                    @if ($latestExec->output)
                        @php $decoded = json_decode($latestExec->output, true); @endphp
                        @if (is_array($decoded) && count($decoded) > 0)
                            <table>
                                <thead>
                                    <tr>
                                        @foreach (array_keys($decoded[0]) as $col)
                                            <th>{{ $col }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($decoded as $row)
                                        <tr>
                                            @foreach ($row as $val)
                                                <td>{{ $val !== null ? $val : 'NULL' }}</td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @elseif (is_array($decoded) && count($decoded) === 0)
                            <span class="success">OK — 0 row(s) returned</span>
                        @else
                            <pre class="{{ $latestExec->is_correct ? 'success' : 'error' }} whitespace-pre-wrap font-mono text-xs">{{ $latestExec->output }}</pre>
                        @endif
                    @endif
                @else
                    <span class="muted">-- Ketik query lalu klik Run (atau Ctrl+Enter)</span>
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
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-8 text-center text-gray-500 text-sm">
            Tidak ada tugas. Pastikan instruksi praktikum berisi baris-baris tugas.
        </div>
    @endforelse
</div>

{{-- ═══════════════════════════════════════════════════════════ --}}
{{-- BAGIAN BAWAH: Ringkasan progres                            --}}
{{-- ═══════════════════════════════════════════════════════════ --}}
@if ($practical->tasks->count() > 0)
<div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5">
    @php
        $totalTasks = $practical->tasks->count();
        $completedTasks = $practical->tasks->filter(fn($t) => $t->executions->where('is_correct', true)->count() > 0)->count();
        $pct = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;
    @endphp
    <div class="flex items-center justify-between mb-2">
        <h3 class="font-bold text-sm text-gray-700">Progres Pengerjaan</h3>
        <span id="progress-bar-text" class="text-sm font-bold {{ $pct === 100 ? 'text-green-600' : 'text-gray-600' }}">
            {{ $completedTasks }}/{{ $totalTasks }} ({{ $pct }}%)
        </span>
    </div>
    <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
        <div id="progress-bar-fill" class="h-2 rounded-full transition-all duration-300 {{ $pct === 100 ? 'bg-green-500' : 'bg-blue-500' }}" style="width: {{ $pct }}%"></div>
    </div>
</div>
@endif

{{-- ═══════════════════════════════════════════════════════════ --}}
{{-- JAVASCRIPT                                                 --}}
{{-- ═══════════════════════════════════════════════════════════ --}}
<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
const headers = { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json', 'Accept': 'application/json' };

// ── Update Progress Bar ──
function updateProgress() {
    const allStatuses = document.querySelectorAll('.task-status');
    const total = allStatuses.length;
    let completed = 0;
    allStatuses.forEach(el => {
        if (el.textContent.includes('Selesai')) {
            completed++;
        }
    });
    const pct = total > 0 ? Math.round((completed / total) * 100) : 0;
    const bar = document.getElementById('progress-bar-fill');
    const label = document.getElementById('progress-bar-text');
    if (bar && label) {
        bar.style.width = pct + '%';
        label.innerText = `${completed}/${total} (${pct}%)`;
        if (pct === 100) {
            bar.className = 'h-2 rounded-full bg-green-500 transition-all duration-300';
            label.className = 'text-sm font-bold text-green-600';
        } else {
            bar.className = 'h-2 rounded-full bg-blue-500 transition-all duration-300';
            label.className = 'text-sm font-bold text-gray-600';
        }
    }
}

// ── Koneksi DB: test ──
document.getElementById('db-config-form').onsubmit = async function(e) {
    e.preventDefault();
    const resultDiv = document.getElementById('db-test-result');
    const btn = document.getElementById('modal-test-btn');
    resultDiv.innerHTML = '<span class="text-gray-500">Testing...</span>';
    btn.disabled = true;
    btn.classList.add('opacity-50', 'cursor-not-allowed');

    const fd = new FormData(this);
    try {
        const res = await fetch('{{ route("database.config.test") }}', { method: 'POST', body: fd, headers: { 'X-Requested-With': 'XMLHttpRequest' } });
        const data = await res.json();
        if (data.status === 'success') {
            resultDiv.innerHTML = '<span class="text-green-600 font-semibold">✓ Koneksi berhasil!</span>';
            const select = document.getElementById('db-select');
            select.innerHTML = '<option value="">-- Pilih --</option>';
            data.databases.forEach(db => {
                const opt = document.createElement('option');
                opt.value = db;
                opt.textContent = db;
                select.appendChild(opt);
            });
            document.getElementById('db-save-form').classList.remove('hidden');
        } else {
            resultDiv.innerHTML = `<span class="text-red-600">✗ Error: ${data.message}</span>`;
        }
    } catch (err) {
        resultDiv.innerHTML = `<span class="text-red-600">✗ ${err.message}</span>`;
    } finally {
        btn.disabled = false;
        btn.classList.remove('opacity-50', 'cursor-not-allowed');
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
    const runBtn = document.getElementById('btn-run-' + taskId);
    const sql = textarea.value.trim();

    if (!sql) {
        terminal.innerHTML = '<span class="error">Query kosong. Silakan tulis query SQL.</span>';
        return;
    }

    if (runBtn) {
        runBtn.disabled = true;
        runBtn.classList.add('opacity-50', 'cursor-not-allowed');
        runBtn.innerText = '...';
    }

    terminal.innerHTML = '<span class="muted">Menjalankan query...</span>';

    try {
        const fd = new FormData();
        fd.append('sql_command', sql);
        fd.append('_token', csrfToken);
        const res = await fetch('{{ route("sql.execute") }}', { method: 'POST', body: fd, headers: { 'X-Requested-With': 'XMLHttpRequest' } });
        const data = await res.json();

        if (data.error) {
            terminal.innerHTML = `<span class="error">ERROR: ${data.error}</span>`;
            await fetch('{{ route("execution.save") }}', {
                method: 'POST', headers,
                body: JSON.stringify({ task_id: taskId, sql_command: sql, output: data.error, is_correct: false })
            });
        } else {
            const rows = data.result;
            const count = Array.isArray(rows) ? rows.length : 0;
            let html = `<span class="success">OK — ${count} row(s) returned</span>\n`;

            if (Array.isArray(rows) && rows.length > 0) {
                const keys = Object.keys(rows[0]);
                html += '<table><thead><tr>' + keys.map(k => `<th>${k}</th>`).join('') + '</tr></thead><tbody>';
                rows.forEach(row => {
                    html += '<tr>' + keys.map(k => `<td>${row[k] !== null && row[k] !== undefined ? row[k] : 'NULL'}</td>`).join('') + '</tr>';
                });
                html += '</tbody></table>';
            }
            terminal.innerHTML = html;

            await fetch('{{ route("execution.save") }}', {
                method: 'POST', headers,
                body: JSON.stringify({ task_id: taskId, sql_command: sql, output: JSON.stringify(rows), is_correct: true })
            });

            // Update badge status
            const badge = document.getElementById('status-' + taskId);
            if (badge) {
                badge.className = 'task-status text-xs font-semibold px-2 py-0.5 rounded bg-green-100 text-green-700';
                badge.innerText = '✓ Selesai';
            }

            updateProgress();
        }
    } catch (err) {
        terminal.innerHTML = `<span class="error">Request failed: ${err.message}</span>`;
    } finally {
        if (runBtn) {
            runBtn.disabled = false;
            runBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            runBtn.innerText = '▶ Run';
        }
    }
}
</script>
@endsection
