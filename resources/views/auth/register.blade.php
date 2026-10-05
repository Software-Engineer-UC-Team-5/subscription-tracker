@extends('layouts.app')

@section('title', 'Daftar Akun Baru')

@section('content')
    <div class="max-w-md mx-auto bg-white rounded-xl shadow-sm border border-slate-200 p-6 sm:p-8">
        <div class="text-center mb-6">
            <h2 class="text-2xl font-bold text-slate-900 tracking-tight">Daftar Akun Baru</h2>
            <p class="text-sm text-slate-500 mt-1">Buat akun untuk mulai melacak seluruh langganan Anda.</p>
        </div>

        <form action="{{ route('register') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label for="name" class="block text-sm font-medium text-slate-700 mb-1">Nama Lengkap</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="Contoh: Budi Santoso"
                    required autofocus
                    class="w-full px-3.5 py-2 border border-slate-300 rounded-lg text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition">
                @error('name')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-slate-700 mb-1">Alamat Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="nama@email.com"
                    required
                    class="w-full px-3.5 py-2 border border-slate-300 rounded-lg text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition">
                @error('email')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-slate-700 mb-1">Kata Sandi (Minimal 8 Karakter)</label>
                <input type="password" id="password" name="password" placeholder="••••••••" required
                    class="w-full px-3.5 py-2 border border-slate-300 rounded-lg text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition">
                @error('password')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-slate-700 mb-1">Ulangi Kata Sandi</label>
                <input type="password" id="password_confirmation" name="password_confirmation" placeholder="••••••••" required
                    class="w-full px-3.5 py-2 border border-slate-300 rounded-lg text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition">
            </div>

            <button type="submit"
                class="w-full py-2.5 px-4 bg-primary hover:bg-primary-hover text-white font-medium text-sm rounded-lg shadow-sm transition duration-150">
                Daftar Sekarang
            </button>
        </form>

        <div class="mt-6 pt-6 border-t border-slate-200 text-center">
            <p class="text-sm text-slate-600">
                Sudah memiliki akun?
                <a href="{{ route('login') }}" class="font-medium text-primary hover:text-primary-hover">Masuk di sini</a>
            </p>
        </div>
    </div>
@endsection