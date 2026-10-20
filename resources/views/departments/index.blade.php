<x-layouts.app>
    <x-slot:header>Master Departemen</x-slot:header>

    <div class="space-y-6">
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex flex-col md:flex-row gap-4 items-center justify-between">
            <form method="GET" action="{{ route('departments.index') }}" class="flex items-center gap-3 w-full md:w-auto">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama departemen..."
                    class="px-3.5 py-2 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none w-full sm:w-64">
                <button type="submit" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white text-sm font-medium rounded-xl transition">
                    Cari
                </button>
                @if (request('search'))
                    <a href="{{ route('departments.index') }}" class="text-sm text-slate-500 hover:text-slate-800">Reset</a>
                @endif
            </form>

            <a href="{{ route('departments.create') }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-xl shadow-sm transition whitespace-nowrap">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Tambah Departemen</span>
            </a>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 text-slate-700 font-semibold border-b border-slate-200">
                        <tr>
                            <th class="px-6 py-3.5">Nama Departemen</th>
                            <th class="px-6 py-3.5 text-center">Jumlah Pengguna</th>
                            <th class="px-6 py-3.5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($departments as $dept)
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="px-6 py-4 font-medium text-slate-900">
                                    {{ $dept->name }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-700">
                                        {{ $dept->users_count }} pengguna
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right space-x-2">
                                    <a href="{{ route('departments.edit', $dept) }}" class="text-indigo-600 hover:text-indigo-900 font-medium text-xs">
                                        Edit
                                    </a>
                                    <form action="{{ route('departments.destroy', $dept) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" onclick="return confirm('Hapus departemen ini?')"
                                            class="text-xs font-medium text-rose-600 hover:text-rose-900">
                                            Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-10 text-center text-slate-400">
                                    Belum ada departemen yang ditambahkan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-slate-200">
                {{ $departments->links() }}
            </div>
        </div>
    </div>
</x-layouts.app>
