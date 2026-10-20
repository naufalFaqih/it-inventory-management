<x-layouts.app>
    <x-slot:header>Detail Aset: {{ $asset->asset_tag }}</x-slot:header>

    <div class="space-y-8">
        <!-- Top Summary Banner -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 border-b border-slate-100 pb-6">
                <div>
                    <div class="flex items-center gap-3">
                        <span class="font-mono text-xl font-bold text-indigo-600 bg-indigo-50 px-3 py-1 rounded-xl border border-indigo-100">
                            {{ $asset->asset_tag }}
                        </span>
                        @if ($asset->status === 'available')
                            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                Tersedia
                            </span>
                        @elseif ($asset->status === 'assigned')
                            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                Ditetapkan
                            </span>
                        @elseif ($asset->status === 'borrowed')
                            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                Dipinjam
                            </span>
                        @elseif ($asset->status === 'maintenance')
                            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                Dalam Perbaikan
                            </span>
                        @else
                            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                                Retired
                            </span>
                        @endif

                        @if ($asset->condition === 'good')
                            <span class="text-xs font-medium text-emerald-600 bg-emerald-50 px-2.5 py-0.5 rounded-full border border-emerald-200">Kondisi Baik</span>
                        @elseif ($asset->condition === 'minor_damage')
                            <span class="text-xs font-medium text-amber-600 bg-amber-50 px-2.5 py-0.5 rounded-full border border-amber-200">Rusak Ringan</span>
                        @else
                            <span class="text-xs font-medium text-rose-600 bg-rose-50 px-2.5 py-0.5 rounded-full border border-rose-200">Rusak Berat</span>
                        @endif
                    </div>
                    <h1 class="text-2xl font-bold text-slate-900 mt-2">{{ $asset->name }}</h1>
                    <p class="text-sm text-slate-500 mt-1">{{ $asset->brand }} &bull; {{ $asset->model }}</p>
                </div>

                <div class="flex items-center gap-3">
                    <a href="{{ route('assets.edit', $asset) }}"
                        class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-xl shadow-sm transition">
                        Edit Aset
                    </a>

                    @if ($asset->status !== 'retired')
                        <form action="{{ route('assets.retire', $asset) }}" method="POST" class="inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" onclick="return confirm('Nonaktifkan (retire) aset ini?')"
                                class="px-4 py-2 bg-amber-50 hover:bg-amber-100 text-amber-700 border border-amber-200 text-sm font-medium rounded-xl transition">
                                Retire
                            </button>
                        </form>
                    @endif

                    <a href="{{ route('assets.index') }}" class="px-4 py-2 border border-slate-300 hover:bg-slate-50 text-slate-700 text-sm font-medium rounded-xl transition">
                        Kembali
                    </a>
                </div>
            </div>

            <!-- Detail Grid -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 pt-6">
                <div>
                    <span class="text-xs font-medium text-slate-400 uppercase tracking-wider block">Kategori</span>
                    <span class="text-sm font-semibold text-slate-800 mt-1 block">{{ $asset->category?->name }}</span>
                </div>
                <div>
                    <span class="text-xs font-medium text-slate-400 uppercase tracking-wider block">Nomor Seri (SN)</span>
                    <span class="text-sm font-mono font-medium text-slate-800 mt-1 block">{{ $asset->serial_number ?: '-' }}</span>
                </div>
                <div>
                    <span class="text-xs font-medium text-slate-400 uppercase tracking-wider block">Lokasi Penempatan</span>
                    <span class="text-sm font-semibold text-slate-800 mt-1 block">{{ $asset->location?->name }}</span>
                </div>
                <div>
                    <span class="text-xs font-medium text-slate-400 uppercase tracking-wider block">Penanggung Jawab (PIC)</span>
                    <span class="text-sm font-semibold text-slate-800 mt-1 block">{{ $asset->assignedUser?->name ?: 'Tidak ada' }}</span>
                </div>
                <div>
                    <span class="text-xs font-medium text-slate-400 uppercase tracking-wider block">Tanggal Pembelian</span>
                    <span class="text-sm text-slate-800 mt-1 block">{{ $asset->purchase_date ? $asset->purchase_date->translatedFormat('d F Y') : '-' }}</span>
                </div>
                <div>
                    <span class="text-xs font-medium text-slate-400 uppercase tracking-wider block">Nilai Perolehan</span>
                    <span class="text-sm font-medium text-slate-800 mt-1 block">{{ $asset->purchase_cost ? 'Rp ' . number_format($asset->purchase_cost, 0, ',', '.') : '-' }}</span>
                </div>
                <div>
                    <span class="text-xs font-medium text-slate-400 uppercase tracking-wider block">Didaftarkan Oleh</span>
                    <span class="text-sm text-slate-800 mt-1 block">{{ $asset->creator?->name }}</span>
                </div>
                <div>
                    <span class="text-xs font-medium text-slate-400 uppercase tracking-wider block">Tanggal Input</span>
                    <span class="text-sm text-slate-800 mt-1 block">{{ $asset->created_at->translatedFormat('d M Y H:i') }}</span>
                </div>
            </div>

            @if ($asset->notes)
                <div class="mt-6 p-4 rounded-xl bg-slate-50 border border-slate-100 text-sm text-slate-600">
                    <span class="font-semibold text-slate-800 block mb-1">Catatan:</span>
                    <p class="whitespace-pre-line">{{ $asset->notes }}</p>
                </div>
            @endif
        </div>

        <!-- Transaction History Tabs -->
        <div class="space-y-6">
            <h2 class="text-lg font-bold text-slate-800 tracking-tight">Riwayat & Histori Transaksi</h2>

            <!-- 1. Mutasi Perpindahan -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="font-semibold text-slate-800 text-sm flex items-center gap-2">
                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                        </svg>
                        <span>Histori Mutasi Lokasi & Penanggung Jawab</span>
                    </h3>
                    <span class="text-xs text-slate-400">{{ $asset->movements->count() }} mutasi</span>
                </div>
                <div class="divide-y divide-slate-100 text-sm">
                    @forelse ($asset->movements as $m)
                        <div class="p-5 hover:bg-slate-50/70 transition flex flex-col md:flex-row md:items-center justify-between gap-4">
                            <div>
                                <div class="font-medium text-slate-900">
                                    {{ $m->fromLocation?->name }} &rarr; <span class="text-indigo-600">{{ $m->toLocation?->name }}</span>
                                </div>
                                <div class="text-xs text-slate-500 mt-1">
                                    PIC: {{ $m->fromUser?->name ?? 'None' }} &rarr; {{ $m->toUser?->name ?? 'None' }}
                                    &bull; Alasan: {{ $m->reason }}
                                </div>
                            </div>
                            <div class="text-right text-xs text-slate-400">
                                <div>{{ $m->moved_at->translatedFormat('d M Y H:i') }}</div>
                                <div>Oleh: {{ $m->creator?->name }}</div>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center text-slate-400 text-xs">Belum ada riwayat mutasi perpindahan untuk aset ini.</div>
                    @endforelse
                </div>
            </div>

            <!-- 2. Peminjaman & Pengembalian -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="font-semibold text-slate-800 text-sm flex items-center gap-2">
                        <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Riwayat Peminjaman & Pengembalian (Loans)</span>
                    </h3>
                    <span class="text-xs text-slate-400">{{ $asset->loans->count() }} transaksi</span>
                </div>
                <div class="divide-y divide-slate-100 text-sm">
                    @forelse ($asset->loans as $l)
                        <div class="p-5 hover:bg-slate-50/70 transition flex flex-col md:flex-row md:items-center justify-between gap-4">
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="font-medium text-slate-900">Peminjam: {{ $l->borrower?->name }}</span>
                                    @if ($l->status === 'active')
                                        <span class="px-2 py-0.5 rounded text-xs bg-amber-50 text-amber-700 font-semibold border border-amber-200">Sedang Dipinjam</span>
                                    @elseif ($l->status === 'returned')
                                        <span class="px-2 py-0.5 rounded text-xs bg-emerald-50 text-emerald-700 font-semibold border border-emerald-200">Dikembalikan</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded text-xs bg-rose-50 text-rose-700 font-semibold border border-rose-200">Terlambat</span>
                                    @endif
                                </div>
                                <div class="text-xs text-slate-500 mt-1">
                                    Dipinjam: {{ $l->borrowed_at->translatedFormat('d M Y H:i') }}
                                    &bull; Tenggat: {{ $l->due_at ? $l->due_at->translatedFormat('d M Y') : '-' }}
                                    @if ($l->returned_at)
                                        &bull; Dikembalikan: {{ $l->returned_at->translatedFormat('d M Y H:i') }}
                                    @endif
                                </div>
                            </div>
                            <div class="text-right text-xs text-slate-400">
                                <div>Kondisi Pinjam: {{ $l->checkout_condition }}</div>
                                @if ($l->return_condition)
                                    <div>Kondisi Kembali: {{ $l->return_condition }}</div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center text-slate-400 text-xs">Belum ada riwayat peminjaman untuk aset ini.</div>
                    @endforelse
                </div>
            </div>

            <!-- 3. Maintenance Records -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="font-semibold text-slate-800 text-sm flex items-center gap-2">
                        <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        </svg>
                        <span>Riwayat Pemeliharaan & Servis (Maintenance)</span>
                    </h3>
                    <span class="text-xs text-slate-400">{{ $asset->maintenanceRecords->count() }} catatan</span>
                </div>
                <div class="divide-y divide-slate-100 text-sm">
                    @forelse ($asset->maintenanceRecords as $maint)
                        <div class="p-5 hover:bg-slate-50/70 transition flex flex-col md:flex-row md:items-center justify-between gap-4">
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="font-medium text-slate-900">Isu: {{ $maint->issue }}</span>
                                    @if ($maint->status === 'in_progress')
                                        <span class="px-2 py-0.5 rounded text-xs bg-rose-50 text-rose-700 font-semibold border border-rose-200">Sedang Dikerjakan</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded text-xs bg-emerald-50 text-emerald-700 font-semibold border border-emerald-200">Selesai</span>
                                    @endif
                                </div>
                                <div class="text-xs text-slate-500 mt-1">
                                    Teknisi: {{ $maint->technician?->name }}
                                    @if ($maint->action_taken)
                                        &bull; Tindakan: {{ $maint->action_taken }}
                                    @endif
                                </div>
                            </div>
                            <div class="text-right text-xs text-slate-400">
                                <div>Biaya: {{ $maint->cost ? 'Rp ' . number_format($maint->cost, 0, ',', '.') : 'Rp 0' }}</div>
                                <div>Mulai: {{ $maint->started_at->translatedFormat('d M Y') }}</div>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center text-slate-400 text-xs">Belum ada riwayat perbaikan/pemeliharaan untuk aset ini.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
