<x-layouts.app>
    <x-slot:header>Tambah Aset Baru</x-slot:header>

    <div class="max-w-4xl bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8">
        <form action="{{ route('assets.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Asset Tag & Name -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label for="asset_tag" class="block text-sm font-medium text-slate-700 mb-1.5">
                        Asset Tag (Kode Unik)
                    </label>
                    <div class="relative">
                        <input type="text" name="asset_tag" id="asset_tag" value="{{ old('asset_tag', $suggestedTag) }}"
                            class="w-full px-4 py-2.5 rounded-xl border @error('asset_tag') border-rose-300 @else border-slate-300 @enderror font-mono text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                            placeholder="AST-2026-0001">
                    </div>
                    <p class="text-xs text-slate-400 mt-1">Kosongkan jika ingin generate kode otomatis.</p>
                    @error('asset_tag')
                        <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="name" class="block text-sm font-medium text-slate-700 mb-1.5">Nama Perangkat</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                        class="w-full px-4 py-2.5 rounded-xl border @error('name') border-rose-300 @else border-slate-300 @enderror text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                        placeholder="misal: ThinkPad T14 Gen 4, Core Switch Lt 1">
                    @error('name')
                        <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Category, Brand, Model, Serial -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label for="category_id" class="block text-sm font-medium text-slate-700 mb-1.5">Kategori</label>
                    <select name="category_id" id="category_id" required
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                        <option value="">Pilih Kategori</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="brand" class="block text-sm font-medium text-slate-700 mb-1.5">Merek (Brand)</label>
                    <input type="text" name="brand" id="brand" value="{{ old('brand') }}" required
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                        placeholder="misal: Lenovo, Cisco, Dell">
                    @error('brand')
                        <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="model" class="block text-sm font-medium text-slate-700 mb-1.5">Model / Tipe</label>
                    <input type="text" name="model" id="model" value="{{ old('model') }}" required
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                        placeholder="misal: T14, Catalyst 9300">
                    @error('model')
                        <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="serial_number" class="block text-sm font-medium text-slate-700 mb-1.5">Nomor Seri (SN)</label>
                    <input type="text" name="serial_number" id="serial_number" value="{{ old('serial_number') }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-300 font-mono text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                        placeholder="Opsional jika tidak ada SN">
                    @error('serial_number')
                        <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Location & Assigned User -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label for="location_id" class="block text-sm font-medium text-slate-700 mb-1.5">Lokasi / Ruangan</label>
                    <select name="location_id" id="location_id" required
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                        <option value="">Pilih Lokasi Penempatan</option>
                        @foreach ($locations as $loc)
                            <option value="{{ $loc->id }}" {{ old('location_id') == $loc->id ? 'selected' : '' }}>
                                {{ $loc->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('location_id')
                        <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="assigned_to" class="block text-sm font-medium text-slate-700 mb-1.5">Penanggung Jawab / PIC</label>
                    <select name="assigned_to" id="assigned_to"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                        <option value="">(Opsional) Tanpa Penanggung Jawab Khusus</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}" {{ old('assigned_to') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }} ({{ $user->department?->name ?? 'Tanpa Departemen' }})
                            </option>
                        @endforeach
                    </select>
                    @error('assigned_to')
                        <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Status & Condition -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label for="status" class="block text-sm font-medium text-slate-700 mb-1.5">Status Operasional</label>
                    <select name="status" id="status" required
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                        @foreach (\App\Models\Asset::STATUSES as $val => $label)
                            <option value="{{ $val }}" {{ old('status', 'available') === $val ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="condition" class="block text-sm font-medium text-slate-700 mb-1.5">Kondisi Fisik</label>
                    <select name="condition" id="condition" required
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                        @foreach (\App\Models\Asset::CONDITIONS as $val => $label)
                            <option value="{{ $val }}" {{ old('condition', 'good') === $val ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Financial Information -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label for="purchase_date" class="block text-sm font-medium text-slate-700 mb-1.5">Tanggal Pembelian / Perolehan</label>
                    <input type="date" name="purchase_date" id="purchase_date" value="{{ old('purchase_date') }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                </div>

                <div>
                    <label for="purchase_cost" class="block text-sm font-medium text-slate-700 mb-1.5">Biaya Perolehan (IDR)</label>
                    <input type="number" step="0.01" name="purchase_cost" id="purchase_cost" value="{{ old('purchase_cost') }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                        placeholder="contoh: 15000000">
                </div>
            </div>

            <!-- Notes -->
            <div>
                <label for="notes" class="block text-sm font-medium text-slate-700 mb-1.5">Catatan Tambahan</label>
                <textarea name="notes" id="notes" rows="3"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                    placeholder="Informasi spesifikasi teknis tambahan, vendor, atau kondisi garansi...">{{ old('notes') }}</textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('assets.index') }}" class="px-4 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:text-slate-800 transition">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium shadow-sm transition">
                    Simpan & Daftarkan Aset
                </button>
            </div>
        </form>
    </div>
</x-layouts.app>
