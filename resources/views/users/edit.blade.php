<x-layouts.app>
    <x-slot:header>Edit Pengguna: {{ $user->name }}</x-slot:header>

    <div class="max-w-2xl bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8">
        <form action="{{ route('users.update', $user) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div>
                <label for="name" class="block text-sm font-medium text-slate-700 mb-1.5">Nama Lengkap</label>
                <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                    class="w-full px-4 py-2.5 rounded-xl border @error('name') border-rose-300 @else border-slate-300 @enderror focus:ring-2 focus:ring-indigo-500 focus:outline-none text-sm">
                @error('name')
                    <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-slate-700 mb-1.5">Alamat Email</label>
                <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required
                    class="w-full px-4 py-2.5 rounded-xl border @error('email') border-rose-300 @else border-slate-300 @enderror focus:ring-2 focus:ring-indigo-500 focus:outline-none text-sm">
                @error('email')
                    <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-slate-700 mb-1.5">Kata Sandi Baru (Kosongkan jika tidak ingin diubah)</label>
                <input type="password" name="password" id="password"
                    class="w-full px-4 py-2.5 rounded-xl border @error('password') border-rose-300 @else border-slate-300 @enderror focus:ring-2 focus:ring-indigo-500 focus:outline-none text-sm"
                    placeholder="Minimal 8 karakter jika diubah">
                @error('password')
                    <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="role_id" class="block text-sm font-medium text-slate-700 mb-1.5">Role / Peran</label>
                    <select name="role_id" id="role_id" required
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 focus:outline-none text-sm">
                        @foreach ($roles as $role)
                            <option value="{{ $role->id }}" {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>
                                {{ $role->label }}
                            </option>
                        @endforeach
                    </select>
                    @error('role_id')
                        <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="department_id" class="block text-sm font-medium text-slate-700 mb-1.5">Departemen</label>
                    <select name="department_id" id="department_id"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 focus:outline-none text-sm">
                        <option value="">(Opsional) Pilih Departemen</option>
                        @foreach ($departments as $dept)
                            <option value="{{ $dept->id }}" {{ old('department_id', $user->department_id) == $dept->id ? 'selected' : '' }}>
                                {{ $dept->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('department_id')
                        <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $user->is_active) ? 'checked' : '' }}
                        class="w-4 h-4 rounded text-indigo-600 border-slate-300 focus:ring-indigo-500">
                    <span class="text-sm font-medium text-slate-700">Akun Aktif (Dapat Login)</span>
                </label>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('users.index') }}" class="px-4 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:text-slate-800 transition">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium shadow-sm transition">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</x-layouts.app>
