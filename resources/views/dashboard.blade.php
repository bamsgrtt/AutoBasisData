@extends('layouts.app')

@section('title', 'Dashboard - AutoBasisData')

@section('content')
    <div class="mb-8">
        <h1 class="text-3xl font-bold">Dashboard</h1>
        <p class="text-gray-500 mt-1">Ringkasan aktivitas praktikum</p>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-3 gap-4 mb-8">
        <div class="bg-white rounded shadow p-5 text-center">
            <div class="text-3xl font-bold text-gray-800">{{ $stats['total'] }}</div>
            <div class="text-xs text-gray-500 mt-1 uppercase">Total Praktikum</div>
        </div>
        <div class="bg-white rounded shadow p-5 text-center">
            <div class="text-3xl font-bold text-yellow-600">{{ $stats['in_progress'] }}</div>
            <div class="text-xs text-gray-500 mt-1 uppercase">Sedang Dikerjakan</div>
        </div>
        <div class="bg-white rounded shadow p-5 text-center">
            <div class="text-3xl font-bold text-green-600">{{ $stats['completed'] }}</div>
            <div class="text-xs text-gray-500 mt-1 uppercase">Selesai</div>
        </div>
    </div>

    {{-- Recent practicals --}}
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-bold">Praktikum Terbaru</h2>
        <a href="{{ route('practicals.index') }}" class="text-sm text-blue-600 hover:underline">Lihat semua →</a>
    </div>

    @if ($practicals->isEmpty())
        <div class="bg-white rounded shadow p-8 text-center text-gray-500">
            Belum ada praktikum.
            <a href="{{ route('practicals.index') }}" class="text-blue-600 underline">Buat praktikum pertama</a>.
        </div>
    @else
        <div class="grid gap-3">
            @foreach ($practicals as $practical)
                <a href="{{ route('practicals.show', $practical) }}"
                   class="bg-white p-4 rounded shadow hover:shadow-md flex items-center justify-between group">
                    <div>
                        <h3 class="font-bold text-gray-800 group-hover:text-blue-600">{{ $practical->name }}</h3>
                        <div class="text-xs text-gray-500 mt-1 flex gap-4">
                            <span>DB: <strong>{{ $practical->selected_database ?? '-' }}</strong></span>
                            <span>Tugas: <strong>{{ $practical->tasks_count }}</strong></span>
                        </div>
                    </div>
                    <span class="text-xs uppercase font-semibold px-2 py-1 rounded
                        {{ $practical->status === 'completed' ? 'bg-green-100 text-green-700' :
                           ($practical->status === 'in_progress' ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-100 text-gray-600') }}">
                        {{ $practical->status }}
                    </span>
                </a>
            @endforeach
        </div>
    @endif
@endsection
