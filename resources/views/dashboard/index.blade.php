@extends('layouts.app')

@section('title', 'Dashboard')

@php
    $user = auth()->user();
    $firstName = strtok(trim($user->name), ' ') ?: $user->name;
    $rupiah = fn ($amount) => 'Rp ' . number_format($amount, 0, ',', '.');

    // Langkah awal hanya dibaca dari relasi yang sudah ada; tampil selama belum ada langganan sama sekali
    $hasSubscriptions = $user->subscriptions()->exists();
    $steps = [
        [
            'title' => 'Tambah metode bayar',
            'description' => 'Kartu atau dompet digital yang kamu pakai',
            'url' => route('payment-methods.index'),
            'done' => $user->paymentMethods()->exists(),
        ],
        [
            'title' => 'Catat langganan pertama',
            'description' => 'Netflix, Spotify, atau yang lain',
            'url' => route('subscriptions.create'),
            'done' => $hasSubscriptions,
        ],
        [
            'title' => 'Atur pengingat',
            'description' => 'Kami kabari lewat email sebelum tagihan',
            'url' => route('reminders.index'),
            'done' => $user->reminders()->exists(),
        ],
    ];
    $doneSteps = collect($steps)->where('done', true)->count();
@endphp

@section('header')
    <!-- Hero ringkasan pengeluaran (FR-005) -->
    <div class="flex flex-wrap items-end justify-between gap-8">
        <div class="min-w-0">
            <p class="text-base leading-6 text-zinc-400">Halo, {{ $firstName }}. Estimasi pengeluaran per bulan</p>
            <p class="mt-1.5 text-5xl sm:text-[72px] sm:leading-[80px] font-bold text-white tracking-[-1.4px] break-words">
                {{ $rupiah($summary['monthly_expense']) }}
            </p>
        </div>
        <div class="flex flex-wrap gap-3">
            <div class="min-w-[168px] px-5 py-4 rounded-[20px] bg-ink-900 border border-ink-800 flex flex-col gap-0.5">
                <span class="text-[13px] font-semibold text-zinc-400">Langganan aktif</span>
                <span class="text-[28px] leading-9 font-bold text-white">{{ $summary['total_active'] }}</span>
            </div>
            <div class="min-w-[168px] px-5 py-4 rounded-[20px] bg-ink-900 border border-ink-800 flex flex-col gap-0.5">
                <span class="text-[13px] font-semibold text-zinc-400">Perkiraan setahun</span>
                <span class="text-[28px] leading-9 font-bold text-white">{{ $rupiah($summary['yearly_expense']) }}</span>
            </div>
        </div>
    </div>
@endsection

