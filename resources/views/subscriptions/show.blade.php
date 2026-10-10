@extends('layouts.app')

@section('title', 'Detail Langganan - ' . $subscription->name)

@php
    $rupiah = fn ($amount) => 'Rp ' . number_format($amount, 0, ',', '.');
    $periodLabels = [
        'DAILY' => 'hari',
        'WEEKLY' => 'minggu',
        'MONTHLY' => 'bulan',
        'QUARTERLY' => '3 bulan',
        'YEARLY' => 'tahun',
    ];
    $reminderTypeLabels = [
        'PAYMENT_DUE' => 'Jatuh tempo pembayaran',
        'FREE_TRIAL_END' => 'Masa uji coba berakhir',
    ];
    $isActive = $subscription->status === \App\Enums\SubscriptionStatus::ACTIVE;
    $daysLeft = $subscription->next_payment_date
        ? (int) today()->diffInDays($subscription->next_payment_date, false)
        : null;
    $formatDate = fn ($date) => $date ? $date->locale('id')->translatedFormat('d M Y') : '-';
@endphp

@section('header')
    <div class="flex flex-wrap items-end justify-between gap-6">
        <div class="min-w-0">
            <a href="{{ route('subscriptions.index') }}"
                class="min-h-11 inline-flex items-center gap-2 text-sm font-semibold text-zinc-400 hover:text-white transition">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"
                    stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M15 6l-6 6 6 6"></path>
                </svg>
                Kembali ke daftar langganan
            </a>
            <div class="mt-1 flex flex-wrap items-center gap-x-4 gap-y-2">
                <h1 class="text-4xl sm:text-5xl sm:leading-[56px] font-bold text-white tracking-[-0.7px] break-words min-w-0">
                    {{ $subscription->name }}
                </h1>
                <!-- Status aktif merah dengan teks putih; status lain abu-abu -->
                <span
                    class="h-[30px] px-3.5 rounded-full text-sm font-bold inline-flex items-center {{ $isActive ? 'bg-primary text-white' : 'bg-ink-800 text-zinc-200' }}">
                    {{ $subscription->status->label() }}
                </span>
                @if ($subscription->is_free_trial)
                    <span class="h-[30px] px-3.5 rounded-full border border-[#3A3A41] text-zinc-200 text-sm font-bold inline-flex items-center">
                        Trial
                    </span>
                @endif
            </div>
        </div>
        <div class="flex flex-wrap gap-2.5">
            <a href="{{ route('subscriptions.edit', $subscription) }}"
                class="h-12 px-[22px] rounded-full border-[1.5px] border-[#3A3A41] hover:bg-ink-900 text-white text-[15px] font-bold inline-flex items-center transition">
                Ubah data
            </a>
            <!-- Hapus memakai popup konfirmasi; nama langganan tidak disisipkan ke JavaScript -->
            <button type="button" id="subscription-delete-open" aria-haspopup="dialog"
                aria-controls="subscription-delete-dialog"
                class="h-12 px-[22px] rounded-full border-[1.5px] border-[#5B2226] hover:bg-[#2A1215] text-[#FF8A8F] text-[15px] font-bold inline-flex items-center transition">
                Hapus
            </button>
        </div>
    </div>
@endsection

