<x-layouts.app>
    <x-slot:header>Audit Trail & Log Aktivitas Sistem</x-slot:header>

    <div class="space-y-6">
        <!-- Filter -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex flex-col md:flex-row gap-4 items-center justify-between">
            <form method="GET" action="{{ route('audit-logs.index') }}" class="flex flex-wrap items-center gap-3 w-full md:w-auto">
                <input type="text" name="action" value="{{ request('action') }}" placeholder="Filter aksi (misal: create_asset)..."
                    class="px-3.5 py-2 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none w-full sm:w-60">

                <select name="user_id" class="px-3.5 py-2 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    <option value="">Semua Aktor / Pengguna</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                            {{ $user->name }}
                        </option>
                    @endforeach
                </select>

                <button type="submit" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white text-sm font-medium rounded-xl transition">
                    Filter
                </button>
                @if (request()->hasAny(['action', 'user_id']))
                    <a href="{{ route('audit-logs.index') }}" class="text-sm text-slate-500 hover:text-slate-800">Reset</a>
                @endif
            </form>
        </div>

        <!-- Audit Table -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 text-slate-700 font-semibold border-b border-slate-200">
                        <tr>
                            <th class="px-6 py-3.5">Waktu (WIB)</th>
                            <th class="px-6 py-3.5">Aktor (Pengguna)</th>
                            <th class="px-6 py-3.5">Aksi</th>
                            <th class="px-6 py-3.5">Entitas Terkait</th>
                            <th class="px-6 py-3.5">Detail Perubahan Data (Old / New)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($auditLogs as $log)
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="px-6 py-4 text-xs text-slate-500 whitespace-nowrap">
                                    {{ $log->created_at->timezone('Asia/Jakarta')->translatedFormat('d M Y H:i:s') }}
                                    @if ($log->ip_address)
                                        <div class="text-[10px] text-slate-400 font-mono">{{ $log->ip_address }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 font-medium text-slate-900 whitespace-nowrap">
                                    {{ $log->user?->name ?: 'System' }}
                                    <div class="text-xs text-slate-400">{{ $log->user?->email }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-mono font-medium bg-slate-100 text-slate-700">
                                        {{ $log->action }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-xs font-mono text-slate-600 whitespace-nowrap">
                                    {{ class_basename($log->entity_type) }} #{{ $log->entity_id }}
                                </td>
                                <td class="px-6 py-4 text-xs font-mono">
                                    @if ($log->old_values || $log->new_values)
                                        <div class="space-y-1 max-w-md">
                                            @if ($log->old_values)
                                                <div class="text-rose-600 bg-rose-50/60 p-1.5 rounded truncate" title="{{ json_encode($log->old_values) }}">
                                                    - Old: {{ json_encode($log->old_values, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) }}
                                                </div>
                                            @endif
                                            @if ($log->new_values)
                                                <div class="text-emerald-700 bg-emerald-50/60 p-1.5 rounded truncate" title="{{ json_encode($log->new_values) }}">
                                                    + New: {{ json_encode($log->new_values, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) }}
                                                </div>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-slate-400 italic">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-slate-400">
                                    Belum ada catatan aktivitas di audit log.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-slate-200">
                {{ $auditLogs->links() }}
            </div>
        </div>
    </div>
</x-layouts.app>
