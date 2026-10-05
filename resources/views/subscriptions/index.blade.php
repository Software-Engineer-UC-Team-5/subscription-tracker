@extends('layouts.app')

@section('title', 'Daftar Subscription')

@section('content')
    <div class="space-y-6">
        <!-- Header Halaman -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-5 border-b border-slate-200 gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight">Daftar Subscription</h1>
                <p class="text-sm text-slate-500 mt-1">Kelola dan pantau seluruh layanan subscription serta masa uji coba gratis Anda.</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('subscriptions.create') }}"
                    class="inline-flex items-center justify-center px-4 py-2.5 text-sm font-semibold text-white bg-primary hover:bg-primary-hover rounded-xl shadow-sm transition">
                    + Tambah Subscription
                </a>
            </div>
        </div>

        <!-- Bilah Pencarian & Pemfilteran (FR-003 / UC09) -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
            <form method="GET" action="{{ route('subscriptions.index') }}"
                class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-3 items-end">
                <!-- Pencarian Teks Nama Layanan -->
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Cari Nama</label>
                    <input type="text" name="search" value="{{ $filters['search'] ?? '' }}"
                        placeholder="Netflix, Spotify..."
                        class="w-full px-3 py-2 text-sm rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                </div>

                <!-- Filter Kategori -->
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Kategori</label>
                    <select name="category_id"
                        class="w-full px-3 py-2 text-sm rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                        <option value="">Semua Kategori</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}" {{ ($filters['category_id'] ?? '') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Metode Pembayaran -->
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Metode Bayar</label>
                    <select name="payment_method_id"
                        class="w-full px-3 py-2 text-sm rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                        <option value="">Semua Metode</option>
                        @foreach ($paymentMethods as $pm)
                            <option value="{{ $pm->id }}" {{ ($filters['payment_method_id'] ?? '') == $pm->id ? 'selected' : '' }}>
                                {{ $pm->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Status Langganan -->
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Status</label>
                    <select name="status"
                        class="w-full px-3 py-2 text-sm rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                        <option value="">Semua Status</option>
                        @foreach (\App\Enums\SubscriptionStatus::cases() as $st)
                            <option value="{{ $st->value }}" {{ ($filters['status'] ?? '') == $st->value ? 'selected' : '' }}>
                                {{ $st->label() }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Tombol Aksi Filter & Reset -->
                <div class="flex items-center gap-2">
                    <button type="submit"
                        class="w-full px-4 py-2 text-sm font-medium text-white bg-slate-800 hover:bg-slate-900 rounded-lg transition">
                        Filter
                    </button>
                    <a href="{{ route('subscriptions.index') }}"
                        class="px-3 py-2 text-sm font-medium text-slate-600 hover:bg-slate-200 border border-slate-300 rounded-lg transition"
                        title="Reset Filter">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Tabel Daftar Langganan (UC06, FR-002) -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            @if ($subscriptions->isEmpty())
                <div class="py-12 px-4 text-center">
                    <div
                        class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-slate-100 text-slate-400 mb-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </div>
                    <p class="text-sm font-medium text-slate-700">Belum ada data langganan yang cocok dengan kriteria filter.
                    </p>
                    <p class="text-xs text-slate-400 mt-1">Tambahkan layanan langganan baru atau ubah filter pencarian Anda.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-600">
                        <thead class="bg-slate-50 text-xs uppercase font-semibold text-slate-500 border-b border-slate-200">
                            <tr>
                                <th class="px-6 py-3.5">Layanan</th>
                                <th class="px-6 py-3.5">Kategori</th>
                                <th class="px-6 py-3.5">Biaya & Siklus</th>
                                <th class="px-6 py-3.5">Metode Bayar</th>
                                <th class="px-6 py-3.5">Jatuh Tempo</th>
                                <th class="px-6 py-3.5">Status</th>
                                <th class="px-6 py-3.5 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($subscriptions as $sub)
                                <tr class="hover:bg-slate-50/75 transition">
                                    <td class="px-6 py-4 font-semibold text-slate-900">
                                        <div class="flex items-center gap-2">
                                            <a href="{{ route('subscriptions.show', $sub) }}"
                                                class="hover:text-blue-600 transition">
                                                {{ $sub->name }}
                                            </a>
                                            @if ($sub->is_free_trial)
                                                <span
                                                    class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                                    Free Trial
                                                </span>
                                            @endif
                                        </div>
                                        @if ($sub->description)
                                            <span class="text-xs text-slate-400 block truncate max-w-xs">{{ $sub->description }}</span>
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
                                        {{ $sub->paymentMethod->name ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 text-slate-600">
                                        {{ $sub->next_payment_date ? $sub->next_payment_date->format('d M Y') : '-' }}
                                    </td>
                                    <td class="px-6 py-4">
                                        @php
                                            $badgeClass = match ($sub->status->value) {
                                                'ACTIVE' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                                'TRIAL' => 'bg-amber-50 text-amber-700 border-amber-200',
                                                'PAUSED' => 'bg-purple-50 text-purple-700 border-purple-200',
                                                'CANCELLED' => 'bg-slate-100 text-slate-600 border-slate-300',
                                                'EXPIRED' => 'bg-rose-50 text-rose-700 border-rose-200',
                                                default => 'bg-slate-100 text-slate-700 border-slate-200',
                                            };
                                        @endphp
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $badgeClass }}">
                                            {{ $sub->status->label() }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right space-x-2">
                                        <a href="{{ route('subscriptions.show', $sub) }}"
                                            class="text-xs font-medium text-blue-600 hover:text-blue-800">
                                            Detail
                                        </a>
                                        <a href="{{ route('subscriptions.edit', $sub) }}"
                                            class="text-xs font-medium text-slate-600 hover:text-slate-900">
                                            Edit
                                        </a>
                                        <form action="{{ route('subscriptions.destroy', $sub) }}" method="POST" class="inline"
                                            onsubmit="return confirm('Apakah Anda yakin ingin menghapus langganan {{ $sub->name }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-xs font-medium text-rose-600 hover:text-rose-800">
                                                Hapus
                                            </button>
                                        </form>
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