@section('content')
    <div class="flex flex-wrap gap-8">
        <!-- Rincian paket -->
        <div class="flex-[1.6_1_420px] min-w-0 flex flex-col">
            <h2 class="text-xl leading-7 font-bold text-ink">Rincian paket</h2>
            <dl class="mt-5 grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-6">
                <div class="flex flex-col gap-1">
                    <dt class="text-[13px] leading-[18px] text-[#6B6B73]">Kategori</dt>
                    <dd>
                        <span class="h-7 px-3 rounded-full bg-[#EDEDF0] text-[#52525B] text-[13px] font-semibold inline-flex items-center">
                            {{ $subscription->category->name ?? 'Belum dikategorikan' }}
                        </span>
                    </dd>
                </div>
                <div class="flex flex-col gap-1">
                    <dt class="text-[13px] leading-[18px] text-[#6B6B73]">Metode pembayaran</dt>
                    <dd class="text-xl leading-7 font-bold text-[#6B6B73] break-words">
                        {{ $subscription->paymentMethod->name ?? 'Belum ditentukan' }}
                    </dd>
                </div>
                <div class="flex flex-col gap-1">
                    <dt class="text-[13px] leading-[18px] text-[#6B6B73]">Biaya per siklus</dt>
                    <dd class="text-xl leading-7 font-bold text-ink">
                        {{ $rupiah($subscription->price) }}
                        <span class="text-sm font-medium text-[#6B6B73]">/ {{ $periodLabels[$subscription->billing_period->value] ?? $subscription->billing_period->label() }}</span>
                    </dd>
                </div>
                <div class="flex flex-col gap-1">
                    <dt class="text-[13px] leading-[18px] text-[#6B6B73]">Jatuh tempo berikutnya</dt>
                    <dd class="flex flex-col">
                        <span class="text-xl leading-7 font-bold text-primary">{{ $formatDate($subscription->next_payment_date) }}</span>
                        @if ($daysLeft !== null)
                            <span class="text-[13px] {{ $daysLeft < 0 ? 'text-[#B42318]' : 'text-[#6B6B73]' }}">
                                @if ($daysLeft === 0)
                                    Hari ini
                                @elseif ($daysLeft > 0)
                                    {{ $daysLeft }} hari lagi
                                @else
                                    Lewat {{ abs($daysLeft) }} hari
                                @endif
                            </span>
                        @endif
                    </dd>
                </div>
            </dl>

            @if ($subscription->freeTrial)
                <!-- Informasi masa uji coba gratis -->
                <div class="mt-8 p-5 rounded-[20px] bg-[#F4F4F5] flex flex-col gap-4">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <h3 class="text-base font-bold text-ink">Masa uji coba gratis</h3>
                        <span
                            class="h-7 px-3 rounded-full text-[13px] font-bold inline-flex items-center {{ $subscription->freeTrial->isExpired() ? 'bg-[#FEF3F2] text-[#B42318]' : 'bg-ink text-white' }}">
                            {{ $subscription->freeTrial->isExpired() ? 'Sudah berakhir' : 'Sedang berjalan' }}
                        </span>
                    </div>
                    <dl class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                        <div>
                            <dt class="text-[13px] text-[#6B6B73]">Mulai</dt>
                            <dd class="text-[15px] font-bold text-ink">{{ $formatDate($subscription->freeTrial->start_date) }}</dd>
                        </div>
                        <div>
                            <dt class="text-[13px] text-[#6B6B73]">Berakhir</dt>
                            <dd class="text-[15px] font-bold text-ink">{{ $formatDate($subscription->freeTrial->end_date) }}</dd>
                        </div>
                        <div>
                            <dt class="text-[13px] text-[#6B6B73]">Batas batalkan</dt>
                            <dd class="text-[15px] font-bold text-ink">H-{{ $subscription->freeTrial->cancel_before_days }} hari</dd>
                        </div>
                    </dl>
                </div>
            @endif
        </div>

        <!-- Normalisasi biaya -->
        <aside class="flex-[1_1_280px] min-w-0 self-start rounded-[28px] bg-ink p-7 flex flex-col">
            <span class="text-[13px] font-semibold text-zinc-400">Normalisasi biaya</span>
            <span class="mt-4 text-[13px] text-zinc-400">Estimasi per bulan</span>
            <span class="text-[32px] leading-10 font-bold text-white break-words">{{ $rupiah($subscription->getMonthlyCost()) }}</span>
            <span class="mt-3 text-[13px] text-zinc-400">Estimasi per tahun</span>
            <span class="text-[32px] leading-10 font-bold text-white break-words">{{ $rupiah($subscription->getAnnualCost()) }}</span>
            <a href="{{ route('reminders.index') }}"
                class="mt-6 h-12 rounded-full bg-primary hover:bg-primary-hover text-white text-sm font-bold flex items-center justify-center transition">
                Atur pengingat layanan ini
            </a>
        </aside>
    </div>

    <!-- Pengingat terjadwal untuk langganan ini -->
    <div class="mt-8 pt-7 border-t border-[#EEEEF0] flex flex-col">
        <h2 class="text-xl leading-7 font-bold text-ink">Pengingat terjadwal</h2>
        <p class="mt-0.5 text-sm leading-5 text-[#6B6B73]">Notifikasi otomatis sebelum tanggal jatuh tempo.</p>

        @if ($subscription->reminders->isEmpty())
            <div class="mt-4 px-5 py-4 rounded-[20px] border-[1.5px] border-dashed border-[#D4D4D8] text-sm text-[#6B6B73]">
                Belum ada pengingat untuk langganan ini.
            </div>
        @else
            <ul class="mt-4 flex flex-col gap-3">
                @foreach ($subscription->reminders as $reminder)
                    <li class="px-5 py-4 rounded-[20px] bg-[#F4F4F5] flex items-center gap-3.5">
                        <span class="w-2.5 h-2.5 rounded-full flex-none {{ $reminder->is_active ? 'bg-primary' : 'bg-[#A1A1AA]' }}"
                            aria-hidden="true"></span>
                        <div class="flex-1 min-w-0 flex flex-col">
                            <span class="text-base leading-6 font-bold text-ink">
                                {{ $reminderTypeLabels[$reminder->type->value] ?? $reminder->type->label() }}
                            </span>
                            <span class="text-sm leading-5 text-[#6B6B73]">
                                @if ($reminder->notify_before_days == 0)
                                    Diingatkan pada hari jatuh tempo
                                @else
                                    Diingatkan H-{{ $reminder->notify_before_days }} sebelum tanggal jatuh tempo
                                @endif
                            </span>
                        </div>
                        <span
                            class="h-7 px-3 rounded-full text-[13px] font-bold inline-flex items-center {{ $reminder->is_active ? 'bg-primary text-white' : 'bg-[#EDEDF0] text-[#52525B]' }}">
                            {{ $reminder->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>

    <!-- Konfirmasi hapus langganan -->
    <dialog id="subscription-delete-dialog" aria-labelledby="subscription-delete-title"
        aria-describedby="subscription-delete-description"
        class="m-auto w-[calc(100vw-2rem)] max-w-[480px] max-h-[90dvh] overflow-y-auto p-8 rounded-[28px] bg-white shadow-[0_24px_64px_rgba(0,0,0,0.35)] backdrop:bg-[rgba(13,13,15,0.62)]">
        <h2 id="subscription-delete-title" class="text-2xl leading-8 font-bold text-ink tracking-[-0.2px]">Hapus langganan?</h2>
        <p id="subscription-delete-description" class="mt-2 text-base leading-6 text-[#6B6B73]">
            Langganan <span class="font-bold text-ink break-words">{{ $subscription->name }}</span>
            beserta pengingatnya akan dihapus.
        </p>
        <form action="{{ route('subscriptions.destroy', $subscription) }}" method="POST"
            class="mt-7 flex flex-wrap items-center justify-end gap-3">
            @csrf
            @method('DELETE')
            <button type="button" id="subscription-delete-cancel" autofocus
                class="h-[52px] px-7 rounded-full border-[1.5px] border-ink bg-white hover:bg-[#F4F4F5] text-ink text-[15px] font-bold transition disabled:opacity-50">
                Batal
            </button>
            <button type="submit" id="subscription-delete-confirm"
                class="h-[52px] px-8 rounded-full bg-[#B42318] hover:bg-[#912018] text-white text-[15px] font-bold transition disabled:opacity-50 disabled:cursor-wait">
                Hapus
            </button>
        </form>
    </dialog>

    <script>
        {
            const dialog = document.getElementById('subscription-delete-dialog');
            const open = document.getElementById('subscription-delete-open');
            const cancel = document.getElementById('subscription-delete-cancel');
            const confirmButton = document.getElementById('subscription-delete-confirm');

            open.addEventListener('click', () => dialog.showModal());
            cancel.addEventListener('click', () => dialog.close());

            // Cegah klik ganda dan penutupan popup selama permintaan hapus berlangsung.
            dialog.querySelector('form').addEventListener('submit', event => {
                if (confirmButton.disabled) {
                    event.preventDefault();
                    return;
                }
                // Tunda penonaktifan agar tombol submit tetap terkirim bersama formulir.
                setTimeout(() => {
                    confirmButton.disabled = cancel.disabled = true;
                    confirmButton.textContent = 'Menghapus…';
                });
            });
            dialog.addEventListener('cancel', event => {
                if (confirmButton.disabled) event.preventDefault();
            });
        }
    </script>
@endsection
