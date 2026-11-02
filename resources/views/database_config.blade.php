@extends('layouts.app')

@section('title', 'Pengaturan - AutoBasisData')

@section('content')
    <div class="mb-6">
        <h1 class="text-3xl font-bold">Pengaturan</h1>
        <p class="text-gray-500 text-sm mt-1">Konfigurasi koneksi database default</p>
    </div>

    <div class="max-w-lg">
        <form id="db-config-form" class="bg-white rounded shadow p-6 mb-4">
            @csrf
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Host</label>
                    <input type="text" name="host" value="127.0.0.1" class="border rounded w-full py-2 px-3 text-sm focus:outline-none" required>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Port</label>
                    <input type="number" name="port" value="3306" class="border rounded w-full py-2 px-3 text-sm focus:outline-none" required>
                </div>
            </div>
            <div class="mb-4">
                <label class="block text-xs font-semibold text-gray-600 mb-1">Username</label>
                <input type="text" name="username" value="root" class="border rounded w-full py-2 px-3 text-sm focus:outline-none" required>
            </div>
            <div class="mb-4">
                <label class="block text-xs font-semibold text-gray-600 mb-1">Password</label>
                <input type="password" name="password" class="border rounded w-full py-2 px-3 text-sm focus:outline-none">
            </div>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded text-sm">
                Test Koneksi
            </button>
        </form>

        <div id="db-test-result" class="mb-4"></div>

        <form id="db-save-form" class="bg-white rounded shadow p-6 hidden">
            @csrf
            <label class="block text-xs font-semibold text-gray-600 mb-1">Pilih Database Default:</label>
            <select name="selected_database" id="selected_database" required class="border rounded w-full py-2 px-3 text-sm focus:outline-none mb-3">
                <option value="">-- Pilih --</option>
            </select>
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded text-sm">
                Simpan
            </button>
        </form>

        @if (session('selected_database'))
            <div class="mt-4 bg-green-50 border border-green-200 rounded p-3 text-sm text-green-800">
                Database aktif saat ini: <strong class="font-mono">{{ session('selected_database') }}</strong>
            </div>
        @endif
    </div>

    <script>
        document.getElementById('db-config-form').onsubmit = async function(e) {
            e.preventDefault();
            const resultDiv = document.getElementById('db-test-result');
            resultDiv.innerHTML = '<p class="text-gray-500 text-sm">Testing...</p>';

            try {
                const res = await fetch('{{ route("database.config.test") }}', {
                    method: 'POST',
                    body: new FormData(this),
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await res.json();

                if (data.status === 'success') {
                    resultDiv.innerHTML = '<p class="text-green-600 font-semibold text-sm">Koneksi berhasil!</p>';
                    const select = document.getElementById('selected_database');
                    select.innerHTML = '<option value="">-- Pilih --</option>';
                    data.databases.forEach(db => select.innerHTML += `<option value="${db}">${db}</option>`);
                    document.getElementById('db-save-form').classList.remove('hidden');
                } else {
                    resultDiv.innerHTML = `<p class="text-red-600 text-sm">Error: ${data.message}</p>`;
                }
            } catch (err) {
                resultDiv.innerHTML = `<p class="text-red-600 text-sm">${err.message}</p>`;
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
