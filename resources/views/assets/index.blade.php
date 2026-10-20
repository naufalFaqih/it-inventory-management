<x-layouts.app>
    <x-slot:header>Daftar Aset IT</x-slot:header>

    <div class="space-y-6">
        <!-- Actions & Filter Bar -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm space-y-4">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <form method="GET" action="{{ route('assets.index') }}" class="flex flex-wrap items-center gap-3 w-full">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari asset tag, nama, merek, SN..."
                        class="px-3.5 py-2 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none w-full sm:w-64">

                    <select name="category_id" class="px-3.5 py-2 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                        <option value="">Semua Kategori</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>

                    <select name="location_id" class="px-3.5 py-2 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                        <option value="">Semua Lokasi</option>
                        @foreach ($locations as $loc)
                            <option value="{{ $loc->id }}" {{ request('location_id') == $loc->id ? 'selected' : '' }}>
                                {{ $loc->name }}
                            </option>
                        @endforeach
                    </select>

                    <select name="status" class="px-3.5 py-2 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                        <option value="">Semua Status</option>
                        @foreach (\App\Models\Asset::STATUSES as $val => $label)
                            <option value="{{ $val }}" {{ request('status') === $val ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>

                    <select name="condition" class="px-3.5 py-2 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                        <option value="">Semua Kondisi</option>
                        @foreach (\App\Models\Asset::CONDITIONS as $val => $label)
                            <option value="{{ $val }}" {{ request('condition') === $val ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>

                    <button type="submit" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white text-sm font-medium rounded-xl transition">
                        Filter
                    </button>

                    @if (request()->hasAny(['search', 'category_id', 'location_id', 'status', 'condition', 'sort']))
                        <a href="{{ route('assets.index') }}" class="text-sm text-slate-500 hover:text-slate-800">Reset</a>
                    @endif
                </form>

                <div class="w-full sm:w-auto flex justify-end">
                    <a href="{{ route('assets.create') }}"
                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-xl shadow-sm transition whitespace-nowrap">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span>Tambah Aset</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Assets Table -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 text-slate-700 font-semibold border-b border-slate-200">
                        <tr>
                            <th class="px-6 py-3.5">Asset Tag & Nama</th>
                            <th class="px-6 py-3.5">Kategori / Merek</th>
                            <th class="px-6 py-3.5">Lokasi / Penanggung Jawab</th>
                            <th class="px-6 py-3.5">Status & Kondisi</th>
                            <th class="px-6 py-3.5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($assets as $asset)
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('assets.show', $asset) }}" class="font-mono font-semibold text-indigo-600 hover:underline">
                                            {{ $asset->asset_tag }}
                                        </a>
                                    </div>
                                    <div class="font-medium text-slate-900 mt-0.5">{{ $asset->name }}</div>
                                    @if ($asset->serial_number)
                                        <div class="text-xs text-slate-400 font-mono">SN: {{ $asset->serial_number }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-slate-800">{{ $asset->category?->name }}</div>
                                    <div class="text-xs text-slate-400">{{ $asset->brand }} &bull; {{ $asset->model }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-slate-800">{{ $asset->location?->name }}</div>
                                    <div class="text-xs text-slate-500">
                                        {{ $asset->assignedUser ? 'PIC: ' . $asset->assignedUser->name : 'Tanpa PIC' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 space-y-1">
                                    <div>
                                        @if ($asset->status === 'available')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                Tersedia
                                            </span>
                                        @elseif ($asset->status === 'assigned')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200">
                                                Ditetapkan
                                            </span>
                                        @elseif ($asset->status === 'borrowed')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200">
                                                Dipinjam
                                            </span>
                                        @elseif ($asset->status === 'maintenance')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-rose-50 text-rose-700 border border-rose-200">
                                                Perbaikan
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-600 border border-slate-200">
                                                Retired
                                            </span>
                                        @endif
                                    </div>
                                    <div class="text-xs">
                                        @if ($asset->condition === 'good')
                                            <span class="text-emerald-600 font-medium">&bull; Kondisi Baik</span>
                                        @elseif ($asset->condition === 'minor_damage')
                                            <span class="text-amber-600 font-medium">&bull; Rusak Ringan</span>
                                        @else
                                            <span class="text-rose-600 font-medium">&bull; Rusak Berat</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right space-x-2 whitespace-nowrap">
                                    <a href="{{ route('assets.show', $asset) }}" class="text-slate-600 hover:text-slate-900 font-medium text-xs">
                                        Detail
                                    </a>
                                    <a href="{{ route('assets.edit', $asset) }}" class="text-indigo-600 hover:text-indigo-900 font-medium text-xs">
                                        Edit
                                    </a>
                                    @if ($asset->status !== 'retired')
                                        <form action="{{ route('assets.retire', $asset) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" onclick="return confirm('Nonaktifkan (retire) aset ini?')"
                                                class="text-xs font-medium text-amber-600 hover:text-amber-900">
                                                Retire
                                            </button>
                                        </form>
                                    @endif
                                    <form action="{{ route('assets.destroy', $asset) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" onclick="return confirm('Hapus aset ini? Aset dengan riwayat akan otomatis dinonaktifkan tanpa menghapus riwayat.')"
                                            class="text-xs font-medium text-rose-600 hover:text-rose-900">
                                            Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                                    Tidak ada aset yang sesuai dengan kriteria pencarian.
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
