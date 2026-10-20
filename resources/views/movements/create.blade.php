<x-layouts.app>
    <x-slot:header>Catat Mutasi / Perpindahan Aset</x-slot:header>

    <div class="max-w-2xl bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8">
        <form action="{{ route('movements.store') }}" method="POST" class="space-y-6">
            @csrf

            <div>
                <label for="asset_id" class="block text-sm font-medium text-slate-700 mb-1.5">Pilih Aset</label>
                <select name="asset_id" id="asset_id" required
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    <option value="">Pilih Aset yang Akan Dipindahkan</option>
                    @foreach ($assets as $asset)
                        <option value="{{ $asset->id }}" {{ (old('asset_id') == $asset->id || (isset($selectedAsset) && $selectedAsset->id == $asset->id)) ? 'selected' : '' }}>
                            [{{ $asset->asset_tag }}] {{ $asset->name }} — Lokasi saat ini: {{ $asset->location?->name }} ({{ $asset->assignedUser?->name ?: 'Tanpa PIC' }})
                        </option>
                    @endforeach
                </select>
                @error('asset_id')
                    <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="to_location_id" class="block text-sm font-medium text-slate-700 mb-1.5">Lokasi Tujuan</label>
                    <select name="to_location_id" id="to_location_id" required
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                        <option value="">Pilih Lokasi Baru</option>
                        @foreach ($locations as $loc)
                            <option value="{{ $loc->id }}" {{ old('to_location_id') == $loc->id ? 'selected' : '' }}>
                                {{ $loc->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('to_location_id')
                        <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="to_user_id" class="block text-sm font-medium text-slate-700 mb-1.5">Penanggung Jawab Baru (PIC)</label>
                    <select name="to_user_id" id="to_user_id"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                        <option value="">(Opsional) Tanpa PIC Khusus</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}" {{ old('to_user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }} ({{ $user->department?->name ?? '-' }})
                            </option>
                        @endforeach
                    </select>
                    @error('to_user_id')
                        <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="moved_at" class="block text-sm font-medium text-slate-700 mb-1.5">Waktu Pelaksanaan Mutasi</label>
                <input type="datetime-local" name="moved_at" id="moved_at" value="{{ old('moved_at', now()->format('Y-m-d\TH:i')) }}" required
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                @error('moved_at')
                    <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="reason" class="block text-sm font-medium text-slate-700 mb-1.5">Alasan Pemindahan / Mutasi</label>
                <textarea name="reason" id="reason" rows="3" required
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                    placeholder="Contoh: Penugasan pegawai baru, perpindahan meja operasional, penarikan ke gudang IT...">{{ old('reason') }}</textarea>
                @error('reason')
                    <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('movements.index') }}" class="px-4 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:text-slate-800 transition">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium shadow-sm transition">
                    Simpan Riwayat Mutasi
                </button>
            </div>
        </form>
    </div>
</x-layouts.app>
