<x-layouts.app>
    <x-slot:header>Ringkasan Operasional & Dashboard Inventory</x-slot:header>

    <div class="space-y-8">
        <!-- Top KPI Cards -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
                <span class="text-xs font-medium text-slate-500 uppercase tracking-wider block">Total Aset</span>
                <span class="text-2xl font-bold text-slate-900 mt-2 block">{{ $totalAssets }}</span>
                <span class="text-xs text-slate-400 mt-1 block">Tercatat di sistem</span>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
                <span class="text-xs font-medium text-emerald-600 uppercase tracking-wider block">Tersedia</span>
                <span class="text-2xl font-bold text-emerald-600 mt-2 block">{{ $availableAssets }}</span>
                <span class="text-xs text-slate-400 mt-1 block">Siap dialokasikan</span>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
                <span class="text-xs font-medium text-blue-600 uppercase tracking-wider block">Ditetapkan</span>
                <span class="text-2xl font-bold text-blue-600 mt-2 block">{{ $assignedAssets }}</span>
                <span class="text-xs text-slate-400 mt-1 block">Digunakan pegawai</span>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
                <span class="text-xs font-medium text-amber-600 uppercase tracking-wider block">Dipinjam</span>
                <span class="text-2xl font-bold text-amber-600 mt-2 block">{{ $borrowedAssets }}</span>
                <span class="text-xs text-slate-400 mt-1 block">Peminjaman aktif</span>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
                <span class="text-xs font-medium text-rose-600 uppercase tracking-wider block">Perbaikan</span>
                <span class="text-2xl font-bold text-rose-600 mt-2 block">{{ $maintenanceAssets }}</span>
                <span class="text-xs text-slate-400 mt-1 block">Maintenance aktif</span>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
                <span class="text-xs font-medium text-slate-500 uppercase tracking-wider block">Total Nilai Aset</span>
                <span class="text-lg font-bold text-slate-800 mt-2 block truncate">Rp {{ number_format($totalCost, 0, ',', '.') }}</span>
                <span class="text-xs text-slate-400 mt-1 block">Akumulasi perolehan</span>
            </div>
        </div>

        <!-- 2 Column Overview -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Peminjaman Aktif -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-semibold text-slate-800 text-sm flex items-center gap-2">
                        <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Peminjaman Aset Berjalan</span>
                    </h3>
                    <a href="{{ route('loans.index') }}" class="text-xs text-indigo-600 hover:text-indigo-900 font-medium">Lihat Semua &rarr;</a>
                </div>
                <div class="space-y-3">
                    @forelse ($activeLoans as $loan)
                        <div class="flex items-center justify-between p-3.5 rounded-xl bg-slate-50 border border-slate-100 text-sm">
                            <div>
                                <span class="font-medium text-slate-900 block">{{ $loan->asset?->name }}</span>
                                <span class="text-xs text-slate-500 block">Peminjam: {{ $loan->borrower?->name }} &bull; Tag: {{ $loan->asset?->asset_tag }}</span>
                            </div>
                            <div class="text-right">
                                <span class="text-xs font-medium px-2.5 py-1 rounded-full bg-amber-100 text-amber-800">
                                    Due: {{ $loan->due_at ? $loan->due_at->translatedFormat('d M Y') : 'Tanpa batas' }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <p class="text-slate-400 text-xs py-6 text-center">Tidak ada peminjaman yang sedang aktif.</p>
                    @endforelse
                </div>
            </div>

            <!-- Maintenance Aktif -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-semibold text-slate-800 text-sm flex items-center gap-2">
                        <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        </svg>
                        <span>Perbaikan & Servis Aktif</span>
                    </h3>
                    <a href="{{ route('maintenance.index') }}" class="text-xs text-indigo-600 hover:text-indigo-900 font-medium">Lihat Semua &rarr;</a>
                </div>
                <div class="space-y-3">
                    @forelse ($activeMaintenance as $maint)
                        <div class="flex items-center justify-between p-3.5 rounded-xl bg-slate-50 border border-slate-100 text-sm">
                            <div>
                                <span class="font-medium text-slate-900 block">{{ $maint->asset?->name }}</span>
                                <span class="text-xs text-slate-500 block">Isu: {{ Str::limit($maint->issue, 35) }} &bull; Teknisi: {{ $maint->technician?->name }}</span>
                            </div>
                            <div class="text-right">
                                <span class="text-xs font-medium px-2.5 py-1 rounded-full bg-rose-100 text-rose-800">
                                    Mulai: {{ $maint->started_at->translatedFormat('d M Y') }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <p class="text-slate-400 text-xs py-6 text-center">Tidak ada perangkat dalam masa perbaikan.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Sebaran Kategori & Aktivitas Terbaru -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Distribusi Kategori -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                <h3 class="font-semibold text-slate-800 text-sm mb-4">Sebaran Aset Berdasarkan Kategori</h3>
                <div class="space-y-3">
                    @foreach ($categories as $cat)
                        <div>
                            <div class="flex justify-between text-xs font-medium text-slate-700 mb-1">
                                <span>{{ $cat->name }}</span>
                                <span>{{ $cat->assets_count }} unit ({{ $totalAssets > 0 ? round(($cat->assets_count / $totalAssets) * 100) : 0 }}%)</span>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                                <div class="bg-indigo-600 h-2 rounded-full" style="width: {{ $totalAssets > 0 ? ($cat->assets_count / $totalAssets) * 100 : 0 }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Mutasi Terbaru -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-semibold text-slate-800 text-sm">Aktivitas Mutasi Terkini</h3>
                    <a href="{{ route('movements.index') }}" class="text-xs text-indigo-600 hover:text-indigo-900 font-medium">Lihat Semua &rarr;</a>
                </div>
                <div class="space-y-3">
                    @forelse ($recentMovements as $rm)
                        <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 border border-slate-100 text-xs">
                            <div>
                                <span class="font-medium text-slate-900 block">{{ $rm->asset?->name }} ({{ $rm->asset?->asset_tag }})</span>
                                <span class="text-slate-500 block mt-0.5">{{ $rm->fromLocation?->name }} &rarr; {{ $rm->toLocation?->name }}</span>
                            </div>
                            <div class="text-right text-slate-400">
                                <div>{{ $rm->moved_at->diffForHumans() }}</div>
                            </div>
                        </div>
                    @empty
                        <p class="text-slate-400 text-xs py-6 text-center">Belum ada aktivitas mutasi.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