@section('content')
    @if (! $hasSubscriptions)
        <!-- Tampilan awal: panduan langkah pertama -->
        <div class="flex flex-wrap gap-8">
            <div class="flex-[1.5_1_420px] min-w-0 flex flex-col">
                <div class="flex items-baseline justify-between gap-4">
                    <h2 class="text-[22px] leading-[30px] font-bold text-ink tracking-[-0.2px]">Mulai dari sini</h2>
                    <span class="text-[13px] font-semibold text-[#6B6B73]">{{ $doneSteps }} dari {{ count($steps) }} selesai</span>
                </div>
                <ol class="mt-4 flex flex-col gap-2.5">
                    @foreach ($steps as $index => $step)
                        <li>
                            <a href="{{ $step['url'] }}"
                                class="min-h-[72px] px-[18px] py-3 rounded-[20px] bg-[#F4F4F5] hover:bg-[#EDEDF0] flex items-center gap-4 transition">
                                <span
                                    class="w-10 h-10 rounded-full text-white text-[15px] font-bold flex items-center justify-center flex-none {{ $step['done'] ? 'bg-primary' : 'bg-ink' }}">
                                    @if ($step['done'])
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                            <path d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        <span class="sr-only">Selesai:</span>
                                    @else
                                        {{ $index + 1 }}
                                    @endif
                                </span>
                                <span class="flex-1 min-w-0 flex flex-col">
                                    <span class="text-base leading-6 font-bold text-ink {{ $step['done'] ? 'line-through decoration-2 decoration-[#A1A1AA]' : '' }}">{{ $step['title'] }}</span>
                                    <span class="text-sm leading-5 text-[#6B6B73]">{{ $step['description'] }}</span>
                                </span>
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#6B6B73"
                                    stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M9 6l6 6-6 6"></path>
                                </svg>
                            </a>
                        </li>
                    @endforeach
                </ol>
                <a href="{{ route('subscriptions.create') }}"
                    class="mt-5 self-start h-14 px-8 rounded-full bg-primary hover:bg-primary-hover text-white text-base font-bold inline-flex items-center gap-2 transition">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6"
                        stroke-linecap="round" aria-hidden="true">
                        <path d="M12 5v14M5 12h14"></path>
                    </svg>
                    Tambah langganan
                </a>
            </div>

            <div
                class="flex-[1_1_280px] min-w-0 rounded-3xl border-[1.5px] border-dashed border-[#D4D4D8] px-6 py-7 flex flex-col items-center justify-center text-center">
                <div class="w-16 h-16 rounded-full bg-[#F4F4F5] flex items-center justify-center">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#0D0D0F" stroke-width="1.8"
                        stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <rect x="3" y="5" width="18" height="15" rx="3"></rect>
                        <path d="M3 10h18M8 3v4M16 3v4"></path>
                    </svg>
                </div>
                <h3 class="mt-4 text-lg leading-[26px] font-bold text-ink">Tagihan terdekat</h3>
                <p class="mt-1 text-sm leading-[21px] text-[#6B6B73]">
                    Belum ada tagihan. Setelah kamu mencatat langganan, yang paling dekat jatuh tempo tampil di sini.
                </p>
            </div>
        </div>
    @else
        <!-- Tagihan mendatang 7 hari ke depan -->
        <div class="flex flex-wrap items-baseline justify-between gap-4">
            <div>
                <h2 class="text-[22px] leading-[30px] font-bold text-ink tracking-[-0.2px]">Tagihan 7 hari ke depan</h2>
                <p class="mt-0.5 text-sm text-[#6B6B73]">Layanan yang akan ditagih atau masa uji cobanya berakhir.</p>
            </div>
            <a href="{{ route('subscriptions.index') }}" class="text-sm font-bold text-primary hover:text-primary-hover">
                Lihat semua
            </a>
        </div>

        @if ($summary['upcoming_payments']->isEmpty())
            <div class="mt-6 py-10 rounded-3xl border-[1.5px] border-dashed border-[#D4D4D8] text-center px-6">
                <p class="text-base font-bold text-ink">Tidak ada tagihan yang jatuh tempo dalam 7 hari ke depan.</p>
                <p class="mt-1 text-sm text-[#6B6B73]">Pengeluaranmu aman untuk minggu ini.</p>
            </div>
        @else
            <div class="mt-6 overflow-x-auto">
                <table class="w-full min-w-[720px] text-left border-separate border-spacing-0">
                    <thead>
                        <tr class="text-[13px] font-semibold text-[#6B6B73]">
                            <th scope="col" class="h-10 px-5 border-b border-[#E4E4E7] w-[30%]">Layanan</th>
                            <th scope="col" class="h-10 px-5 border-b border-[#E4E4E7]">Biaya</th>
                            <th scope="col" class="h-10 px-5 border-b border-[#E4E4E7]">Metode bayar</th>
                            <th scope="col" class="h-10 px-5 border-b border-[#E4E4E7]">Jatuh tempo</th>
                            <th scope="col" class="h-10 px-5 border-b border-[#E4E4E7] text-right w-24">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($summary['upcoming_payments'] as $sub)
                            <tr>
                                <td class="px-5 py-3.5 border-b border-[#EEEEF0]">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <span
                                            class="w-10 h-10 rounded-xl bg-ink text-white text-base font-bold flex items-center justify-center flex-none"
                                            aria-hidden="true">{{ mb_strtoupper(mb_substr($sub->name, 0, 1)) }}</span>
                                        <div class="min-w-0 flex flex-wrap items-center gap-x-2 gap-y-1">
                                            <span class="text-base font-bold text-ink break-words">{{ $sub->name }}</span>
                                            @if ($sub->is_free_trial)
                                                <span
                                                    class="h-7 px-3 rounded-full bg-[#EDEDF0] text-[#52525B] text-[13px] font-semibold inline-flex items-center whitespace-nowrap">Trial</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-3.5 border-b border-[#EEEEF0]">
                                    <span class="block text-[15px] font-bold text-ink whitespace-nowrap">
                                        {{ $rupiah($sub->price) }}
                                        <span class="font-normal text-[13px] text-[#6B6B73]">/ {{ $sub->billing_period->label() }}</span>
                                    </span>
                                    <span class="block text-[13px] text-[#6B6B73]">{{ $sub->category->name ?? 'Tanpa kategori' }}</span>
                                </td>
                                <td class="px-5 py-3.5 border-b border-[#EEEEF0] text-sm text-[#6B6B73]">
                                    {{ $sub->paymentMethod->name ?? 'Belum ditentukan' }}
                                </td>
                                <td class="px-5 py-3.5 border-b border-[#EEEEF0] text-[15px] font-bold text-primary whitespace-nowrap">
                                    {{ $sub->next_payment_date ? $sub->next_payment_date->locale('id')->translatedFormat('d M Y') : '-' }}
                                </td>
                                <td class="px-5 py-3.5 border-b border-[#EEEEF0] text-right">
                                    <a href="{{ route('subscriptions.show', $sub) }}"
                                        aria-label="Detail langganan {{ $sub->name }}"
                                        class="h-9 px-3.5 rounded-xl bg-ink hover:bg-ink-800 text-white text-[13px] font-semibold inline-flex items-center transition">
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    @endif
@endsection
