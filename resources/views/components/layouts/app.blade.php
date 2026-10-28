<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'IT Inventory Management' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @fonts
</head>
<body class="bg-slate-50 text-slate-900 font-sans antialiased min-h-screen">

    <div class="relative min-h-screen lg:flex">
        <!-- Overlay mobile -->
        <div id="sidebar-backdrop" class="fixed inset-0 z-30 hidden bg-slate-950/50 lg:hidden"></div>

        <!-- Sidebar -->
        <aside id="sidebar"
            class="fixed inset-y-0 left-0 z-40 w-64 -translate-x-full transition-transform duration-200 ease-in-out bg-slate-900 text-slate-300 flex flex-col flex-shrink-0 border-r border-slate-800 lg:translate-x-0 lg:static lg:flex lg:min-h-screen">
            <!-- Logo -->
            <div class="h-16 flex items-center gap-3 px-6 border-b border-slate-800/80 bg-slate-950/40">
                <div class="w-9 h-9 rounded-xl bg-indigo-600 flex items-center justify-center text-white shadow-md shadow-indigo-500/20">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" />
                    </svg>
                </div>
                <div>
                    <span class="font-bold text-white tracking-tight text-sm block">IT Inventory Trans</span>
                    <span class="text-xs text-slate-400 block -mt-0.5">Asset Operations</span>
                </div>
            </div>

            <!-- Navigation Links -->
            <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto text-sm">
                <div class="px-3 py-1 text-xs font-semibold text-slate-400 uppercase tracking-wider">Utama</div>
                <a href="{{ route('dashboard') }}"
                    class="flex items-center gap-3 px-3 py-2 rounded-xl transition {{ request()->routeIs('dashboard') ? 'bg-indigo-600 text-white font-medium' : 'hover:bg-slate-800/60 hover:text-white' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    <span>Dashboard</span>
                </a>

                <div class="pt-3 px-3 py-1 text-xs font-semibold text-slate-400 uppercase tracking-wider">Manajemen Aset</div>
                <a href="{{ route('assets.index') }}"
                    class="flex items-center gap-3 px-3 py-2 rounded-xl transition {{ request()->routeIs('assets.*') ? 'bg-indigo-600 text-white font-medium' : 'hover:bg-slate-800/60 hover:text-white' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                    <span>Daftar Aset</span>
                </a>
                <a href="{{ route('movements.index') }}"
                    class="flex items-center gap-3 px-3 py-2 rounded-xl transition {{ request()->routeIs('movements.*') ? 'bg-indigo-600 text-white font-medium' : 'hover:bg-slate-800/60 hover:text-white' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                    </svg>
                    <span>Mutasi & Lokasi</span>
                </a>
                <a href="{{ route('loans.index') }}"
                    class="flex items-center gap-3 px-3 py-2 rounded-xl transition {{ request()->routeIs('loans.*') ? 'bg-indigo-600 text-white font-medium' : 'hover:bg-slate-800/60 hover:text-white' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Peminjaman</span>
                </a>
                <a href="{{ route('maintenance.index') }}"
                    class="flex items-center gap-3 px-3 py-2 rounded-xl transition {{ request()->routeIs('maintenance.*') ? 'bg-indigo-600 text-white font-medium' : 'hover:bg-slate-800/60 hover:text-white' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span>Pemeliharaan</span>
                </a>
                @can('view-reports')
                    <a href="{{ route('reports.index') }}"
                        class="flex items-center gap-3 px-3 py-2 rounded-xl transition {{ request()->routeIs('reports.*') ? 'bg-indigo-600 text-white font-medium' : 'hover:bg-slate-800/60 hover:text-white' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span>Laporan</span>
                    </a>
                @endcan

                @can('admin-only')
                    <div class="pt-3 px-3 py-1 text-xs font-semibold text-slate-400 uppercase tracking-wider">Administrasi</div>
                    <a href="{{ route('users.index') }}"
                        class="flex items-center gap-3 px-3 py-2 rounded-xl transition {{ request()->routeIs('users.*') ? 'bg-indigo-600 text-white font-medium' : 'hover:bg-slate-800/60 hover:text-white' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        <span>Pengguna & Role</span>
                    </a>
                    <a href="{{ route('categories.index') }}"
                        class="flex items-center gap-3 px-3 py-2 rounded-xl transition {{ request()->routeIs('categories.*') ? 'bg-indigo-600 text-white font-medium' : 'hover:bg-slate-800/60 hover:text-white' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                        </svg>
                        <span>Master Kategori</span>
                    </a>
                    <a href="{{ route('locations.index') }}"
                        class="flex items-center gap-3 px-3 py-2 rounded-xl transition {{ request()->routeIs('locations.*') ? 'bg-indigo-600 text-white font-medium' : 'hover:bg-slate-800/60 hover:text-white' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span>Master Lokasi</span>
                    </a>
                    <a href="{{ route('departments.index') }}"
                        class="flex items-center gap-3 px-3 py-2 rounded-xl transition {{ request()->routeIs('departments.*') ? 'bg-indigo-600 text-white font-medium' : 'hover:bg-slate-800/60 hover:text-white' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        <span>Departemen</span>
                    </a>
                    <a href="{{ route('audit-logs.index') }}"
                        class="flex items-center gap-3 px-3 py-2 rounded-xl transition {{ request()->routeIs('audit-logs.*') ? 'bg-indigo-600 text-white font-medium' : 'hover:bg-slate-800/60 hover:text-white' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Audit Log</span>
                    </a>
                @endcan
            </nav>

            <!-- Current User Info -->
            <div class="p-4 border-t border-slate-800/80 bg-slate-950/30">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-slate-700 flex items-center justify-center font-semibold text-white text-xs">
                        {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 2)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-white truncate">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-indigo-400 capitalize truncate">{{ auth()->user()->role?->label ?? 'Staff' }}</p>
                    </div>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" title="Keluar" class="p-1.5 rounded-lg text-slate-400 hover:text-rose-400 hover:bg-slate-800 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-w-0">
            <!-- Topbar -->
            <header class="h-16 bg-white border-b border-slate-200 px-4 sm:px-6 flex items-center justify-between sticky top-0 z-20">
                <div class="flex items-center gap-3">
                    <button id="sidebar-toggle"
                        type="button"
                        class="lg:hidden inline-flex items-center justify-center rounded-lg p-2 text-slate-600 hover:bg-slate-100 hover:text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>

                    <h2 class="text-lg font-semibold text-slate-800">{{ $header ?? 'Dashboard' }}</h2>
                </div>

                <div class="flex items-center gap-4">
                    <div class="text-xs text-slate-500 hidden sm:block">
                        {{ now()->translatedFormat('l, d F Y') }}
                    </div>
                </div>
            </header>

            <!-- Flash Messages -->
            <main class="flex-1 p-6 sm:p-8">
                @if (session('success'))
                    <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center gap-3">
                        <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm flex items-center gap-3">
                        <svg class="w-5 h-5 text-rose-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                @if (session('warning'))
                    <div class="mb-6 p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-sm flex items-center gap-3">
                        <svg class="w-5 h-5 text-amber-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <span>{{ session('warning') }}</span>
                    </div>
                @endif

                @if (session('info'))
                    <div class="mb-6 p-4 rounded-xl bg-blue-50 border border-blue-200 text-blue-800 text-sm flex items-center gap-3">
                        <svg class="w-5 h-5 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>{{ session('info') }}</span>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm">
                        <div class="flex items-center gap-2 mb-2 font-semibold">
                            <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            Terdapat {{ $errors->count() }} kesalahan pada formulir:
                        </div>
                        <ul class="list-disc list-inside space-y-1 text-xs">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{ $slot }}
            </main>
        </div>
    </div>
</body>
</html>
