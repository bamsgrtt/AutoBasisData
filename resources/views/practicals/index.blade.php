@extends('layouts.app')

@section('title', 'Praktikum - AutoBasisData')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold">Praktikum</h1>
            <p class="text-gray-500 text-sm mt-1">Kelola instruksi dan tugas praktikum</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Form buat praktikum baru (sidebar kiri) --}}
        <div class="lg:col-span-1">
            <form action="{{ route('practicals.store') }}" method="POST" class="bg-white rounded shadow p-5 sticky top-20">
                @csrf
                <h2 class="font-bold text-gray-800 mb-4">+ Buat Praktikum Baru</h2>

                <div class="mb-3">
                    <label class="block text-xs font-semibold text-gray-600 mb-1" for="name">Judul</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required
                           placeholder="Modul 1 - DDL & DML"
                           class="border rounded w-full py-2 px-3 text-sm focus:outline-none focus:ring-1 focus:ring-blue-400">
                    @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="block text-xs font-semibold text-gray-600 mb-1" for="selected_database">Database</label>
                    <input type="text" id="selected_database" name="selected_database"
                           value="{{ old('selected_database', session('selected_database')) }}"
                           placeholder="nama_database"
                           class="border rounded w-full py-2 px-3 text-sm focus:outline-none focus:ring-1 focus:ring-blue-400">
                </div>

                <div class="mb-4">
                    <label class="block text-xs font-semibold text-gray-600 mb-1" for="instructions">Instruksi Praktikum</label>
                    <textarea id="instructions" name="instructions" rows="8" required
                              placeholder="1. Buat tabel mahasiswa&#10;2. Insert 3 data&#10;3. Tampilkan semua data"
                              class="border rounded w-full py-2 px-3 text-sm font-mono focus:outline-none focus:ring-1 focus:ring-blue-400">{{ old('instructions') }}</textarea>
                    @error('instructions')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-400 mt-1">Setiap baris → 1 tugas</p>
                </div>

                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-sm">
                    Simpan & Buat Tugas
                </button>
            </form>
        </div>

        {{-- Daftar praktikum --}}
        <div class="lg:col-span-2">
            @if ($practicals->isEmpty())
                <div class="bg-white rounded shadow p-8 text-center text-gray-500">
                    Belum ada praktikum. Isi form di samping untuk membuat yang pertama.
                </div>
            @else
                <div class="space-y-3">
                    @foreach ($practicals as $practical)
                        <a href="{{ route('practicals.show', $practical) }}"
                           class="block bg-white p-5 rounded shadow hover:shadow-md group">
                            <div class="flex items-center justify-between">
                                <h3 class="font-bold text-gray-800 group-hover:text-blue-600">{{ $practical->name }}</h3>
                                <span class="text-xs uppercase font-semibold px-2 py-1 rounded
                                    {{ $practical->status === 'completed' ? 'bg-green-100 text-green-700' :
                                       ($practical->status === 'in_progress' ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-100 text-gray-600') }}">
                                    {{ $practical->status }}
                                </span>
                            </div>
                            <div class="text-xs text-gray-500 mt-2 flex gap-4">
                                <span>DB: <strong class="text-gray-700">{{ $practical->selected_database ?? '-' }}</strong></span>
                                <span>Tugas: <strong class="text-gray-700">{{ $practical->tasks_count }}</strong></span>
                                <span>{{ $practical->created_at->diffForHumans() }}</span>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endsection
