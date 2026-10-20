<x-layouts.app>
    <x-slot:header>Daftar Peminjaman & Pengembalian Aset</x-slot:header>

    <div class="space-y-6">
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex flex-col md:flex-row gap-4 items-center justify-between">
            <form method="GET" action="{{ route('loans.index') }}" class="flex flex-wrap items-center gap-3 w-full md:w-auto">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama aset/peminjam..."
                    class="px-3.5 py-2 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none w-full sm:w-60">

                <select name="status" class="px-3.5 py-2 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    <option value="">Semua Status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Sedang Dipinjam</option>
                    <option value="returned" {{ request('status') === 'returned' ? 'selected' : '' }}>Sudah Dikembalikan</option>
                </select>

                <button type="submit" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white text-sm font-medium rounded-xl transition">
                    Filter
                </button>
                @if (request()->hasAny(['search', 'status', 'borrower_id']))
                    <a href="{{ route('loans.index') }}" class="text-sm text-slate-500 hover:text-slate-800">Reset</a>
                @endif
            </form>

            <a href="{{ route('loans.create') }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-xl shadow-sm transition whitespace-nowrap">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Pinjamkan Perangkat</span>
            </a>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 text-slate-700 font-semibold border-b border-slate-200">
                        <tr>
                            <th class="px-6 py-3.5">Aset & Tag</th>
                            <th class="px-6 py-3.5">Peminjam</th>
                            <th class="px-6 py-3.5">Jadwal Pinjam & Due Date</th>
                            <th class="px-6 py-3.5">Kondisi & Status</th>
                            <th class="px-6 py-3.5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($loans as $loan)
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="px-6 py-4">
                                    <div class="font-medium text-slate-900">{{ $loan->asset?->name }}</div>
                                    <a href="{{ route('assets.show', $loan->asset_id) }}" class="font-mono text-xs text-indigo-600 hover:underline">
                                        {{ $loan->asset?->asset_tag }}
                                    </a>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-medium text-slate-900">{{ $loan->borrower?->name }}</div>
                                    <div class="text-xs text-slate-400">{{ $loan->borrower?->department?->name ?? 'Pegawai' }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-xs text-slate-700">Pinjam: {{ $loan->borrowed_at->translatedFormat('d M Y') }}</div>
                                    <div class="text-xs {{ $loan->due_at && $loan->due_at->isPast() && $loan->status === 'active' ? 'text-rose-600 font-bold' : 'text-slate-500' }}">
                                        Tenggat: {{ $loan->due_at ? $loan->due_at->translatedFormat('d M Y') : 'Tanpa batas' }}
                                    </div>
                                    @if ($loan->returned_at)
                                        <div class="text-xs text-emerald-600">Kembali: {{ $loan->returned_at->translatedFormat('d M Y') }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 space-y-1">
                                    <div>
                                        @if ($loan->status === 'active')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200">
                                                Aktif Dipinjam
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                Dikembalikan
                                            </span>
                                        @endif
                                    </div>
                                    <div class="text-xs text-slate-500">
                                        Keluar: {{ $loan->checkout_condition }}
                                        @if ($loan->return_condition)
                                            &bull; Masuk: {{ $loan->return_condition }}
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    @if ($loan->status === 'active')
                                        <a href="{{ route('loans.return', $loan) }}"
                                            class="inline-flex items-center px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-medium shadow-sm transition">
                                            Pengembalian
                                        </a>
                                    @else
                                        <span class="text-xs text-slate-400">Selesai</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-slate-400">
                                    Belum ada data peminjaman yang tercatat.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-slate-200">
                {{ $loans->links() }}
            </div>
        </div>
    </div>
</x-layouts.app>
