@extends('layouts.app')

@section('title', 'Detail Subscription')

@section('content')
    <div class="flex items-center justify-between pb-4 border-b border-slate-200 mb-6">
        <h2 class="text-xl font-bold text-slate-900">Detail Subscription</h2>
        <a href="{{ route('subscriptions.index') }}"
            class="inline-flex items-center px-4 py-2 bg-white hover:bg-slate-50 text-slate-700 text-sm font-medium rounded-lg border border-slate-300 shadow-sm transition">
            Kembali
        </a>
    </div>

    <div>
        <h3>INI HALAMAN DETAIL SUBSCRIPTION</h3>
        <p>Kanvas kosong untuk rincian data lengkap langganan beserta riwayat free trial dan pengingat terkait.</p>
    </div>
@endsection