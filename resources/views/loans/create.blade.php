<x-layouts.app>
    <x-slot:header>Form Peminjaman Perangkat</x-slot:header>

    <div class="max-w-2xl bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8">
        <form action="{{ route('loans.store') }}" method="POST" class="space-y-6">
            @csrf

            <div>
                <label for="asset_id" class="block text-sm font-medium text-slate-700 mb-1.5">Pilih Aset yang Tersedia</label>
                <select name="asset_id" id="asset_id" required
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    <option value="">Pilih Perangkat (Hanya Aset Berstatus Tersedia)</option>
                    @foreach ($availableAssets as $asset)
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
                <label for="borrower_id" class="block text-sm font-medium text-slate-700 mb-1.5">Nama Peminjam</label>
                <select name="borrower_id" id="borrower_id" required
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    <option value="">Pilih Pengguna / Pegawai Peminjam</option>
                    @foreach ($borrowers as $user)
                        <option value="{{ $user->id }}" {{ old('borrower_id') == $user->id ? 'selected' : '' }}>
                            {{ $user->name }} &bull; {{ $user->department?->name ?? 'Staf' }}
                        </option>
                    @endforeach
                </select>
                @error('borrower_id')
                    <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="borrowed_at" class="block text-sm font-medium text-slate-700 mb-1.5">Waktu Peminjaman</label>
                    <input type="datetime-local" name="borrowed_at" id="borrowed_at" value="{{ old('borrowed_at', now()->format('Y-m-d\TH:i')) }}" required
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    @error('borrowed_at')
                        <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="due_at" class="block text-sm font-medium text-slate-700 mb-1.5">Tenggat Pengembalian (Due Date)</label>
                    <input type="date" name="due_at" id="due_at" value="{{ old('due_at', now()->addDays(7)->format('Y-m-d')) }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    @error('due_at')
                        <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="checkout_condition" class="block text-sm font-medium text-slate-700 mb-1.5">Kondisi Saat Dipinjam</label>
                <select name="checkout_condition" id="checkout_condition" required
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    @foreach (\App\Models\Asset::CONDITIONS as $val => $label)
                        <option value="{{ $val }}" {{ old('checkout_condition', 'good') === $val ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="notes" class="block text-sm font-medium text-slate-700 mb-1.5">Keperluan & Catatan Peminjaman</label>
                <textarea name="notes" id="notes" rows="3"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                    placeholder="misal: Presentasi rapat direksi, perjalanan dinas luar kota...">{{ old('notes') }}</textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('loans.index') }}" class="px-4 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:text-slate-800 transition">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium shadow-sm transition">
                    Proses Peminjaman
                </button>
            </div>
        </form>
    </div>
</x-layouts.app>
