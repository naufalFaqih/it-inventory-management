<x-layouts.app>
    <x-slot:header>Proses Pengembalian Perangkat</x-slot:header>

    <div class="max-w-xl bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8">
        <!-- Summary Box -->
        <div class="mb-6 p-4 rounded-xl bg-slate-50 border border-slate-100 text-sm space-y-2">
            <div class="flex justify-between">
                <span class="text-slate-500">Perangkat:</span>
                <span class="font-medium text-slate-800">{{ $loan->asset?->name }} ({{ $loan->asset?->asset_tag }})</span>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-500">Peminjam:</span>
                <span class="font-medium text-slate-800">{{ $loan->borrower?->name }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-500">Waktu Pinjam:</span>
                <span class="text-slate-700">{{ $loan->borrowed_at->translatedFormat('d F Y H:i') }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-500">Kondisi Awal Pinjam:</span>
                <span class="font-medium text-slate-700">{{ $loan->checkout_condition }}</span>
            </div>
        </div>

        <form action="{{ route('loans.return.process', $loan) }}" method="POST" class="space-y-6">
            @csrf

            <div>
                <label for="returned_at" class="block text-sm font-medium text-slate-700 mb-1.5">Waktu Pengembalian</label>
                <input type="datetime-local" name="returned_at" id="returned_at" value="{{ old('returned_at', now()->format('Y-m-d\TH:i')) }}" required
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                @error('returned_at')
                    <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="return_condition" class="block text-sm font-medium text-slate-700 mb-1.5">Kondisi Fisik Saat Kembali</label>
                <select name="return_condition" id="return_condition" required
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    <option value="good" {{ old('return_condition') === 'good' ? 'selected' : '' }}>Baik (Normal)</option>
                    <option value="minor_damage" {{ old('return_condition') === 'minor_damage' ? 'selected' : '' }}>Rusak Ringan (Lecet/Goresan)</option>
                    <option value="major_damage" {{ old('return_condition') === 'major_damage' ? 'selected' : '' }}>Rusak Berat (Butuh Maintenance)</option>
                </select>
                <p class="text-xs text-slate-400 mt-1">Jika kondisi 'Rusak Berat', aset akan otomatis dialihkan ke status Maintenance.</p>
                @error('return_condition')
                    <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="notes" class="block text-sm font-medium text-slate-700 mb-1.5">Catatan Pemeriksaan Saat Kembali</label>
                <textarea name="notes" id="notes" rows="3"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                    placeholder="Kelengkapan charger, tas laptop, kondisi fisik...">{{ old('notes') }}</textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('loans.index') }}" class="px-4 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:text-slate-800 transition">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium shadow-sm transition">
                    Konfirmasi Pengembalian
                </button>
            </div>
        </form>
    </div>
</x-layouts.app>
