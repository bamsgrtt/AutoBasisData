@extends('layouts.app')

@section('title', 'Dashboard - AutoBasisData')

@section('content')
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
            <p class="text-gray-500 mt-1">Ringkasan aktivitas dan progres praktikum basis data</p>
        </div>
        <a href="{{ route('practicals.index') }}"
           class="bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm px-4 py-2 rounded shadow-sm inline-flex items-center gap-1.5 transition-colors">
            + Praktikum Baru
        </a>
    </div>

    {{-- Statistik --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5 text-center">
            <div class="text-3xl font-bold text-gray-800">{{ $stats['total'] }}</div>
            <div class="text-xs font-semibold text-gray-500 mt-1 uppercase tracking-wide">Total Praktikum</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5 text-center">
            <div class="text-3xl font-bold text-gray-500">{{ $stats['draft'] ?? 0 }}</div>
            <div class="text-xs font-semibold text-gray-500 mt-1 uppercase tracking-wide">Draft</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5 text-center">
            <div class="text-3xl font-bold text-amber-600">{{ $stats['in_progress'] }}</div>
            <div class="text-xs font-semibold text-gray-500 mt-1 uppercase tracking-wide">Sedang Dikerjakan</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5 text-center">
            <div class="text-3xl font-bold text-emerald-600">{{ $stats['completed'] }}</div>
            <div class="text-xs font-semibold text-gray-500 mt-1 uppercase tracking-wide">Selesai</div>
        </div>
    </div>

    {{-- Praktikum Terbaru --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between mb-5 border-b pb-3">
            <h2 class="text-lg font-bold text-gray-800">Praktikum Terbaru</h2>
            <a href="{{ route('practicals.index') }}" class="text-sm font-semibold text-blue-600 hover:text-blue-800 hover:underline">
                Lihat Semua ({{ $stats['total'] }}) →
            </a>
        </div>

        @if ($practicals->isEmpty())
            <div class="py-12 text-center">
                <p class="text-gray-500 text-sm">Belum ada praktikum yang dibuat.</p>
                <a href="{{ route('practicals.index') }}"
                   class="mt-3 inline-block text-sm font-semibold text-blue-600 hover:underline">
                    Buat praktikum pertama sekarang →
                </a>
            </div>
        @else
            <div class="divide-y divide-gray-100">
                @foreach ($practicals as $practical)
                    <div class="py-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 group">
                        <div class="flex-1">
                            <div class="flex items-center gap-2.5">
                                <a href="{{ route('practicals.show', $practical) }}"
                                   class="font-bold text-gray-900 group-hover:text-blue-600 transition-colors">
                                    {{ $practical->name }}
                                </a>
                                <span class="text-xs uppercase font-semibold px-2 py-0.5 rounded
                                    {{ $practical->status === 'completed'
                                        ? 'bg-emerald-100 text-emerald-700'
                                        : ($practical->status === 'in_progress'
                                            ? 'bg-amber-100 text-amber-700'
                                            : 'bg-gray-100 text-gray-600') }}">
                                    {{ $practical->status }}
                                </span>
                            </div>
                            <div class="text-xs text-gray-500 mt-1.5 flex flex-wrap gap-x-4 gap-y-1">
                                <span>DB: <strong class="text-gray-700 font-mono">{{ $practical->selected_database ?? '-' }}</strong></span>
                                <span>Tugas: <strong class="text-gray-700">{{ $practical->tasks_count }}</strong></span>
                                <span>Dibuat: <span class="text-gray-600">{{ $practical->created_at->diffForHumans() }}</span></span>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 self-start sm:self-center">
                            <a href="{{ route('practicals.show', $practical) }}"
                               class="text-xs bg-blue-50 hover:bg-blue-100 text-blue-700 font-semibold px-3 py-1.5 rounded transition-colors">
                                Buka Workspace →
                            </a>

                            @if ($practical->status === 'draft')
                                <form action="{{ route('practicals.destroy', $practical) }}" method="POST"
                                      onsubmit="return confirm('Hapus draft praktikum \'{{ addslashes($practical->name) }}\' beserta seluruh tugasnya?')"
                                      class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="text-xs bg-red-50 hover:bg-red-100 text-red-600 font-semibold px-2.5 py-1.5 rounded transition-colors"
                                            title="Hapus draft">
                                        Hapus
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection
