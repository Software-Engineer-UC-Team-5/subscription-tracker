@extends('layouts.app')

@section('title', 'Daftar Kategori')

@section('content')
    <div class="flex items-center justify-between pb-4 border-b border-slate-200 mb-6">
        <h2 class="text-xl font-bold text-slate-900">Kategori</h2>
        <a href="{{ route('categories.create') }}"
            class="inline-flex items-center px-4 py-2 bg-primary hover:bg-primary-hover text-white text-sm font-medium rounded-lg shadow-sm transition">
            + Tambah Kategori
        </a>
    </div>

    <div>
        <h3>INI HALAMAN DAFTAR KATEGORI</h3>
        <p>Kanvas kosong siap diisi desain Google Stitch.</p>
    </div>
@endsection