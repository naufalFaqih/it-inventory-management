<x-layouts.app>
    <x-slot:header>Riwayat Mutasi & Perpindahan Lokasi Aset</x-slot:header>

    <div class="space-y-6">
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex flex-col md:flex-row gap-4 items-center justify-between">
            <form method="GET" action="{{ route('movements.index') }}" class="flex flex-wrap items-center gap-3 w-full md:w-auto">
                <select name="location_id" class="px-3.5 py-2 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    <option value="">Semua Lokasi</option>
                    @foreach ($locations as $loc)
                        <option value="{{ $loc->id }}" {{ request('location_id') == $loc->id ? 'selected' : '' }}>
                            {{ $loc->name }}
                        </option>
                    @endforeach
                </select>

                <button type="submit" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white text-sm font-medium rounded-xl transition">
                    Filter
                </button>
                @if (request()->hasAny(['location_id', 'asset_id']))
                    <a href="{{ route('movements.index') }}" class="text-sm text-slate-500 hover:text-slate-800">Reset</a>
                @endif
            </form>

            <a href="{{ route('movements.create') }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-xl shadow-sm transition whitespace-nowrap">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Catat Mutasi Baru</span>
            </a>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 text-slate-700 font-semibold border-b border-slate-200">
                        <tr>
                            <th class="px-6 py-3.5">Perangkat & Asset Tag</th>
                            <th class="px-6 py-3.5">Perpindahan Lokasi</th>
                            <th class="px-6 py-3.5">Perubahan PIC / Penanggung Jawab</th>
                            <th class="px-6 py-3.5">Alasan Mutasi</th>
                            <th class="px-6 py-3.5 text-right">Waktu & Pelaksana</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($movements as $m)
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="px-6 py-4">
                                    <div class="font-medium text-slate-900">{{ $m->asset?->name }}</div>
                                    <a href="{{ route('assets.show', $m->asset_id) }}" class="font-mono text-xs text-indigo-600 hover:underline">
                                        {{ $m->asset?->asset_tag }}
                                    </a>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-slate-500 text-xs line-through">{{ $m->fromLocation?->name }}</div>
                                    <div class="font-medium text-slate-800">&darr; {{ $m->toLocation?->name }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-slate-500 text-xs">{{ $m->fromUser?->name ?: 'Tanpa PIC' }}</div>
                                    <div class="font-medium text-slate-800">&rarr; {{ $m->toUser?->name ?: 'Tanpa PIC' }}</div>
                                </td>
                                <td class="px-6 py-4 text-slate-600">
                                    {{ $m->reason }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="text-slate-900 font-medium text-xs">{{ $m->moved_at->translatedFormat('d M Y H:i') }}</div>
                                    <div class="text-slate-400 text-xs">Oleh: {{ $m->creator?->name }}</div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-slate-400">
                                    Belum ada data mutasi atau perpindahan aset yang tercatat.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-slate-200">
                {{ $movements->links() }}
            </div>
        </div>
    </div>
</x-layouts.app>
