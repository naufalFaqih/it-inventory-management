@php
    $emailError = $errors->first('email');
    $otherErrors = collect($errors->keys())
        ->reject(fn (string $key): bool => $key === 'email')
        ->flatMap(fn (string $key): array => $errors->get($key))
        ->values();

    $features = [
        [
            'title' => 'Inventaris terpusat',
            'description' => 'Seluruh aset, kategori, dan lokasi tercatat dalam satu sumber data.',
            'path' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
        ],
        [
            'title' => 'Mutasi & peminjaman terlacak',
            'description' => 'Setiap perpindahan dan peminjaman punya riwayat yang jelas.',
            'path' => 'M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4',
        ],
        [
            'title' => 'Pemeliharaan & audit',
            'description' => 'Jadwal servis dan jejak aktivitas tim IT terekam otomatis.',
            'path' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
        ],
    ];
@endphp

<x-layouts.guest>
    <div class="flex min-h-screen flex-col lg:grid lg:grid-cols-[1.05fr_1fr]">

        {{-- Panel brand --}}
        <aside class="relative isolate flex flex-col overflow-hidden bg-slate-950 px-6 py-8 text-white sm:px-10 lg:justify-between lg:p-12 xl:p-16">

            {{-- Lapisan latar: aurora + grid --}}
            <div aria-hidden="true" class="pointer-events-none absolute inset-0 -z-10">
                <div class="absolute -left-32 -top-40 size-[34rem] animate-aurora bg-[radial-gradient(circle_at_center,rgba(99,102,241,0.38),transparent_65%)]"></div>
                <div class="absolute -bottom-48 -right-32 size-[36rem] animate-aurora-reverse bg-[radial-gradient(circle_at_center,rgba(217,70,239,0.22),transparent_65%)]"></div>
                <div class="absolute left-1/3 top-1/3 size-80 animate-aurora bg-[radial-gradient(circle_at_center,rgba(34,211,238,0.18),transparent_65%)]"></div>

                <svg class="absolute inset-0 size-full text-white/[0.07]" aria-hidden="true">
                    <defs>
                        <pattern id="login-grid" width="46" height="46" patternUnits="userSpaceOnUse">
                            <path d="M46 0H0V46" fill="none" stroke="currentColor" stroke-width="1" />
                        </pattern>
                    </defs>
                    <rect width="100%" height="100%" fill="url(#login-grid)" />
                </svg>

                <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/40 to-transparent"></div>
            </div>

            {{-- Brand lockup --}}
            <div class="relative z-10 flex animate-fade-up items-center gap-3">
                <div class="flex size-11 items-center justify-center rounded-2xl bg-indigo-600 text-white shadow-lg shadow-indigo-950/60">
                    <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" />
                    </svg>
                </div>
                <div class="leading-tight">
                    <p class="text-sm font-semibold tracking-tight text-white">IT Inventory Trans</p>
                    <p class="text-xs text-slate-400">Asset Operations</p>
                </div>
            </div>

            {{-- Narasi (desktop) --}}
            <div class="relative z-10 mt-12 hidden animate-fade-up [animation-delay:90ms] lg:block">
                <p class="text-xs font-semibold uppercase tracking-[0.22em] text-indigo-300">IT Inventory Management</p>
                <p class="mt-4 max-w-md text-3xl font-semibold leading-[1.15] tracking-tight text-white xl:text-4xl">
                    Kelola aset IT tanpa kehilangan jejak.
                </p>
                <p class="mt-4 max-w-sm text-sm leading-relaxed text-slate-400">
                    Satu tempat untuk inventaris, mutasi, peminjaman, dan pemeliharaan — rapi serta dapat ditelusuri.
                </p>

                <ul class="mt-10 space-y-5">
                    @foreach ($features as $feature)
                        <li class="flex items-start gap-3">
                            <span class="mt-0.5 flex size-8 shrink-0 items-center justify-center rounded-lg bg-white/5 ring-1 ring-white/10">
                                <svg class="size-4 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $feature['path'] }}" />
                                </svg>
                            </span>
                            <div>
                                <p class="text-sm font-medium text-white">{{ $feature['title'] }}</p>
                                <p class="mt-0.5 text-xs leading-relaxed text-slate-400">{{ $feature['description'] }}</p>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>

            <p class="relative z-10 mt-10 hidden animate-fade-up text-xs text-slate-500 [animation-delay:180ms] lg:block">
                Aplikasi Internal Divisi IT &bull; Versi 1.0
            </p>
        </aside>

        {{-- Panel form --}}
        <main class="relative isolate flex flex-1 items-center justify-center overflow-hidden bg-white px-5 py-12 sm:px-8 lg:px-10"
            data-login-glow-host>
            <div aria-hidden="true"
                class="pointer-events-none absolute inset-0 z-0 opacity-0 transition-opacity duration-500"
                data-login-glow
                style="background-image: radial-gradient(520px circle at var(--login-glow-x, 50%) var(--login-glow-y, 0%), rgb(99 102 241 / 0.12), transparent 62%);"></div>

            <div class="relative z-10 w-full max-w-sm animate-fade-up">
                <div class="@if ($errors->any()) animate-shake @endif">
                    <header class="text-center lg:text-left">
                        <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Selamat datang kembali</h1>
                        <p class="mt-2 text-sm leading-relaxed text-slate-500">
                            Masuk untuk melanjutkan ke dashboard aset dan operasional tim IT.
                        </p>
                    </header>

                    @if ($otherErrors->isNotEmpty())
                        <div class="mt-6 rounded-2xl border border-rose-200 bg-rose-50/80 p-4 text-sm text-rose-700" role="alert">
                            <div class="flex items-start gap-2.5">
                                <svg class="mt-0.5 size-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                <ul class="space-y-1">
                                    @foreach ($otherErrors as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif

                    <form action="{{ route('login') }}" method="POST" class="mt-8 space-y-5" data-login-form>
                        @csrf

                        <div>
                            <label for="email" class="mb-1.5 block text-sm font-medium text-slate-700">Alamat Email</label>
                            <div class="relative">
                                <span class="pointer-events-none absolute inset-y-0 left-0 flex w-11 items-center justify-center text-slate-400">
                                    <svg class="size-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                </span>
                                <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                                    autocomplete="username" inputmode="email" spellcheck="false"
                                    placeholder="contoh: admin@inventory.local"
                                    @if ($emailError) aria-invalid="true" aria-describedby="email-error" @endif
                                    @class([
                                        'w-full rounded-xl border bg-white py-3 pl-11 pr-4 text-sm text-slate-900 placeholder:text-slate-400 transition duration-200 focus:outline-none focus:ring-4',
                                        'border-rose-300 focus:border-rose-400 focus:ring-rose-500/15' => (bool) $emailError,
                                        'border-slate-200 hover:border-slate-300 focus:border-indigo-500 focus:ring-indigo-500/15' => ! $emailError,
                                    ])>
                            </div>

                            @if ($emailError)
                                <p id="email-error" class="mt-1.5 flex items-start gap-1.5 text-xs font-medium text-rose-600">
                                    <svg class="mt-px size-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span>{{ $emailError }}</span>
                                </p>
                            @endif
                        </div>

                        <div>
                            <label for="password" class="mb-1.5 block text-sm font-medium text-slate-700">Kata Sandi</label>
                            <div class="relative">
                                <span class="pointer-events-none absolute inset-y-0 left-0 flex w-11 items-center justify-center text-slate-400">
                                    <svg class="size-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                </span>
                                <input type="password" name="password" id="password" required
                                    autocomplete="current-password" placeholder="••••••••"
                                    aria-describedby="caps-hint"
                                    class="w-full rounded-xl border border-slate-200 bg-white py-3 pl-11 pr-12 text-sm text-slate-900 placeholder:text-slate-400 transition duration-200 hover:border-slate-300 focus:border-indigo-500 focus:outline-none focus:ring-4 focus:ring-indigo-500/15">

                                <button type="button" data-password-toggle aria-controls="password" aria-pressed="false"
                                    aria-label="Tampilkan kata sandi"
                                    class="absolute inset-y-0 right-0 flex w-12 items-center justify-center rounded-r-xl text-slate-400 transition hover:text-slate-600 focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-inset focus-visible:ring-indigo-500/20">
                                    <svg data-icon-show class="size-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    <svg data-icon-hide class="hidden size-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                    </svg>
                                </button>
                            </div>

                            <p id="caps-hint" data-caps-hint aria-live="polite"
                                class="mt-1.5 flex items-center gap-1.5 text-xs font-medium text-amber-600" style="display: none">
                                <svg class="size-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>Caps Lock sedang aktif</span>
                            </p>
                        </div>

                        <label for="remember" class="flex cursor-pointer items-center gap-3 text-sm text-slate-600 select-none">
                            <span class="relative inline-flex h-6 w-11 shrink-0 items-center">
                                <input type="checkbox" name="remember" id="remember" value="1" class="peer sr-only">
                                <span class="h-6 w-11 rounded-full bg-slate-200 transition-colors duration-200 peer-checked:bg-indigo-600 peer-focus-visible:ring-4 peer-focus-visible:ring-indigo-500/20"></span>
                                <span class="pointer-events-none absolute left-0.5 size-5 rounded-full bg-white shadow-sm ring-1 ring-slate-900/5 transition-transform duration-200 peer-checked:translate-x-5"></span>
                            </span>
                            <span>Ingat saya di perangkat ini</span>
                        </label>

                        <button type="submit" data-submit-button
                            class="group relative flex w-full items-center justify-center gap-2 overflow-hidden rounded-xl bg-indigo-600 px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-indigo-600/20 transition duration-200 hover:bg-indigo-500 hover:shadow-indigo-600/30 focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-indigo-500/30 active:scale-[0.99] disabled:cursor-not-allowed disabled:opacity-80">
                            <span aria-hidden="true" class="pointer-events-none absolute inset-0 -translate-x-full bg-gradient-to-r from-transparent via-white/25 to-transparent transition-transform duration-700 group-hover:translate-x-full"></span>

                            <svg data-submit-spinner class="hidden size-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                                <path class="opacity-90" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z" />
                            </svg>

                            <span data-submit-label>Masuk ke Sistem</span>

                            <svg data-submit-arrow class="size-4 transition-transform duration-200 group-hover:translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </button>
                    </form>

                    <p class="mt-8 text-center text-xs leading-relaxed text-slate-400">
                        Butuh akses? Hubungi Administrator IT untuk pembuatan akun.
                    </p>
                </div>
            </div>
        </main>
    </div>
</x-layouts.guest>
