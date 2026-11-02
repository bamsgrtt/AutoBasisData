<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'AutoBasisData')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak] { display: none !important; }
        .terminal-box {
            background: #0d1117;
            color: #c9d1d9;
            font-family: 'Consolas', 'Monaco', 'Courier New', monospace;
            font-size: 13px;
            min-height: 100px;
            border-radius: 6px;
            padding: 12px 16px;
            overflow-x: auto;
        }
        .terminal-box table { border-collapse: collapse; width: 100%; }
        .terminal-box th { color: #58a6ff; border-bottom: 1px solid #30363d; padding: 4px 10px; text-align: left; }
        .terminal-box td { border-bottom: 1px solid #21262d; padding: 4px 10px; }
        .terminal-box .error { color: #f85149; }
        .terminal-box .success { color: #3fb950; }
        .terminal-box .muted { color: #484f58; }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">
    {{-- Navbar: 3 menu saja --}}
    <nav class="bg-white shadow-sm border-b sticky top-0 z-50">
        <div class="max-w-6xl mx-auto px-4 py-3 flex items-center justify-between">
            <div class="flex items-center gap-8">
                <a href="{{ route('home') }}" class="font-bold text-lg text-blue-600">AutoBasisData</a>
                <div class="flex items-center gap-5 text-sm font-medium">
                    <a href="{{ route('home') }}"
                       class="{{ request()->routeIs('home') ? 'text-blue-600' : 'text-gray-600 hover:text-blue-600' }}">
                        Dashboard
                    </a>
                    <a href="{{ route('practicals.index') }}"
                       class="{{ request()->routeIs('practicals.*') ? 'text-blue-600' : 'text-gray-600 hover:text-blue-600' }}">
                        Praktikum
                    </a>
                    <a href="{{ route('settings') }}"
                       class="{{ request()->routeIs('settings') ? 'text-blue-600' : 'text-gray-600 hover:text-blue-600' }}">
                        Pengaturan
                    </a>
                </div>
            </div>
            @if (session('selected_database'))
                <span class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded font-mono">
                    DB: {{ session('selected_database') }}
                </span>
            @endif
        </div>
    </nav>

    <main class="max-w-6xl mx-auto px-4 py-6">
        @if (session('success'))
            <div class="bg-green-50 border border-green-300 text-green-800 px-4 py-3 rounded mb-4 text-sm">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="bg-red-50 border border-red-300 text-red-800 px-4 py-3 rounded mb-4 text-sm">
                {{ session('error') }}
            </div>
        @endif

        @yield('content')
    </main>
</body>
</html>
