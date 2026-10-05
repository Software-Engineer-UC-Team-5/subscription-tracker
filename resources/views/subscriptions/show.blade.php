@extends('layouts.app')

@section('title', 'Detail Subscription - ' . $subscription->name)

@section('content')
    <div class="space-y-6">
        <!-- Header Halaman Detail -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-5 border-b border-slate-200">
            <div>
                <a href="{{ route('subscriptions.index') }}"
                    class="text-xs font-semibold text-primary hover:underline mb-2 inline-flex items-center gap-1.5 transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span>Kembali ke Daftar Subscription</span>
                </a>
                <div class="flex flex-wrap items-center gap-3">
                    <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight">{{ $subscription->name }}</h1>
                    @php
                        $badgeClass = match ($subscription->status->value) {
                            'ACTIVE' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                            'TRIAL' => 'bg-amber-50 text-amber-700 border-amber-200',
                            'PAUSED' => 'bg-purple-50 text-purple-700 border-purple-200',
                            'CANCELLED' => 'bg-slate-100 text-slate-600 border-slate-300',
                            'EXPIRED' => 'bg-rose-50 text-rose-700 border-rose-200',
                            default => 'bg-slate-100 text-slate-700 border-slate-200',
                        };
                    @endphp
                    <span
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border {{ $badgeClass }}">
                        {{ $subscription->status->label() }}
                    </span>
                    @if ($subscription->is_free_trial)
                        <span
                            class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800 border border-amber-300">
                            Masa Uji Coba (Free Trial)
                        </span>
                    @endif
                </div>
            </div>

            <!-- Tombol Aksi -->
            <div class="flex items-center gap-2">
                <a href="{{ route('subscriptions.edit', $subscription) }}"
                    class="px-4 py-2 text-xs font-semibold text-slate-700 bg-white border border-slate-300 hover:bg-slate-50 rounded-xl transition shadow-sm inline-flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                    </svg>
                    <span>Ubah Data</span>
                </a>
                <form action="{{ route('subscriptions.destroy', $subscription) }}" method="POST" class="inline"
                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus langganan {{ $subscription->name }}?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="px-4 py-2 text-xs font-semibold text-rose-600 bg-white border border-rose-200 hover:bg-rose-50 rounded-xl transition shadow-sm inline-flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        <span>Hapus</span>
                    </button>
                </form>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Informasi Utama -->
            <div class="md:col-span-2 bg-white rounded-xl border border-slate-200 p-6 shadow-sm space-y-5">
                <h2 class="text-sm font-bold uppercase tracking-wider text-slate-700 border-b border-slate-100 pb-2">Rincian
                    Paket</h2>

                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-xs text-slate-400 block">Kategori</span>
                        <span
                            class="font-medium text-slate-900">{{ $subscription->category->name ?? 'Belum dikategorikan' }}</span>
                    </div>
                    <div>
                        <span class="text-xs text-slate-400 block">Metode Pembayaran</span>
                        <span
                            class="font-medium text-slate-900">{{ $subscription->paymentMethod->name ?? 'Belum ditentukan' }}</span>
                    </div>
                    <div>
                        <span class="text-xs text-slate-400 block">Biaya per Siklus</span>
                        <span class="font-semibold text-slate-900 text-base">Rp
                            {{ number_format($subscription->price, 0, ',', '.') }}</span>
                        <span class="text-xs text-slate-500">/ {{ $subscription->billing_period->label() }}</span>
                    </div>
                    <div>
                        <span class="text-xs text-slate-400 block">Jatuh Tempo Berikutnya</span>
                        <span class="font-semibold text-rose-600 text-base">
                            {{ $subscription->next_payment_date ? $subscription->next_payment_date->format('d M Y') : '-' }}
                        </span>
                    </div>
                </div>

                <!-- Bagian Free Trial jika ada -->
                @if ($subscription->freeTrial)
                    <div class="mt-6 pt-5 border-t border-slate-100 bg-amber-50/60 p-4 rounded-xl border border-amber-200/80">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="text-xs font-bold uppercase tracking-wider text-amber-800">Masa Uji Coba Gratis (Free
                                Trial)</h3>
                            @if ($subscription->freeTrial->isExpired())
                                <span class="px-2 py-0.5 text-[10px] font-bold rounded bg-rose-100 text-rose-800">Sudah
                                    Berakhir</span>
                            @else
                                <span class="px-2 py-0.5 text-[10px] font-bold rounded bg-emerald-100 text-emerald-800">Sedang
                                    Berjalan</span>
                            @endif
                        </div>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 text-xs mt-3">
                            <div>
                                <span class="text-slate-500 block">Mulai:</span>
                                <span
                                    class="font-semibold text-slate-800">{{ $subscription->freeTrial->start_date ? $subscription->freeTrial->start_date->format('d M Y') : '-' }}</span>
                            </div>
                            <div>
                                <span class="text-slate-500 block">Berakhir:</span>
                                <span
                                    class="font-semibold text-slate-800">{{ $subscription->freeTrial->end_date ? $subscription->freeTrial->end_date->format('d M Y') : '-' }}</span>
                            </div>
                            <div>
                                <span class="text-slate-500 block">Batas Batalkan:</span>
                                <span class="font-semibold text-slate-800">H-{{ $subscription->freeTrial->cancel_before_days }}
                                    hari</span>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Analisis Biaya Khusus Layanan Ini -->
            <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm flex flex-col justify-between">
                <div>
                    <h2 class="text-sm font-bold uppercase tracking-wider text-slate-700 border-b border-slate-100 pb-2">
                        Normalisasi Biaya</h2>
                    <div class="space-y-4 mt-4">
                        <div>
                            <span class="text-xs text-slate-400 block">Estimasi Beban Bulanan</span>
                            <span class="text-lg font-bold text-slate-900">
                                Rp {{ number_format($subscription->getMonthlyCost(), 0, ',', '.') }}
                            </span>
                        </div>
                        <div>
                            <span class="text-xs text-slate-400 block">Estimasi Beban Tahunan</span>
                            <span class="text-lg font-bold text-slate-900">
                                Rp {{ number_format($subscription->getAnnualCost(), 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="pt-6 border-t border-slate-100 mt-6">
                    <a href="{{ route('reminders.index') }}"
                        class="w-full inline-flex items-center justify-center px-3 py-2 text-xs font-semibold text-blue-700 bg-blue-50 hover:bg-blue-100 rounded-lg transition border border-blue-200">
                        Atur Pengingat untuk Layanan Ini &rarr;
                    </a>
                </div>
            </div>
        </div>

        <!-- Daftar Pengingat Terkait -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
                <div>
                    <h2 class="text-base font-semibold text-slate-900">Pengingat Terjadwal untuk Layanan Ini</h2>
                    <p class="text-xs text-slate-500">Notifikasi otomatis yang akan dikirim sebelum tanggal jatuh tempo</p>
                </div>
            </div>

            @if ($subscription->reminders->isEmpty())
                <p class="text-xs text-slate-400 italic py-2">Belum ada pengingat khusus yang dibuat untuk langganan ini.</p>
            @else
                <ul class="divide-y divide-slate-100 text-sm">
                    @foreach ($subscription->reminders as $reminder)
                        <li class="py-3 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <span
                                    class="w-2.5 h-2.5 rounded-full {{ $reminder->is_active ? 'bg-emerald-500' : 'bg-slate-300' }}"></span>
                                <div>
                                    <span class="font-medium text-slate-800">{{ $reminder->type->label() }}</span>
                                    <span class="text-xs text-slate-400 block">Ingatkan H-{{ $reminder->notify_before_days }} hari
                                        sebelum tanggal jatuh tempo</span>
                                </div>
                            </div>
                            <span
                                class="text-xs font-medium px-2 py-0.5 rounded {{ $reminder->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                                {{ $reminder->is_active ? 'Aktif' : 'Non-aktif' }}
                            </span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
@endsection