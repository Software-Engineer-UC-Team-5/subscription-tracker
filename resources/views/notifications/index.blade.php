@extends('layouts.app')

@section('title', 'Riwayat Notifikasi')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-5 border-b border-slate-200 gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight">Riwayat Notifikasi</h1>
                <p class="text-sm text-slate-500 mt-1">Daftar pemberitahuan pengingat tagihan dan masa uji coba gratis yang
                    diproses oleh sistem.</p>
            </div>
            <span class="text-xs font-semibold px-3 py-1.5 rounded-full bg-slate-100 text-slate-700">
                Total Notifikasi: {{ $notifications->count() }}
            </span>
        </div>

        @if ($notifications->isEmpty())
            <div class="py-16 px-4 text-center bg-white rounded-xl border border-slate-200 shadow-sm">
                <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-slate-100 text-slate-400 mb-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                    </svg>
                </div>
                <p class="text-sm font-medium text-slate-700">Inbox Notifikasi Masih Kosong</p>
                <p class="text-xs text-slate-400 mt-1">Notifikasi akan muncul di sini secara otomatis saat jadwal pengingat
                    tagihan
                    tiba.</p>
            </div>
        @else
            <div class="space-y-4">
                @foreach ($notifications as $notification)
                    @php
                        $statusClass = match ($notification->status->value) {
                            'SENT' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                            'PENDING' => 'bg-amber-50 text-amber-700 border-amber-200',
                            'FAILED' => 'bg-rose-50 text-rose-700 border-rose-200',
                            'READ' => 'bg-slate-100 text-slate-600 border-slate-200',
                            default => 'bg-slate-100 text-slate-700 border-slate-200',
                        };
                    @endphp
                    <div
                        class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex flex-col sm:flex-row sm:items-start justify-between gap-4 {{ $notification->status->value === 'READ' ? 'opacity-75' : '' }}">
                        <div class="space-y-1">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span
                                    class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider border {{ $statusClass }}">
                                    {{ $notification->status->value }}
                                </span>
                                <span
                                    class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                    {{ $notification->type->label() }}
                                </span>
                                <span class="text-xs text-slate-400">
                                    {{ $notification->created_at ? $notification->created_at->diffForHumans() : '-' }}
                                </span>
                            </div>
                            <h2 class="text-base font-bold text-slate-900">{{ $notification->title }}</h2>
                            <p class="text-sm text-slate-600 leading-relaxed">{{ $notification->message }}</p>
                        </div>

                        <div class="flex sm:flex-col items-center sm:items-end justify-between sm:justify-start gap-2 shrink-0">
                            @if ($notification->status->value !== 'READ')
                                <form action="{{ route('notifications.read', $notification) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                        class="px-3 py-1.5 text-xs font-semibold text-blue-700 bg-blue-50 hover:bg-blue-100 border border-blue-200 rounded-lg transition">
                                        Tandai Dibaca
                                    </button>
                                </form>
                            @else
                                <span class="text-xs font-medium text-slate-400 flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    Telah Dibaca
                                </span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection