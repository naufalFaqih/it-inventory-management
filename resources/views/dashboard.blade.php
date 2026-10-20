<x-layouts.app>
    <x-slot:header>Ringkasan Operasional & Dashboard Inventory</x-slot:header>

    <div class="space-y-8">
        <!-- Top KPI Cards -->
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
                <span class="text-xs font-medium text-slate-500 uppercase tracking-wider block">Total Aset</span>
                <span class="text-2xl font-bold text-slate-900 mt-2 block">{{ $totalAssets }}</span>
                <span class="text-xs text-slate-400 mt-1 block">Semua perangkat tercatat</span>
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
                <span class="text-xs font-medium text-rose-600 uppercase tracking-wider block">Maintenance</span>
                <span class="text-2xl font-bold text-rose-600 mt-2 block">{{ $maintenanceAssets }}</span>
                <span class="text-xs text-slate-400 mt-1 block">Dalam perbaikan</span>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
                <span class="text-xs font-medium text-slate-500 uppercase tracking-wider block">Retired</span>
                <span class="text-2xl font-bold text-slate-500 mt-2 block">{{ $retiredAssets }}</span>
                <span class="text-xs text-slate-400 mt-1 block">Aset nonaktif</span>
            </div>
        </div>

        <!-- Quick Summary Grids -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Peminjaman Aktif -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-semibold text-slate-800 text-sm">Peminjaman Aset Berjalan</h3>
                    <a href="{{ route('loans.index') }}" class="text-xs text-indigo-600 hover:text-indigo-900 font-medium">Lihat Semua</a>
                </div>
                <div class="space-y-3">
                    @forelse ($activeLoans as $loan)
                        <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 border border-slate-100 text-sm">
                            <div>
                                <span class="font-medium text-slate-900 block">{{ $loan->asset?->name }}</span>
                                <span class="text-xs text-slate-500 block">Peminjam: {{ $loan->borrower?->name }} &bull; Tag: {{ $loan->asset?->asset_tag }}</span>
                            </div>
                            <div class="text-right">
                                <span class="text-xs font-medium px-2 py-1 rounded-full bg-amber-100 text-amber-800">
                                    Due: {{ $loan->due_at ? $loan->due_at->translatedFormat('d M Y') : 'Tanpa batas' }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <p class="text-slate-400 text-xs py-4 text-center">Tidak ada peminjaman yang sedang aktif.</p>
                    @endforelse
                </div>
            </div>

            <!-- Maintenance Aktif -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-semibold text-slate-800 text-sm">Pemeliharaan / Perbaikan Aktif</h3>
                    <a href="{{ route('maintenance.index') }}" class="text-xs text-indigo-600 hover:text-indigo-900 font-medium">Lihat Semua</a>
                </div>
                <div class="space-y-3">
                    @forelse ($activeMaintenance as $maint)
                        <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 border border-slate-100 text-sm">
                            <div>
                                <span class="font-medium text-slate-900 block">{{ $maint->asset?->name }}</span>
                                <span class="text-xs text-slate-500 block">Isu: {{ Str::limit($maint->issue, 35) }} &bull; Teknisi: {{ $maint->technician?->name }}</span>
                            </div>
                            <div class="text-right">
                                <span class="text-xs font-medium px-2 py-1 rounded-full bg-rose-100 text-rose-800">
                                    {{ $maint->started_at->translatedFormat('d M Y') }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <p class="text-slate-400 text-xs py-4 text-center">Tidak ada perangkat dalam masa perbaikan.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
