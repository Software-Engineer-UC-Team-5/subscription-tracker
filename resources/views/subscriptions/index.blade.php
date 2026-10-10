@extends('layouts.app')

@section('title', 'Langganan')

@section('page_title', 'Langganan')
@section('page_subtitle', 'Semua layanan yang kamu bayar rutin, termasuk masa uji coba gratis.')

@section('page_actions')
    <a href="{{ route('subscriptions.create') }}"
        class="h-14 px-7 rounded-full bg-primary hover:bg-primary-hover text-white text-base font-bold inline-flex items-center gap-2 transition">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6"
            stroke-linecap="round" aria-hidden="true">
            <path d="M12 5v14M5 12h14"></path>
        </svg>
        Tambah langganan
    </a>
@endsection

@section('content')
    @php
        $isFiltered = collect($filters)->filter(fn ($value) => filled($value))->isNotEmpty();
        $periodLabels = [
            'DAILY' => 'per hari',
            'WEEKLY' => 'per minggu',
            'MONTHLY' => 'per bulan',
            'QUARTERLY' => 'per 3 bulan',
            'YEARLY' => 'per tahun',
        ];
        $filterClass = 'h-11 rounded-xl border-[1.5px] border-[#E4E4E7] bg-white px-3.5 text-sm font-semibold text-ink outline-none cursor-pointer focus:border-primary';
    @endphp

    <!-- Bilah pencarian dan filter (dikirim lewat GET) -->
    <form method="GET" action="{{ route('subscriptions.index') }}" class="flex flex-wrap items-center gap-2.5">
        <div class="relative flex-[1_1_240px]">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#6B6B73" stroke-width="2"
                stroke-linecap="round" aria-hidden="true" class="absolute left-3.5 top-[13px]">
                <circle cx="11" cy="11" r="7"></circle>
                <path d="M20 20l-4-4"></path>
            </svg>
            <label for="search" class="sr-only">Cari langganan</label>
            <input type="search" id="search" name="search" value="{{ $filters['search'] ?? '' }}"
                placeholder="Cari Netflix, Spotify…"
                class="w-full h-11 rounded-xl border-[1.5px] border-[#E4E4E7] bg-white pl-[42px] pr-3.5 text-sm text-ink placeholder:text-[#A1A1AA] outline-none focus:border-primary">
        </div>

        <label for="category_id" class="sr-only">Kategori</label>
        <select id="category_id" name="category_id" class="{{ $filterClass }}">
            <option value="">Semua kategori</option>
            @foreach ($categories as $cat)
                <option value="{{ $cat->id }}" @selected(($filters['category_id'] ?? '') == $cat->id)>{{ $cat->name }}</option>
            @endforeach
        </select>

        <label for="payment_method_id" class="sr-only">Metode bayar</label>
        <select id="payment_method_id" name="payment_method_id" class="{{ $filterClass }}">
            <option value="">Semua metode</option>
            @foreach ($paymentMethods as $pm)
                <option value="{{ $pm->id }}" @selected(($filters['payment_method_id'] ?? '') == $pm->id)>{{ $pm->name }}</option>
            @endforeach
        </select>

        <label for="status" class="sr-only">Status</label>
        <select id="status" name="status" class="{{ $filterClass }}">
            <option value="">Semua status</option>
            @foreach (\App\Enums\SubscriptionStatus::cases() as $st)
                <option value="{{ $st->value }}" @selected(($filters['status'] ?? '') == $st->value)>{{ $st->label() }}</option>
            @endforeach
        </select>

        <button type="submit"
            class="h-11 px-5 rounded-xl bg-ink hover:bg-ink-800 text-white text-sm font-bold transition">Filter</button>
        @if ($isFiltered)
            <a href="{{ route('subscriptions.index') }}"
                class="h-11 px-1.5 inline-flex items-center text-sm font-semibold text-[#6B6B73] hover:text-ink">Reset</a>
        @endif
    </form>

    @if ($subscriptions->isEmpty())
        <!-- Kosong: bedakan antara belum ada data dan hasil filter kosong -->
        <div class="flex-1 flex flex-col items-center justify-center text-center pt-14 pb-12 px-6">
            <div class="w-[84px] h-[84px] rounded-full bg-[#F4F4F5] flex items-center justify-center">
                <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#0D0D0F" stroke-width="1.8"
                    stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <rect x="3" y="5" width="18" height="15" rx="3"></rect>
                    <path d="M3 10h18M8 3v4M16 3v4"></path>
                    <path d="M9 15h6"></path>
                </svg>
            </div>
            @if ($isFiltered)
                <h2 class="mt-5 text-[22px] leading-[30px] font-bold text-ink tracking-[-0.2px]">Tidak ada yang cocok</h2>
                <p class="mt-1.5 max-w-[420px] text-base leading-6 text-[#6B6B73]">
                    Belum ada langganan yang sesuai dengan pencarian atau filter ini.
                </p>
                <a href="{{ route('subscriptions.index') }}"
                    class="mt-6 h-14 px-8 rounded-full border-[1.5px] border-ink bg-white hover:bg-[#F4F4F5] text-ink text-base font-bold inline-flex items-center transition">
                    Reset filter
                </a>
            @else
                <h2 class="mt-5 text-[22px] leading-[30px] font-bold text-ink tracking-[-0.2px]">Belum ada langganan</h2>
                <p class="mt-1.5 max-w-[420px] text-base leading-6 text-[#6B6B73]">
                    Catat Netflix, Spotify, atau layanan lain yang kamu bayar rutin. Kami ingatkan sebelum tagihannya jatuh.
                </p>
                <a href="{{ route('subscriptions.create') }}"
                    class="mt-6 h-14 px-8 rounded-full bg-primary hover:bg-primary-hover text-white text-base font-bold inline-flex items-center transition">
                    Tambah langganan pertama
                </a>
            @endif
        </div>
    @else
        <!-- Tabel daftar langganan -->
        <div class="mt-5 overflow-x-auto">
            <table class="w-full min-w-[820px] text-left border-separate border-spacing-0">
                <thead>
                    <tr class="text-[13px] font-semibold text-[#6B6B73]">
                        <th scope="col" class="h-10 px-5 border-b border-[#E4E4E7] w-[34%]">Layanan</th>
                        <th scope="col" class="h-10 px-5 border-b border-[#E4E4E7]">Biaya</th>
                        <th scope="col" class="h-10 px-5 border-b border-[#E4E4E7]">Metode bayar</th>
                        <th scope="col" class="h-10 px-5 border-b border-[#E4E4E7]">Jatuh tempo</th>
                        <th scope="col" class="h-10 px-5 border-b border-[#E4E4E7]">Status</th>
                        <th scope="col" class="h-10 px-5 border-b border-[#E4E4E7] text-right w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($subscriptions as $sub)
                        @php
                            $daysLeft = $sub->next_payment_date
                                ? (int) today()->diffInDays($sub->next_payment_date, false)
                                : null;
                        @endphp
                        <tr>
                            <td class="px-5 py-4 border-b border-[#EEEEF0]">
                                <div class="flex items-center gap-3.5 min-w-0">
                                    <span
                                        class="w-10 h-10 rounded-xl bg-ink text-white text-base font-bold flex items-center justify-center flex-none"
                                        aria-hidden="true">{{ mb_strtoupper(mb_substr($sub->name, 0, 1)) }}</span>
                                    <div class="min-w-0 flex flex-wrap items-center gap-x-2.5 gap-y-1">
                                        <a href="{{ route('subscriptions.show', $sub) }}"
                                            class="text-[17px] leading-6 font-bold text-ink hover:underline break-words">{{ $sub->name }}</a>
                                        <span
                                            class="h-6 px-2.5 rounded-full bg-[#EDEDF0] text-[#52525B] text-xs font-semibold inline-flex items-center whitespace-nowrap">
                                            {{ $sub->category->name ?? 'Tanpa kategori' }}
                                        </span>
                                        @if ($sub->is_free_trial)
                                            <span
                                                class="h-6 px-2.5 rounded-full border border-[#D4D4D8] text-[#52525B] text-xs font-semibold inline-flex items-center whitespace-nowrap">Trial</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-4 border-b border-[#EEEEF0]">
                                <span class="block text-[15px] leading-[22px] font-bold text-ink whitespace-nowrap">
                                    Rp {{ number_format($sub->price, 0, ',', '.') }}
                                </span>
                                <span class="block text-[13px] leading-[18px] text-[#6B6B73]">
                                    {{ $periodLabels[$sub->billing_period->value] ?? $sub->billing_period->label() }}
                                </span>
                            </td>
                            <td class="px-5 py-4 border-b border-[#EEEEF0] text-sm leading-5 text-[#6B6B73]">
                                {{ $sub->paymentMethod->name ?? '-' }}
                            </td>
                            <td class="px-5 py-4 border-b border-[#EEEEF0]">
                                @if ($sub->next_payment_date)
                                    <span class="block text-[15px] leading-[22px] font-semibold text-ink whitespace-nowrap">
                                        {{ $sub->next_payment_date->locale('id')->translatedFormat('d M Y') }}
                                    </span>
                                    <span class="block text-[13px] leading-[18px] whitespace-nowrap {{ $daysLeft < 0 ? 'text-[#B42318]' : 'text-[#6B6B73]' }}">
                                        @if ($daysLeft === 0)
                                            Hari ini
                                        @elseif ($daysLeft > 0)
                                            {{ $daysLeft }} hari lagi
                                        @else
                                            Lewat {{ abs($daysLeft) }} hari
                                        @endif
                                    </span>
                                @else
                                    <span class="text-[15px] text-[#6B6B73]">-</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 border-b border-[#EEEEF0]">
                                <!-- Status aktif merah dengan teks putih; status lain abu-abu -->
                                <span
                                    class="h-7 px-3 rounded-full text-[13px] font-bold inline-flex items-center whitespace-nowrap {{ $sub->status === \App\Enums\SubscriptionStatus::ACTIVE ? 'bg-primary text-white' : 'bg-[#EDEDF0] text-[#52525B]' }}">
                                    {{ $sub->status->label() }}
                                </span>
                            </td>
                            <td class="px-5 py-4 border-b border-[#EEEEF0] text-right">
                                <a href="{{ route('subscriptions.show', $sub) }}"
                                    aria-label="Detail langganan {{ $sub->name }}"
                                    class="h-9 px-3.5 rounded-xl bg-ink hover:bg-ink-800 text-white text-[13px] font-semibold inline-flex items-center gap-1 transition">
                                    Detail
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <path d="M9 6l6 6-6 6"></path>
                                    </svg>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <p class="mt-5 text-[13px] leading-5 text-[#6B6B73]">Menampilkan {{ $subscriptions->count() }} langganan</p>
    @endif
@endsection
