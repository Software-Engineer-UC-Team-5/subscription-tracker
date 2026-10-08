@extends('layouts.app')

@section('title', 'Edit Kategori')

@section('content')
    <!-- Header formulir edit dan tautan kembali ke daftar -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-slate-200 mb-6">
        <h1 class="text-xl font-bold text-slate-900">Edit Kategori</h1>
        <a href="{{ route('categories.index', [], false) }}"
            class="inline-flex items-center px-4 py-2 bg-white hover:bg-slate-50 text-slate-700 text-sm font-medium rounded-lg border border-slate-300 shadow-sm transition">
            Kembali
        </a>
    </div>

    <!-- Pembaruan dikirim melalui formulir PUT dengan token CSRF -->
    <form action="{{ route('categories.update', $category, false) }}" method="POST"
        class="max-w-xl bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-5">
        @csrf
        @method('PUT')
        @include('categories._form', ['category' => $category])

        <div class="flex items-center gap-4">
            <button type="submit"
                class="px-4 py-2 bg-primary hover:bg-primary-hover text-white text-sm font-medium rounded-lg shadow-sm transition">
                Simpan Perubahan
            </button>
            <a href="{{ route('categories.index', [], false) }}" class="text-sm text-slate-700 hover:underline">Batal</a>
        </div>
    </form>
@endsection
