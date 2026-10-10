@extends('layouts.app')

@section('title', 'Tambah Metode Pembayaran')

@section('page_title', 'Tambah metode bayar')
@section('page_subtitle', 'Cukup nama yang mudah kamu kenali, misalnya BCA Debit atau GoPay.')

@section('page_actions')
    <a href="{{ route('payment-methods.index') }}"
        class="h-14 px-7 rounded-full border-[1.5px] border-ink-800 bg-ink-900 hover:bg-ink-800 text-white text-base font-bold inline-flex items-center transition">
        Kembali
    </a>
@endsection

@section('content')
    <!-- Formulir tambah tetap dapat dipakai melalui halaman biasa tanpa popup -->
    <form action="{{ route('payment-methods.store') }}" method="POST" class="w-full max-w-[520px]">
        @csrf

        <!-- NFR-004: Input hanya nama metode pembayaran; pertahankan isian jika validasi gagal -->
        <div class="flex flex-col gap-2">
            <label for="name" class="text-sm font-semibold text-ink">Nama metode pembayaran</label>
            <input type="text" id="name" name="name" value="{{ old('name') }}" required maxlength="100" placeholder="Contoh: BCA Debit"
                aria-describedby="name-help @error('name') name-error @enderror"
                @error('name') aria-invalid="true" @enderror
                class="w-full h-[52px] rounded-2xl px-[18px] text-base text-ink placeholder:text-[#A1A1AA] outline-none transition focus:bg-white focus:border-2 focus:border-primary {{ $errors->has('name') ? 'bg-white border-2 border-[#B42318]' : 'bg-[#F4F4F5] border-[1.5px] border-[#E4E4E7]' }}">
            <p id="name-help" class="text-[13px] leading-5 text-[#6B6B73]">
                Jangan masukkan nomor kartu, CVV, atau PIN.
            </p>
            @error('name')
                <p id="name-error" role="alert" class="text-sm text-[#B42318]">{{ $message }}</p>
            @enderror
        </div>

        <div class="mt-7 flex flex-wrap items-center gap-3">
            <button type="submit"
                class="h-[52px] px-8 rounded-full bg-primary hover:bg-primary-hover text-white text-[15px] font-bold transition">
                Simpan metode
            </button>
            <a href="{{ route('payment-methods.index') }}"
                class="h-[52px] px-7 rounded-full border-[1.5px] border-ink bg-white hover:bg-[#F4F4F5] text-ink text-[15px] font-bold inline-flex items-center transition">
                Batal
            </a>
        </div>
    </form>
@endsection
