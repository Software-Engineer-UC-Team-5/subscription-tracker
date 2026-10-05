@extends('layouts.app')

@section('title', 'Metode Pembayaran')

@section('content')
    <div class="flex items-center justify-between pb-4 border-b border-slate-200 mb-6">
        <h2 class="text-xl font-bold text-slate-900">Metode Pembayaran</h2>
        <a href="{{ route('payment-methods.create') }}"
            class="inline-flex items-center px-4 py-2 bg-primary hover:bg-primary-hover text-white text-sm font-medium rounded-lg shadow-sm transition">
            + Tambah Metode
        </a>
    </div>

    <div>
        <h3>INI HALAMAN DAFTAR METODE PEMBAYARAN</h3>
        <p>Kanvas kosong untuk daftar metode bayar (kartu kredit, e-wallet, transfer bank, dll).</p>
    </div>
@endsection