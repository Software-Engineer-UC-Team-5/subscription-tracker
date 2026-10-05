@extends('layouts.app')

@section('title', 'Daftar Subscription')

@section('content')
    <div class="flex items-center justify-between pb-4 border-b border-slate-200 mb-6">
        <h2 class="text-xl font-bold text-slate-900">Daftar Subscription</h2>
        <a href="{{ route('subscriptions.create') }}"
            class="inline-flex items-center px-4 py-2 bg-primary hover:bg-primary-hover text-white text-sm font-medium rounded-lg shadow-sm transition">
            + Tambah Subscription
        </a>
    </div>

    <div>
        <h3>INI HALAMAN DAFTAR SUBSCRIPTION</h3>
        <p>Kanvas kosong untuk daftar langganan, filter pencarian (nama, kategori, metode bayar, status), dan tombol aksi.</p>
    </div>
@endsection