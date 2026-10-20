<x-layouts.app>
    <x-slot:header>Selesaikan Servis & Pemeliharaan Aset</x-slot:header>

    <div class="max-w-xl bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8">
        <!-- Summary Box -->
        <div class="mb-6 p-4 rounded-xl bg-slate-50 border border-slate-100 text-sm space-y-2">
            <div class="flex justify-between">
                <span class="text-slate-500">Perangkat:</span>
                <span class="font-medium text-slate-800">{{ $maintenance->asset?->name }} ({{ $maintenance->asset?->asset_tag }})</span>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-500">Isu:</span>
                <span class="font-medium text-slate-800">{{ $maintenance->issue }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-500">Teknisi:</span>
                <span class="text-slate-700">{{ $maintenance->technician?->name }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-500">Mulai Pengerjaan:</span>
                <span class="text-slate-700">{{ $maintenance->started_at->translatedFormat('d F Y H:i') }}</span>
            </div>
        </div>

        <form action="{{ route('maintenance.complete.process', $maintenance) }}" method="POST" class="space-y-6">
            @csrf

            <div>
                <label for="action_taken" class="block text-sm font-medium text-slate-700 mb-1.5">Tindakan Perbaikan yang Dilakukan</label>
                <textarea name="action_taken" id="action_taken" rows="3" required
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                    placeholder="misal: Penggantian thermal paste, reinstall OS, ganti modul RAM...">{{ old('action_taken') }}</textarea>
                @error('action_taken')
                    <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="completed_at" class="block text-sm font-medium text-slate-700 mb-1.5">Waktu Selesai</label>
                    <input type="datetime-local" name="completed_at" id="completed_at" value="{{ old('completed_at', now()->format('Y-m-d\TH:i')) }}" required
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    @error('completed_at')
                        <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="cost" class="block text-sm font-medium text-slate-700 mb-1.5">Biaya Akhir Riil (IDR)</label>
                    <input type="number" step="0.01" name="cost" id="cost" value="{{ old('cost', $maintenance->cost) }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                        placeholder="0 jika tidak ada biaya">
                </div>
            </div>

            <div>
                <label for="condition_after" class="block text-sm font-medium text-slate-700 mb-1.5">Kondisi Fisik Setelah Servis</label>
                <select name="condition_after" id="condition_after" required
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    <option value="good">Baik (Normal Berfungsi Sempurna)</option>
                    <option value="minor_damage">Rusak Ringan (Masih bisa digunakan dengan catatan)</option>
                </select>
                <p class="text-xs text-slate-400 mt-1">Status aset akan otomatis dikembalikan ke 'Tersedia' / 'Ditetapkan'.</p>
                @error('condition_after')
                    <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="notes" class="block text-sm font-medium text-slate-700 mb-1.5">Catatan Akhir Teknisi (Opsional)</label>
                <textarea name="notes" id="notes" rows="2"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">{{ old('notes') }}</textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('maintenance.index') }}" class="px-4 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:text-slate-800 transition">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium shadow-sm transition">
                    Simpan & Tandai Selesai
                </button>
            </div>
        </form>
    </div>
</x-layouts.app>
