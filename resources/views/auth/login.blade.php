@extends('layouts.guest')

@section('title', 'Masuk')

@section('content')
    @php
        // Gaya input desain: abu-abu saat diam, putih dengan border merah saat fokus, merah tua saat error
        $inputClass = 'w-full h-[52px] rounded-2xl px-[18px] text-base text-ink placeholder:text-[#A1A1AA] outline-none transition focus:bg-white focus:border-2 focus:border-primary';
        $inputState = fn (string $field) => $errors->has($field)
            ? 'bg-white border-2 border-[#B42318]'
            : 'bg-[#F4F4F5] border-[1.5px] border-[#E4E4E7]';
    @endphp

    <h1 class="text-[32px] leading-[38px] font-bold text-ink tracking-[-0.4px]">Masuk</h1>
    <p class="mt-1.5 text-base leading-6 text-[#6B6B73]">Lanjut kelola langgananmu.</p>

    <form action="{{ route('login') }}" method="POST" class="mt-8 flex flex-col">
        @csrf

        <div class="flex flex-col gap-2">
            <label for="email" class="text-sm font-semibold text-ink">Email</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="nama@email.com"
                required autofocus autocomplete="email"
                @error('email') aria-invalid="true" aria-describedby="email-error" @enderror
                class="{{ $inputClass }} {{ $inputState('email') }}">
            @error('email')
                <p id="email-error" class="text-sm text-[#B42318]">{{ $message }}</p>
            @enderror
        </div>

        <div class="mt-[18px] flex flex-col gap-2">
            <label for="password" class="text-sm font-semibold text-ink">Kata sandi</label>
            <div class="relative">
                <input type="password" id="password" name="password" placeholder="••••••••" required
                    autocomplete="current-password"
                    @error('password') aria-invalid="true" aria-describedby="password-error" @enderror
                    class="{{ $inputClass }} {{ $inputState('password') }} pr-14">
                <!-- Tombol tampilkan / sembunyikan kata sandi -->
                <button type="button" data-password-toggle="password" aria-label="Tampilkan kata sandi"
                    aria-pressed="false"
                    class="absolute right-1 top-1 w-11 h-11 rounded-xl flex items-center justify-center text-[#6B6B73] hover:text-ink">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                    </svg>
                </button>
            </div>
            @error('password')
                <p id="password-error" class="text-sm text-[#B42318]">{{ $message }}</p>
            @enderror
        </div>

        <label for="remember" class="mt-2.5 min-h-11 flex items-center gap-3 text-[15px] text-[#3F3F46] cursor-pointer">
            <input type="checkbox" id="remember" name="remember" checked class="w-5 h-5 m-0 accent-primary">
            Ingat saya di perangkat ini
        </label>

        <button type="submit"
            class="mt-3.5 h-14 w-full rounded-full bg-primary hover:bg-primary-hover text-white text-base font-bold transition">
            Masuk
        </button>
    </form>

    <p class="mt-[22px] text-center text-[15px] text-[#52525B]">
        Belum punya akun?
        <a href="{{ route('register') }}" class="font-bold text-primary hover:text-primary-hover">Daftar</a>
    </p>

    <script>
        // Ganti tipe input agar kata sandi bisa dilihat sebelum dikirim
        document.querySelectorAll('[data-password-toggle]').forEach(button => {
            button.addEventListener('click', () => {
                const input = document.getElementById(button.dataset.passwordToggle);
                const show = input.type === 'password';
                input.type = show ? 'text' : 'password';
                button.setAttribute('aria-pressed', show);
                button.setAttribute('aria-label', show ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi');
            });
        });
    </script>
@endsection
