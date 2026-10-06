@extends('layouts.app')

@section('title', 'Edit Metode Pembayaran')

@section('content')
    <!-- Header formulir edit dan tautan kembali ke daftar -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-slate-200 mb-6">
        <h2 class="text-xl font-bold text-slate-900">Edit Metode Pembayaran</h2>
        <a href="{{ route('payment-methods.index') }}"
            class="inline-flex items-center px-4 py-2 bg-white hover:bg-slate-50 text-slate-700 text-sm font-medium rounded-lg border border-slate-300 shadow-sm transition">
            Kembali
        </a>
    </div>

    <!-- Formulir edit mengirim pembaruan melalui method PUT dengan token CSRF -->
    <form action="{{ route('payment-methods.update', $paymentMethod) }}" method="POST"
        class="max-w-xl bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-5">
        @csrf
        @method('PUT')

        <!-- Tampilkan nama tersimpan atau isian sebelumnya jika validasi gagal -->
        <div>
            <label for="name" class="block text-sm font-medium text-slate-700 mb-2">Nama metode pembayaran</label>
            <input type="text" id="name" name="name" value="{{ old('name', $paymentMethod->name) }}" required maxlength="100"
                aria-describedby="name-help @error('name') name-error @enderror"
                @error('name') aria-invalid="true" @enderror
                class="w-full px-3 py-2 text-sm rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            <p id="name-help" class="mt-2 text-sm text-slate-600">
                Jangan masukkan nomor kartu, CVV, atau PIN.
            </p>
            @error('name')
                <p id="name-error" role="alert" class="mt-2 text-sm text-rose-700">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center gap-4">
            <button type="submit"
                class="px-4 py-2 bg-primary hover:bg-primary-hover text-white text-sm font-medium rounded-lg shadow-sm transition">
                Simpan Perubahan
            </button>
            <a href="{{ route('payment-methods.index') }}" class="text-sm text-slate-700 hover:underline">Batal</a>
        </div>
    </form>
@endsection
