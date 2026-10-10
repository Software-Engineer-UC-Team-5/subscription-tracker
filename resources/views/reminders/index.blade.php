@extends('layouts.app')

@section('title', 'Pengingat')

@section('page_title', 'Pengingat')
@section('page_subtitle', 'Diingatkan sebelum tagihan dipotong atau masa uji coba habis.')

@section('content')
    @php
        $hasSubscriptions = $subscriptions->isNotEmpty();

        // Label jenis versi desain; jenis lain memakai label() bawaan enum
        $typeLabels = [
            'PAYMENT_DUE' => 'Jatuh tempo pembayaran',
            'FREE_TRIAL_END' => 'Masa uji coba berakhir',
        ];
        $dayOptions = [
            0 => 'Hari H (saat tanggal tagihan)',
            1 => 'H-1 Hari',
            2 => 'H-2 Hari',
            3 => 'H-3 Hari (Rekomendasi)',
            5 => 'H-5 Hari',
            7 => 'H-7 Hari (1 minggu sebelumnya)',
        ];
        $selectedDays = (string) old('notify_before_days', '3');

        // Form nonaktif memakai teks abu-abu sesuai board "kosong"
        $labelClass = 'text-sm font-semibold ' . ($hasSubscriptions ? 'text-ink' : 'text-[#6B6B73]');
        $selectClass = 'w-full h-[52px] rounded-2xl px-3.5 text-base outline-none transition focus:bg-white focus:border-2 focus:border-primary disabled:cursor-not-allowed disabled:text-[#6B6B73]';
        $selectState = fn (string $field) => $errors->has($field)
            ? 'bg-white border-2 border-[#B42318] text-ink'
            : 'bg-[#F4F4F5] border-[1.5px] border-[#E4E4E7] text-ink';
    @endphp

    <div class="flex flex-wrap gap-8 items-start">
        <!-- Formulir buat pengingat baru -->
        <form action="{{ route('reminders.store') }}" method="POST" class="flex-[3_1_420px] min-w-0 flex flex-col">
            @csrf
            <h2 class="text-[22px] leading-[30px] font-bold text-ink tracking-[-0.2px]">Buat pengingat</h2>

            @php
                // Error di luar tiga field utama (misalnya is_active) tetap ditampilkan di atas formulir
                $otherErrors = collect($errors->getMessages())
                    ->except(['subscription_id', 'type', 'notify_before_days'])
                    ->flatten();
            @endphp
            @if ($otherErrors->isNotEmpty())
                <div role="alert"
                    class="mt-4 px-5 py-4 rounded-2xl bg-[#FEF3F2] border border-[#FECDCA] text-[#B42318] text-sm font-semibold">
                    @foreach ($otherErrors as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            @unless ($hasSubscriptions)
                <!-- Peringatan saat belum ada langganan aktif; formulir di bawahnya dinonaktifkan -->
                <div class="mt-4 p-4 rounded-[20px] bg-[#FFFFFF] flex gap-3">
                    <span class="w-9 h-9 rounded-full bg-[#E4E4E7] flex items-center justify-center flex-none">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#0D0D0F" stroke-width="2.4"
                            stroke-linecap="round" aria-hidden="true">
                            <path d="M12 7v6M12 17v.5"></path>
                        </svg>
                    </span>
                    <div class="flex-1 flex flex-col gap-0.5">
                        <span class="text-[15px] leading-[22px] font-bold text-ink">Belum ada langganan aktif</span>
                        <span class="text-sm leading-5 text-[#4A4A52]">Tambahkan langganan dulu, baru pengingat bisa diatur.</span>
                        <a href="{{ route('subscriptions.create') }}"
                            class="mt-1 min-h-11 inline-flex items-center text-[15px] font-bold text-primary hover:text-primary-hover">
                            Tambah langganan →
                        </a>
                    </div>
                </div>
            @endunless

            <fieldset @disabled(! $hasSubscriptions) class="flex flex-col">
                <div class="mt-5 flex flex-col gap-2">
                    <label for="subscription_id" class="{{ $labelClass }}">
                        Pilih layanan langganan <span class="text-primary" aria-hidden="true">*</span>
                    </label>
                    <select id="subscription_id" name="subscription_id" required
                        @error('subscription_id') aria-invalid="true" aria-describedby="subscription_id-error" @enderror
                        class="{{ $selectClass }} {{ $selectState('subscription_id') }}">
                        <option value="">Pilih langganan</option>
                        @foreach ($subscriptions as $sub)
                            <option value="{{ $sub->id }}" @selected(old('subscription_id') == $sub->id)>
                                {{ $sub->name }} (Jatuh tempo: {{ $sub->next_payment_date ? $sub->next_payment_date->locale('id')->translatedFormat('d M') : '-' }})
                            </option>
                        @endforeach
                    </select>
                    @error('subscription_id')
                        <p id="subscription_id-error" class="text-sm text-[#B42318]">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mt-4 flex flex-col gap-2">
                    <label for="type" class="{{ $labelClass }}">
                        Jenis pengingat <span class="text-primary" aria-hidden="true">*</span>
                    </label>
                    <select id="type" name="type" required
                        @error('type') aria-invalid="true" aria-describedby="type-error" @enderror
                        class="{{ $selectClass }} {{ $selectState('type') }}">
                        @foreach (\App\Enums\ReminderType::cases() as $type)
                            <option value="{{ $type->value }}" @selected(old('type', 'PAYMENT_DUE') === $type->value)>
                                {{ $typeLabels[$type->value] ?? $type->label() }}
                            </option>
                        @endforeach
                    </select>
                    @error('type')
                        <p id="type-error" class="text-sm text-[#B42318]">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mt-4 flex flex-col gap-2">
                    <label for="notify_before_days" class="{{ $labelClass }}">
                        Waktu pemberitahuan (H-hari) <span class="text-primary" aria-hidden="true">*</span>
                    </label>
                    <select id="notify_before_days" name="notify_before_days" required
                        aria-describedby="notify_before_days-help @error('notify_before_days') notify_before_days-error @enderror"
                        @error('notify_before_days') aria-invalid="true" @enderror
                        class="{{ $selectClass }} {{ $selectState('notify_before_days') }}">
                        @foreach ($dayOptions as $days => $dayLabel)
                            <option value="{{ $days }}" @selected($selectedDays === (string) $days)>{{ $dayLabel }}</option>
                        @endforeach
                    </select>
                    <p id="notify_before_days-help" class="text-[13px] leading-5 text-[#6B6B73]">
                        Notifikasi email akan dikirim tepat 1 kali pada jadwal ini per siklus penagihan.
                    </p>
                    @error('notify_before_days')
                        <p id="notify_before_days-error" class="text-sm text-[#B42318]">{{ $message }}</p>
                    @enderror
                </div>

                <label for="is_active"
                    class="mt-4 min-h-11 flex items-center gap-3 text-[15px] font-semibold cursor-pointer {{ $hasSubscriptions ? 'text-ink' : 'text-[#6B6B73] cursor-not-allowed' }}">
                    <input type="checkbox" id="is_active" name="is_active" value="1" checked
                        class="w-5 h-5 m-0 accent-primary">
                    Aktifkan pengingat ini sekarang
                </label>

                <div class="mt-6 flex justify-end">
                    <button type="submit"
                        class="h-[52px] px-8 rounded-full text-[15px] font-bold transition bg-primary hover:bg-primary-hover text-white disabled:bg-[#E4E4E7] disabled:text-[#52525B] disabled:cursor-not-allowed">
                        Simpan pengingat
                    </button>
                </div>
            </fieldset>
        </form>

        @if (! $hasSubscriptions && $reminders->isEmpty())
            <!-- Kosong total: kotak putus-putus seperti board "kosong" -->
            <div class="flex-[2_1_300px] min-w-0 flex flex-col self-stretch">
                <div class="flex items-center justify-between gap-4">
                    <h2 class="text-[22px] leading-[30px] font-bold text-ink tracking-[-0.2px]">Pengingat aktif</h2>
                    <span class="text-[13px] font-semibold text-[#6B6B73]">0</span>
                </div>
                <div
                    class="mt-4 flex-1 rounded-3xl border-[1.5px] border-dashed border-[#D4D4D8] px-7 py-10 flex flex-col items-center justify-center text-center">
                    <div class="w-[72px] h-[72px] rounded-full bg-[#F4F4F5] flex items-center justify-center">
                        <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="#0D0D0F" stroke-width="1.8"
                            stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <circle cx="12" cy="13" r="8"></circle>
                            <path d="M12 9v4l2.5 2M9 2.5h6"></path>
                        </svg>
                    </div>
                    <p class="mt-4 text-lg leading-[26px] font-bold text-ink">Belum ada pengingat</p>
                </div>
            </div>
        @else
            <!-- Panel gelap berisi jumlah dan daftar pengingat -->
            <aside class="flex-[2_1_300px] min-w-0 bg-ink rounded-[28px] p-7 flex flex-col">
                <span class="text-[13px] font-semibold text-zinc-400">Pengingat aktif</span>
                <span class="mt-1.5 text-[56px] leading-[60px] font-bold text-white tracking-[-0.8px]">
                    {{ $reminders->where('is_active', true)->count() }}
                </span>

                @if ($reminders->isEmpty())
                    <div class="mt-6 p-5 rounded-[20px] border-[1.5px] border-dashed border-[#3A3A41] flex flex-col gap-1.5">
                        <span class="text-base font-bold text-white">Belum ada pengingat</span>
                        <span class="text-sm leading-[21px] text-zinc-400">
                            Simpan pengingat pertamamu dan jadwalnya akan tampil di sini.
                        </span>
                    </div>
                @else
                    <p class="mt-1 text-[13px] text-zinc-400">dari {{ $reminders->count() }} pengingat</p>
                    <ul class="mt-6 flex flex-col gap-3">
                        @foreach ($reminders as $reminder)
                            <li class="p-4 rounded-[20px] bg-ink-900 border border-ink-800 flex flex-col gap-3">
                                <div class="min-w-0">
                                    @if ($reminder->subscription)
                                        <a href="{{ route('subscriptions.show', $reminder->subscription) }}"
                                            class="text-base font-bold text-white hover:underline break-words">
                                            {{ $reminder->subscription->name }}
                                        </a>
                                        <span class="block text-[13px] text-zinc-400">
                                            Jatuh tempo:
                                            {{ $reminder->subscription->next_payment_date ? $reminder->subscription->next_payment_date->locale('id')->translatedFormat('d M Y') : '-' }}
                                        </span>
                                    @else
                                        <span class="text-base font-bold text-zinc-400">Langganan telah dihapus</span>
                                    @endif
                                    <span class="mt-2 flex flex-wrap items-center gap-2">
                                        <span
                                            class="h-7 px-3 rounded-full bg-ink-800 text-zinc-200 text-[13px] font-semibold inline-flex items-center whitespace-nowrap">
                                            {{ $typeLabels[$reminder->type->value] ?? $reminder->type->label() }}
                                        </span>
                                        <span class="text-[13px] font-semibold text-zinc-200">
                                            {{ $reminder->notify_before_days == 0 ? 'Hari H' : 'H-' . $reminder->notify_before_days . ' Hari' }}
                                        </span>
                                    </span>
                                </div>
                                <div class="flex items-center justify-between gap-2">
                                    <!-- Klik status untuk mengaktifkan atau menonaktifkan -->
                                    <form action="{{ route('reminders.toggle', $reminder) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                            title="Klik untuk {{ $reminder->is_active ? 'menonaktifkan' : 'mengaktifkan' }} pengingat"
                                            aria-label="{{ $reminder->is_active ? 'Aktif, klik untuk menonaktifkan' : 'Mati, klik untuk mengaktifkan' }}"
                                            class="h-9 px-3.5 rounded-full text-[13px] font-bold inline-flex items-center gap-2 transition {{ $reminder->is_active ? 'bg-primary hover:bg-primary-hover text-white' : 'bg-ink-800 hover:bg-[#3A3A41] text-zinc-300' }}">
                                            <span class="w-2 h-2 rounded-full {{ $reminder->is_active ? 'bg-white' : 'bg-zinc-500' }}"
                                                aria-hidden="true"></span>
                                            {{ $reminder->is_active ? 'Aktif' : 'Mati' }}
                                        </button>
                                    </form>
                                    <form action="{{ route('reminders.destroy', $reminder) }}" method="POST"
                                        onsubmit="return confirm('Hapus konfigurasi pengingat ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="h-9 px-3.5 rounded-xl text-[13px] font-semibold text-[#F97066] hover:bg-ink-800 transition">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </aside>
        @endif
    </div>
@endsection
