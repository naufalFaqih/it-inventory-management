<x-layouts.app>
    <x-slot:header>Edit Lokasi: {{ $location->name }}</x-slot:header>

    <div class="max-w-xl bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8">
        <form action="{{ route('locations.update', $location) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div>
                <label for="name" class="block text-sm font-medium text-slate-700 mb-1.5">Nama Lokasi / Ruangan</label>
                <input type="text" name="name" id="name" value="{{ old('name', $location->name) }}" required
                    class="w-full px-4 py-2.5 rounded-xl border @error('name') border-rose-300 @else border-slate-300 @enderror focus:ring-2 focus:ring-indigo-500 focus:outline-none text-sm">
                @error('name')
                    <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="parent_id" class="block text-sm font-medium text-slate-700 mb-1.5">Gedung / Lokasi Induk (Opsional)</label>
                <select name="parent_id" id="parent_id"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 focus:outline-none text-sm">
                    <option value="">Tidak ada (Jadikan Lokasi Utama / Gedung)</option>
                    @foreach ($parentLocations as $parent)
                        <option value="{{ $parent->id }}" {{ old('parent_id', $location->parent_id) == $parent->id ? 'selected' : '' }}>
                            {{ $parent->name }}
                        </option>
                    @endforeach
                </select>
                @error('parent_id')
                    <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-slate-700 mb-1.5">Deskripsi / Detail Ruangan</label>
                <textarea name="description" id="description" rows="3"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 focus:outline-none text-sm">{{ old('description', $location->description) }}</textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('locations.index') }}" class="px-4 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:text-slate-800 transition">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium shadow-sm transition">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</x-layouts.app>
