@extends('layouts.app')

@section('title', 'Dashboard - Analisis Pengeluaran')

@section('content')
    <div class="space-y-6">
        <!-- Header Dashboard -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-5 border-b border-slate-200 gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight">
                    Dashboard & Analisis Pengeluaran
                </h1>
                <p class="text-sm text-slate-500 mt-1">
                    Ringkasan pemantauan seluruh subscription aktif dan kalkulasi estimasi pengeluaran Anda.
                </p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('subscriptions.create') }}"
                    class="inline-flex items-center justify-center px-4 py-2.5 text-sm font-semibold text-white bg-primary hover:bg-primary-hover rounded-xl shadow-sm transition">
                    + Tambah Subscription
                </a>
            </div>
        </div>

        <!-- Banner Info Pemantauan (Inspirasi Banner Dayaboard SS 1) -->
        <div
            class="bg-blue-50/70 border border-blue-200/80 rounded-2xl p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-start sm:items-center gap-3.5">
                <div
                    class="w-10 h-10 rounded-xl bg-blue-600 text-white flex items-center justify-center flex-shrink-0 shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="font-bold text-slate-900 text-sm">Sistem Pelacak Beban Pengeluaran Otomatis</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800">Aktif</span>
                    </div>
                    <p class="text-xs text-slate-600 mt-0.5">
                        Pengingat email dan scheduler berjalan setiap 15 menit untuk memantau jatuh tempo autodebit dan masa
                        free trial.
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('reminders.index') }}"
                    class="px-3.5 py-1.5 text-xs font-semibold text-blue-700 bg-white hover:bg-blue-50 border border-blue-200 rounded-lg shadow-sm transition">
                    Kelola Pengingat &rarr;
                </a>
            </div>
        </div>

        <!-- Ringkasan Metrik Keuangan (FR-005) -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Ringkasan Subscription Aktif -->
            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between text-slate-500 mb-2">
                    <span class="text-xs font-semibold uppercase tracking-wider">
                        Subscription Aktif
                    </span>
                    <span
                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200">
                        Aktif
                    </span>
                </div>
                <div class="mt-2">
                    <div class="text-3xl font-extrabold text-blue-600">
                        {{ $summary['total_active'] }}
                    </div>
                    <p class="text-xs text-slate-400 mt-1">
                        Layanan subscription aktif saat ini
                    </p>
                </div>
            </div>

            <!-- Estimasi Pengeluaran Bulanan -->
            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between text-slate-500 mb-2">
                    <span class="text-xs font-semibold uppercase tracking-wider">Estimasi Pengeluaran / Bulan</span>
                    <span
                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                        Bulanan
                    </span>
                </div>
                <div class="mt-2">
                    <div class="text-3xl font-extrabold text-emerald-600">Rp
                        {{ number_format($summary['monthly_expense'], 0, ',', '.') }}
                    </div>
                    <p class="text-xs text-slate-400 mt-1">Normalisasi seluruh siklus billing ke bulanan</p>
                </div>
            </div>

            <!-- Estimasi Pengeluaran Tahunan -->
            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between text-slate-500 mb-2">
                    <span class="text-xs font-semibold uppercase tracking-wider">Estimasi Pengeluaran / Tahun</span>
                    <span
                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-indigo-50 text-indigo-700 border border-indigo-200">
                        Tahunan
                    </span>
                </div>
                <div class="mt-2">
                    <div class="text-3xl font-extrabold text-indigo-600">Rp
                        {{ number_format($summary['yearly_expense'], 0, ',', '.') }}
                    </div>
                    <p class="text-xs text-slate-400 mt-1">Proyeksi pengeluaran subscription setahun penuh</p>
                </div>
            </div>
        </div>

        <!-- Bagian Tagihan Mendatang 7 Hari ke Depan (FR-005, NFR-002) -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden mb-6">
            <div class="p-5 border-b border-slate-200 flex items-center justify-between">
                <div>
                    <h2 class="text-base font-semibold text-slate-900">Tagihan Mendatang (7 Hari ke Depan)</h2>
                    <p class="text-xs text-slate-500">Layanan yang akan memasuki tanggal autodebit atau batas akhir uji coba
                    </p>
                </div>
                <a href="{{ route('subscriptions.index') }}" class="text-xs font-medium text-blue-600 hover:text-blue-800">
                    Lihat Semua &rarr;
                </a>
            </div>

            @if ($summary['upcoming_payments']->isEmpty())
                <div class="py-12 px-4 text-center">
                    <div
                        class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-slate-100 text-slate-400 mb-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <p class="text-sm font-medium text-slate-700">Tidak ada tagihan yang jatuh tempo dalam 7 hari ke depan.</p>
                    <p class="text-xs text-slate-400 mt-1">Pengeluaran Anda aman untuk minggu ini!</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-600">
                        <thead class="bg-slate-50 text-xs uppercase font-semibold text-slate-500 border-b border-slate-200">
                            <tr>
                                <th class="px-6 py-3.5">Layanan</th>
                                <th class="px-6 py-3.5">Kategori</th>
                                <th class="px-6 py-3.5">Biaya</th>
                                <th class="px-6 py-3.5">Metode Bayar</th>
                                <th class="px-6 py-3.5">Jatuh Tempo</th>
                                <th class="px-6 py-3.5 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($summary['upcoming_payments'] as $sub)
                                <tr class="hover:bg-slate-50/75 transition">
                                    <td class="px-6 py-4 font-semibold text-slate-900 flex items-center gap-2">
                                        <span>{{ $sub->name }}</span>
                                        @if ($sub->is_free_trial)
                                            <span
                                                class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                                Trial
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-700">
                                            {{ $sub->category->name ?? 'Tanpa Kategori' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 font-medium text-slate-900">
                                        Rp {{ number_format($sub->price, 0, ',', '.') }}
                                        <span class="text-xs text-slate-400 block">/ {{ $sub->billing_period->label() }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-slate-600">
                                        {{ $sub->paymentMethod->name ?? 'Belum ditentukan' }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <span
                                            class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                            {{ $sub->next_payment_date ? $sub->next_payment_date->format('d M Y') : '-' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('subscriptions.show', $sub) }}"
                                            class="text-xs font-semibold text-blue-600 hover:text-blue-800 transition">
                                            Detail
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
@endsection