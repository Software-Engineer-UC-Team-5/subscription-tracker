@extends('layouts.guest')

@section('title', 'Daftar akun')

@section('content')
    @php
        // Gaya input desain: abu-abu saat diam, putih dengan border merah saat fokus, merah tua saat error
        $inputClass = 'w-full h-[52px] rounded-2xl px-[18px] text-base text-ink placeholder:text-[#A1A1AA] outline-none transition focus:bg-white focus:border-2 focus:border-primary';
        $inputState = fn (string $field) => $errors->has($field)
            ? 'bg-white border-2 border-[#B42318]'
            : 'bg-[#F4F4F5] border-[1.5px] border-[#E4E4E7]';
    @endphp

    <h1 class="text-[32px] leading-[38px] font-bold text-ink tracking-[-0.4px]">Daftar akun</h1>
    <p class="mt-1.5 text-base leading-6 text-[#6B6B73]">Buat akun untuk mulai melacak langgananmu.</p>

    <form action="{{ route('register') }}" method="POST" class="mt-4 flex flex-col">
        @csrf

        <div class="mt-4 flex flex-col gap-2">
            <label for="name" class="text-sm font-semibold text-ink">Nama lengkap</label>
            <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="Contoh: Budi Santoso"
                required autofocus autocomplete="name" maxlength="100"
                @error('name') aria-invalid="true" aria-describedby="name-error" @enderror
                class="{{ $inputClass }} {{ $inputState('name') }}">
            @error('name')
                <p id="name-error" class="text-sm text-[#B42318]">{{ $message }}</p>
            @enderror
        </div>

        <div class="mt-4 flex flex-col gap-2">
            <label for="email" class="text-sm font-semibold text-ink">Alamat email</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="nama@email.com"
                required autocomplete="email" maxlength="100"
                @error('email') aria-invalid="true" aria-describedby="email-error" @enderror
                class="{{ $inputClass }} {{ $inputState('email') }}">
            @error('email')
                <p id="email-error" class="text-sm text-[#B42318]">{{ $message }}</p>
            @enderror
        </div>

        <div class="mt-4 flex flex-col gap-2">
            <label for="password" class="text-sm font-semibold text-ink">Kata sandi (min. 8 karakter)</label>
            <input type="password" id="password" name="password" placeholder="••••••••" required minlength="8"
                autocomplete="new-password"
                @error('password') aria-invalid="true" aria-describedby="password-error" @enderror
                class="{{ $inputClass }} {{ $inputState('password') }}">
            @error('password')
                <p id="password-error" class="text-sm text-[#B42318]">{{ $message }}</p>
            @enderror
        </div>

        <div class="mt-4 flex flex-col gap-2">
            <label for="password_confirmation" class="text-sm font-semibold text-ink">Ulangi kata sandi</label>
            <input type="password" id="password_confirmation" name="password_confirmation" placeholder="••••••••"
                required autocomplete="new-password"
                class="{{ $inputClass }} {{ $inputState('password_confirmation') }}">
        </div>

        <button type="submit"
            class="mt-6 h-14 w-full rounded-full bg-primary hover:bg-primary-hover text-white text-base font-bold transition">
            Daftar sekarang
        </button>
    </form>

    <p class="mt-[22px] text-center text-[15px] text-[#52525B]">
        Sudah punya akun?
        <a href="{{ route('login') }}" class="font-bold text-primary hover:text-primary-hover">Masuk</a>
    </p>
@endsection
