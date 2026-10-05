@extends('layouts.guest')

@section('title', 'Masuk Akun')

@section('content')
    <div class="mb-6">
        <h2 class="text-xl font-bold text-slate-900 tracking-tight">Masuk ke Akun Anda</h2>
        <p class="text-sm text-slate-500 mt-1">Masukkan kredensial Anda untuk mengakses dashboard langganan.</p>
    </div>

    <form action="{{ route('login') }}" method="POST" class="space-y-4">
        @csrf

        <div>
            <label for="email" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Alamat
                Email</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="nama@email.com" required
                autofocus
                class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition">
            @error('email')
                <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Kata
                Sandi</label>
            <input type="password" id="password" name="password" placeholder="••••••••" required
                class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition">
            @error('password')
                <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center justify-between pt-1">
            <div class="flex items-center">
                <input type="checkbox" id="remember" name="remember" checked
                    class="h-4 w-4 rounded border-slate-300 text-primary focus:ring-primary">
                <label for="remember" class="ml-2 block text-xs font-medium text-slate-600">Ingat Sesi Saya</label>
            </div>
        </div>

        <button type="submit"
            class="w-full py-2.5 px-4 bg-primary hover:bg-primary-hover text-white font-semibold text-sm rounded-xl shadow-sm transition duration-150">
            Masuk
        </button>
    </form>

    <div class="mt-6 pt-5 border-t border-slate-100 text-center">
        <p class="text-xs text-slate-600">
            Belum memiliki akun?
            <a href="{{ route('register') }}" class="font-semibold text-primary hover:underline">Daftar sekarang</a>
        </p>
    </div>
@endsection