@extends('layouts.app')

@section('title', 'Masuk Akun')

@section('content')
    <div class="max-w-md mx-auto bg-white rounded-xl shadow-sm border border-slate-200 p-6 sm:p-8">
        <div class="text-center mb-6">
            <h2 class="text-2xl font-bold text-slate-900 tracking-tight">Masuk Akun</h2>
            <p class="text-sm text-slate-500 mt-1">Masukkan email dan kata sandi Anda untuk mengakses sistem.</p>
        </div>

        <form action="{{ route('login') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label for="email" class="block text-sm font-medium text-slate-700 mb-1">Alamat Email</label>
                <input type="email" id="email" name="email" value="{{ old('email', 'user@example.com') }}"
                    placeholder="nama@email.com" required autofocus
                    class="w-full px-3.5 py-2 border border-slate-300 rounded-lg text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition">
                @error('email')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-slate-700 mb-1">Kata Sandi</label>
                <input type="password" id="password" name="password" value="password" placeholder="••••••••" required
                    class="w-full px-3.5 py-2 border border-slate-300 rounded-lg text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition">
                @error('password')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center">
                <input type="checkbox" id="remember" name="remember" checked
                    class="h-4 w-4 rounded border-slate-300 text-primary focus:ring-primary">
                <label for="remember" class="ml-2 block text-sm text-slate-600">Ingat Sesi Saya</label>
            </div>

            <button type="submit"
                class="w-full py-2.5 px-4 bg-primary hover:bg-primary-hover text-white font-medium text-sm rounded-lg shadow-sm transition duration-150">
                Masuk
            </button>
        </form>

        <div class="mt-6 pt-6 border-t border-slate-200 text-center">
            <p class="text-sm text-slate-600">
                Belum memiliki akun?
                <a href="{{ route('register') }}" class="font-medium text-primary hover:text-primary-hover">Daftar di sini</a>
            </p>
        </div>
    </div>
@endsection