@extends('layouts.app')

@section('title', 'Tambah Metode Pembayaran')

@section('content')
    <div class="flex items-center justify-between pb-4 border-b border-slate-200 mb-6">
        <h2 class="text-xl font-bold text-slate-900">Tambah Metode Pembayaran</h2>
        <a href="{{ route('payment-methods.index') }}"
            class="inline-flex items-center px-4 py-2 bg-white hover:bg-slate-50 text-slate-700 text-sm font-medium rounded-lg border border-slate-300 shadow-sm transition">
            Kembali
        </a>
    </div>

    <div>
        <h3>INI HALAMAN FORMULIR TAMBAH METODE PEMBAYARAN</h3>
        <p>Kanvas kosong untuk formulir input nama label/alias metode pembayaran baru.</p>
    </div>
@endsection