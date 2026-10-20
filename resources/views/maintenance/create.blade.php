<x-layouts.app>
    <x-slot:header>Catat Pemeliharaan / Perbaikan Baru</x-slot:header>

    <div class="max-w-2xl bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8">
        <form action="{{ route('maintenance.store') }}" method="POST" class="space-y-6">
            @csrf

            <div>
                <label for="asset_id" class="block text-sm font-medium text-slate-700 mb-1.5">Pilih Aset yang Bermasalah</label>
                <select name="asset_id" id="asset_id" required
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    <option value="">Pilih Perangkat</option>
                    @foreach ($eligibleAssets as $asset)
                        <option value="{{ $asset->id }}" {{ (old('asset_id') == $asset->id || (isset($selectedAsset) && $selectedAsset->id == $asset->id)) ? 'selected' : '' }}>
                            [{{ $asset->asset_tag }}] {{ $asset->name }} ({{ $asset->brand }} - Lokasi: {{ $asset->location?->name }})
                        </option>
                    @endforeach
                </select>
                @error('asset_id')
                    <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="technician_id" class="block text-sm font-medium text-slate-700 mb-1.5">Teknisi / Penanggung Jawab Servis</label>
                <select name="technician_id" id="technician_id" required
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    <option value="">Pilih Teknisi</option>
                    @foreach ($technicians as $tech)
                        <option value="{{ $tech->id }}" {{ old('technician_id', auth()->id()) == $tech->id ? 'selected' : '' }}>
                            {{ $tech->name }} ({{ $tech->role?->label ?? 'Staf' }})
                        </option>
                    @endforeach
                </select>
                @error('technician_id')
                    <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="started_at" class="block text-sm font-medium text-slate-700 mb-1.5">Tanggal Mulai Servis</label>
                    <input type="datetime-local" name="started_at" id="started_at" value="{{ old('started_at', now()->format('Y-m-d\TH:i')) }}" required
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    @error('started_at')
                        <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="cost" class="block text-sm font-medium text-slate-700 mb-1.5">Estimasi Biaya Perbaikan (IDR)</label>
                    <input type="number" step="0.01" name="cost" id="cost" value="{{ old('cost') }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                        placeholder="contoh: 500000">
                </div>
            </div>

            <div>
                <label for="issue" class="block text-sm font-medium text-slate-700 mb-1.5">Deskripsi Isu / Kerusakan</label>
                <textarea name="issue" id="issue" rows="3" required
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                    placeholder="misal: Blue screen berulang, kipas berbunyi keras, port LAN mati...">{{ old('issue') }}</textarea>
                @error('issue')
                    <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('maintenance.index') }}" class="px-4 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:text-slate-800 transition">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium shadow-sm transition">
                    Simpan Catatan Servis
                </button>
            </div>
        </form>
    </div>
</x-layouts.app>
