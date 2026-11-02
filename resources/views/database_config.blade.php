@extends('layouts.app')

@section('title', 'Pengaturan - AutoBasisData')

@section('content')
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Pengaturan</h1>
        <p class="text-gray-500 text-sm mt-1">Konfigurasi koneksi database MySQL untuk eksekusi SQL</p>
    </div>

    <div class="max-w-lg space-y-4">
        @if (session('selected_database'))
            <div class="bg-emerald-50 border border-emerald-200 rounded-lg p-4 text-sm text-emerald-800 flex items-center justify-between">
                <div>
                    <span class="text-xs uppercase font-semibold text-emerald-600 block">Database Aktif</span>
                    <strong class="font-mono text-base text-emerald-900">{{ session('selected_database') }}</strong>
                </div>
                <span class="text-xs bg-emerald-100 text-emerald-700 px-2 py-1 rounded font-semibold">Tersambung</span>
            </div>
        @else
            <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 text-sm text-amber-800">
                <span class="font-semibold">Perhatian:</span> Belum ada database yang dipilih. Uji koneksi dan pilih database di bawah agar query SQL dapat dieksekusi.
            </div>
        @endif

        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
            <h2 class="font-bold text-base text-gray-800 mb-4">Uji Koneksi MySQL</h2>
            <form id="db-config-form" class="space-y-4">
                @csrf
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1" for="host">Host</label>
                        <input type="text" id="host" name="host" value="127.0.0.1"
                               class="border rounded w-full py-2 px-3 text-sm focus:outline-none focus:ring-1 focus:ring-blue-400 font-mono" required>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1" for="port">Port</label>
                        <input type="number" id="port" name="port" value="3306"
                               class="border rounded w-full py-2 px-3 text-sm focus:outline-none focus:ring-1 focus:ring-blue-400 font-mono" required>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1" for="username">Username</label>
                    <input type="text" id="username" name="username" value="root"
                           class="border rounded w-full py-2 px-3 text-sm focus:outline-none focus:ring-1 focus:ring-blue-400 font-mono" required>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1" for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="(kosong jika tanpa password)"
                           class="border rounded w-full py-2 px-3 text-sm focus:outline-none focus:ring-1 focus:ring-blue-400 font-mono">
                </div>
                <button type="submit" id="btn-test-db"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded text-sm transition-colors">
                    Test Koneksi & Ambil Daftar Database
                </button>
            </form>

            <div id="db-test-result" class="mt-4"></div>
        </div>

        <form id="db-save-form" class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 hidden">
            @csrf
            <h2 class="font-bold text-base text-gray-800 mb-3">Pilih Database Default</h2>
            <p class="text-xs text-gray-500 mb-3">Database yang dipilih akan digunakan untuk eksekusi perintah SQL di workspace.</p>
            <select name="selected_database" id="selected_database" required
                    class="border rounded w-full py-2 px-3 text-sm focus:outline-none focus:ring-1 focus:ring-blue-400 font-mono mb-4">
                <option value="">-- Pilih Database --</option>
            </select>
            <button type="submit"
                    class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded text-sm transition-colors">
                Simpan & Jadikan Default
            </button>
        </form>
    </div>

    <script>
        document.getElementById('db-config-form').onsubmit = async function(e) {
            e.preventDefault();
            const resultDiv = document.getElementById('db-test-result');
            const submitBtn = document.getElementById('btn-test-db');
            resultDiv.innerHTML = '<p class="text-gray-500 text-sm">Menguji koneksi...</p>';
            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-50', 'cursor-not-allowed');

            try {
                const res = await fetch('{{ route("database.config.test") }}', {
                    method: 'POST',
                    body: new FormData(this),
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await res.json();

                if (data.status === 'success') {
                    resultDiv.innerHTML = '<div class="p-3 bg-emerald-50 border border-emerald-200 rounded text-emerald-800 text-sm font-semibold">✓ Koneksi berhasil! Pilih database di bawah.</div>';
                    const select = document.getElementById('selected_database');
                    select.innerHTML = '<option value="">-- Pilih Database --</option>';
                    data.databases.forEach(db => {
                        const opt = document.createElement('option');
                        opt.value = db;
                        opt.textContent = db;
                        if (db === '{{ session("selected_database") }}') {
                            opt.selected = true;
                        }
                        select.appendChild(opt);
                    });
                    document.getElementById('db-save-form').classList.remove('hidden');
                } else {
                    resultDiv.innerHTML = `<div class="p-3 bg-red-50 border border-red-200 rounded text-red-700 text-sm">✗ Gagal: ${data.message}</div>`;
                }
            } catch (err) {
                resultDiv.innerHTML = `<div class="p-3 bg-red-50 border border-red-200 rounded text-red-700 text-sm">✗ Error: ${err.message}</div>`;
            } finally {
                submitBtn.disabled = false;
                submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            }
        };

        document.getElementById('db-save-form').onsubmit = async function(e) {
            e.preventDefault();
            const fd = new FormData(this);
            await fetch('{{ route("database.save") }}', { method: 'POST', body: fd, headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            location.reload();
        };
    </script>
@endsection
