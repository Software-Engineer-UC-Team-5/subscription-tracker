@extends('layouts.app')

@section('title', 'Edit Kategori')

@section('page_title', 'Edit kategori')
@section('page_subtitle', 'Perubahan langsung berlaku di semua langganan dengan kategori ini.')

@section('page_actions')
    <a href="{{ route('categories.index', [], false) }}"
        class="h-14 px-7 rounded-full border-[1.5px] border-ink-800 bg-ink-900 hover:bg-ink-800 text-white text-base font-bold inline-flex items-center transition">
        Kembali
    </a>
@endsection

@section('content')
    <!-- Pembaruan dikirim melalui formulir PUT dengan token CSRF -->
    <form action="{{ route('categories.update', $category, false) }}" method="POST" class="w-full max-w-[520px]">
        @csrf
        @method('PUT')
        @include('categories._form', ['category' => $category])

        <div class="mt-7 flex flex-wrap items-center gap-3">
            <button type="submit"
                class="h-[52px] px-8 rounded-full bg-primary hover:bg-primary-hover text-white text-[15px] font-bold transition">
                Simpan perubahan
            </button>
            <a href="{{ route('categories.index', [], false) }}"
                class="h-[52px] px-7 rounded-full border-[1.5px] border-ink bg-white hover:bg-[#F4F4F5] text-ink text-[15px] font-bold inline-flex items-center transition">
                Batal
            </a>
        </div>
    </form>
@endsection
