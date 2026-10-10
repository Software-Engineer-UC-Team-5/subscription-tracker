@extends('layouts.app')

@section('title', 'Notifikasi')

@section('page_title', 'Notifikasi')
@section('page_subtitle', 'Pengingat tagihan yang sudah dikirim ke kamu.')

@section('content')
    @php
        // Label jenis versi desain; jenis lain memakai label() bawaan enum
        $typeLabels = [
            'PAYMENT_REMINDER' => 'Jatuh tempo pembayaran',
            'FREE_TRIAL_REMINDER' => 'Masa uji coba',
        ];
    @endphp

    @if ($notifications->isEmpty())
        <!-- Tampilan kosong -->
        <div class="flex-1 flex flex-col items-center justify-center text-center py-10">
            <div class="w-[84px] h-[84px] rounded-full bg-[#F4F4F5] flex items-center justify-center">
                <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#0D0D0F" stroke-width="1.8"
                    stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M4 13l2.6-7.2A2 2 0 0 1 8.5 4.5h7a2 2 0 0 1 1.9 1.3L20 13"></path>
                    <path d="M4 13v5a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-5h-5l-1 2h-4l-1-2z"></path>
                </svg>
            </div>
            <h2 class="mt-5 text-2xl leading-8 font-bold text-ink tracking-[-0.2px]">Belum ada notifikasi</h2>
            <p class="mt-1.5 max-w-[400px] text-base leading-6 text-[#6B6B73]">
                Saat pengingat pertamamu terkirim, riwayatnya muncul di sini.
            </p>
        </div>
    @else
        <div class="flex items-center justify-between gap-4">
            <h2 class="text-[22px] leading-[30px] font-bold text-ink">Semua notifikasi</h2>
            <span class="text-[13px] font-semibold text-[#6B6B73]">Total: {{ $notifications->count() }}</span>
        </div>

        <ul class="mt-6 flex flex-col gap-3">
            @foreach ($notifications as $notification)
                @php
                    $isRead = $notification->status === \App\Enums\NotificationStatus::READ;
                @endphp
                <!-- Belum dibaca: latar abu-abu dengan titik merah; sudah dibaca: latar putih -->
                <li
                    class="px-[22px] py-5 rounded-[20px] border-[1.5px] border-[#EEEEF0] flex flex-wrap items-center justify-between gap-4 {{ $isRead ? 'bg-white' : 'bg-[#F4F4F5]' }}">
                    <div class="flex-[1_1_360px] min-w-0 flex flex-col gap-1.5">
                        <div class="flex flex-wrap items-center gap-2.5">
                            @unless ($isRead)
                                <span class="w-2 h-2 rounded-full bg-primary flex-none" aria-hidden="true"></span>
                                <span class="sr-only">Belum dibaca.</span>
                            @endunless
                            <span
                                class="h-7 px-3 rounded-full bg-[#EDEDF0] text-[#52525B] text-[13px] font-semibold inline-flex items-center whitespace-nowrap">
                                {{ $typeLabels[$notification->type->value] ?? $notification->type->label() }}
                            </span>
                            @if ($notification->status === \App\Enums\NotificationStatus::FAILED)
                                <span class="text-[13px] font-semibold text-[#B42318]">{{ $notification->status->label() }}</span>
                            @endif
                            <span class="text-[13px] text-[#6B6B73]">
                                {{ $notification->created_at ? $notification->created_at->locale('id')->diffForHumans() : '-' }}
                            </span>
                        </div>
                        <h3 class="text-[17px] leading-6 font-bold text-ink break-words">{{ $notification->title }}</h3>
                        <p class="text-[15px] leading-[22px] text-[#52525B] break-words">{{ $notification->message }}</p>
                    </div>

                    @if ($isRead)
                        <span class="inline-flex items-center gap-1.5 text-[13px] font-semibold text-[#6B6B73]">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M5 12.5l4.5 4.5L19 7.5"></path>
                            </svg>
                            Telah dibaca
                        </span>
                    @else
                        <form action="{{ route('notifications.read', $notification) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit"
                                class="h-9 px-4 rounded-xl bg-ink hover:bg-ink-800 text-white text-[13px] font-semibold transition">
                                Tandai dibaca
                            </button>
                        </form>
                    @endif
                </li>
            @endforeach
        </ul>
    @endif
@endsection
