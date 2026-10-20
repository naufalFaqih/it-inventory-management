<x-layouts.app>
    <x-slot:header>Manajemen Pemeliharaan & Perbaikan Aset (Maintenance)</x-slot:header>

    <div class="space-y-6">
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex flex-col md:flex-row gap-4 items-center justify-between">
            <form method="GET" action="{{ route('maintenance.index') }}" class="flex flex-wrap items-center gap-3 w-full md:w-auto">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari isu / nama perangkat..."
                    class="px-3.5 py-2 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none w-full sm:w-60">

                <select name="status" class="px-3.5 py-2 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    <option value="">Semua Status</option>
                    <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>Sedang Dikerjakan</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Selesai</option>
                </select>

                <button type="submit" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white text-sm font-medium rounded-xl transition">
                    Filter
                </button>
                @if (request()->hasAny(['search', 'status']))
                    <a href="{{ route('maintenance.index') }}" class="text-sm text-slate-500 hover:text-slate-800">Reset</a>
                @endif
            </form>

            <a href="{{ route('maintenance.create') }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-xl shadow-sm transition whitespace-nowrap">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Catat Servis / Perbaikan</span>
            </a>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 text-slate-700 font-semibold border-b border-slate-200">
                        <tr>
                            <th class="px-6 py-3.5">Aset & Tag</th>
                            <th class="px-6 py-3.5">Isu & Tindakan</th>
                            <th class="px-6 py-3.5">Teknisi / Biaya</th>
                            <th class="px-6 py-3.5">Status & Waktu</th>
                            <th class="px-6 py-3.5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($records as $maint)
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="px-6 py-4">
                                    <div class="font-medium text-slate-900">{{ $maint->asset?->name }}</div>
                                    <a href="{{ route('assets.show', $maint->asset_id) }}" class="font-mono text-xs text-indigo-600 hover:underline">
                                        {{ $maint->asset?->asset_tag }}
                                    </a>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-medium text-slate-800">{{ $maint->issue }}</div>
                                    @if ($maint->action_taken)
                                        <div class="text-xs text-emerald-600 mt-0.5">Tindakan: {{ $maint->action_taken }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-slate-900">{{ $maint->technician?->name }}</div>
                                    <div class="text-xs text-slate-400">
                                        {{ $maint->cost ? 'Rp ' . number_format($maint->cost, 0, ',', '.') : 'Rp 0' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 space-y-1">
                                    <div>
                                        @if ($maint->status === 'in_progress')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-rose-50 text-rose-700 border border-rose-200">
                                                Sedang Dikerjakan
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                Selesai
                                            </span>
                                        @endif
                                    </div>
                                    <div class="text-xs text-slate-400">
                                        Mulai: {{ $maint->started_at->translatedFormat('d M Y') }}
                                        @if ($maint->completed_at)
                                            &bull; Selesai: {{ $maint->completed_at->translatedFormat('d M Y') }}
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    @if ($maint->status === 'in_progress')
                                        <a href="{{ route('maintenance.complete', $maint) }}"
                                            class="inline-flex items-center px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-medium shadow-sm transition">
                                            Selesaikan Servis
                                        </a>
                                    @else
                                        <span class="text-xs text-slate-400">Terselesaikan</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-slate-400">
                                    Belum ada data pemeliharaan atau perbaikan yang dicatat.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-slate-200">
                {{ $records->links() }}
            </div>
        </div>
    </div>
</x-layouts.app>
