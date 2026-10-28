<x-layouts.app>
    <x-slot:header>Laporan Inventaris & Nilai Aset</x-slot:header>

    <div class="space-y-6">
        <!-- Filter Bar -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
            <form method="GET" action="{{ route('reports.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Kategori</label>
                    <select name="category_id" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                        <option value="">Semua Kategori</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Lokasi</label>
                    <select name="location_id" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                        <option value="">Semua Lokasi</option>
                        @foreach ($locations as $loc)
                            <option value="{{ $loc->id }}" {{ request('location_id') == $loc->id ? 'selected' : '' }}>
                                {{ $loc->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Status Operasional</label>
                    <select name="status" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                        <option value="">Semua Status</option>
                        @foreach (\App\Models\Asset::STATUSES as $val => $label)
                            <option value="{{ $val }}" {{ request('status') === $val ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Dari Tanggal Beli</label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}"
                        class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Sampai Tanggal Beli</label>
                    <input type="date" name="end_date" value="{{ request('end_date') }}"
                        class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                </div>

                <div class="sm:col-span-2 lg:col-span-5 flex justify-end">
                    <button type="submit" class="w-full sm:w-auto px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-medium transition shadow-sm">
                        Filter
                    </button>
                </div>
            </form>
        </div>

        <!-- Summary Metric Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
                <span class="text-xs font-medium text-slate-500 uppercase tracking-wider block">Jumlah Aset Terfilter</span>
                <span class="text-2xl font-bold text-slate-900 mt-2 block">{{ $totalCount }} unit</span>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
                <span class="text-xs font-medium text-indigo-600 uppercase tracking-wider block">Total Nilai Perolehan</span>
                <span class="text-2xl font-bold text-indigo-600 mt-2 block">Rp {{ number_format($totalValue, 0, ',', '.') }}</span>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
                <span class="text-xs font-medium text-slate-500 uppercase tracking-wider block">Rata-rata Nilai / Unit</span>
                <span class="text-2xl font-bold text-slate-800 mt-2 block">Rp {{ number_format($averageValue, 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- Export Actions -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <p class="text-xs text-slate-500">
                Export mengikuti filter aktif dan memuat seluruh data hasil filter.
            </p>
            <div class="flex items-center gap-2">
                <a href="{{ route('reports.export.pdf', request()->query()) }}"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-white hover:bg-slate-50 text-slate-700 border border-slate-300 rounded-xl text-sm font-medium transition shadow-sm">
                    <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Export PDF
                </a>
                <a href="{{ route('reports.export.excel', request()->query()) }}"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-sm font-medium transition shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Export Excel
                </a>
            </div>
        </div>

        <!-- Report Table -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 text-slate-700 font-semibold border-b border-slate-200">
                        <tr>
                            <th class="px-6 py-3.5">Asset Tag</th>
                            <th class="px-6 py-3.5">Nama Perangkat & Merek</th>
                            <th class="px-6 py-3.5">Kategori</th>
                            <th class="px-6 py-3.5">Lokasi & PIC</th>
                            <th class="px-6 py-3.5">Status</th>
                            <th class="px-6 py-3.5">Tgl Pembelian</th>
                            <th class="px-6 py-3.5 text-right">Biaya Perolehan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($assets as $asset)
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="px-6 py-4 font-mono font-medium text-indigo-600">
                                    <a href="{{ route('assets.show', $asset) }}" class="hover:underline">
                                        {{ $asset->asset_tag }}
                                    </a>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-medium text-slate-900">{{ $asset->name }}</div>
                                    <div class="text-xs text-slate-400">{{ $asset->brand }} &bull; {{ $asset->model }}</div>
                                </td>
                                <td class="px-6 py-4 text-slate-800">
                                    {{ $asset->category?->name }}
                                </td>
                                <td class="px-6 py-4">
                                    <div>{{ $asset->location?->name }}</div>
                                    <div class="text-xs text-slate-400">{{ $asset->assignedUser?->name ?: 'Tanpa PIC' }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $asset->status === 'available' ? 'bg-emerald-50 text-emerald-700' : ($asset->status === 'borrowed' ? 'bg-amber-50 text-amber-700' : 'bg-slate-100 text-slate-700') }}">
                                        {{ \App\Models\Asset::STATUSES[$asset->status] ?? $asset->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-slate-600 text-xs">
                                    {{ $asset->purchase_date ? $asset->purchase_date->translatedFormat('d M Y') : '-' }}
                                </td>
                                <td class="px-6 py-4 text-right font-medium text-slate-900">
                                    {{ $asset->purchase_cost ? 'Rp ' . number_format($asset->purchase_cost, 0, ',', '.') : '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-10 text-center text-slate-400">
                                    Tidak ada data aset yang cocok dengan filter laporan ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-slate-200">
                {{ $assets->links() }}
            </div>
        </div>
    </div>
</x-layouts.app>
