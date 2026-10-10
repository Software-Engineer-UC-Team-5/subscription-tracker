@extends('layouts.app')

@section('title', 'Ubah Langganan')

@section('header')
    <div>
        <a href="{{ route('subscriptions.show', $subscription) }}"
            class="min-h-11 inline-flex items-center gap-2 text-sm font-semibold text-zinc-400 hover:text-white transition">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"
                stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M15 6l-6 6 6 6"></path>
            </svg>
            Kembali ke detail langganan
        </a>
        <h1 class="text-4xl sm:text-5xl sm:leading-[56px] font-bold text-white tracking-[-0.7px]">Ubah langganan</h1>
        <p class="mt-1.5 text-base text-zinc-400">Perbarui data layanan yang kamu catat.</p>
    </div>
@endsection

@section('content')
    @php
        $labelClass = 'text-sm font-semibold text-ink';
        $fieldClass = 'w-full h-[52px] rounded-2xl text-base text-ink outline-none transition focus:bg-white focus:border-2 focus:border-primary';
        $fieldState = fn (string $field) => $errors->has($field)
            ? 'bg-white border-2 border-[#B42318]'
            : 'bg-[#F4F4F5] border-[1.5px] border-[#E4E4E7]';

        // Error yang tidak punya field di formulir ini tetap ditampilkan di atas
        $formFields = ['name', 'price', 'billing_period', 'next_payment_date', 'status', 'category_id', 'payment_method_id'];
        $otherErrors = collect($errors->getMessages())->except($formFields)->flatten();
    @endphp

    <form action="{{ route('subscriptions.update', $subscription) }}" method="POST" class="w-full max-w-[720px] flex flex-col gap-5">
        @csrf
        @method('PUT')

        @if ($otherErrors->isNotEmpty())
            <div role="alert"
                class="px-5 py-4 rounded-2xl bg-[#FEF3F2] border border-[#FECDCA] text-[#B42318] text-sm font-semibold">
                @foreach ($otherErrors as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <div class="flex flex-col gap-2">
            <label for="name" class="{{ $labelClass }}">Nama layanan <span class="text-primary" aria-hidden="true">*</span></label>
            <input type="text" id="name" name="name" value="{{ old('name', $subscription->name) }}" required maxlength="100"
                @error('name') aria-invalid="true" aria-describedby="name-error" @enderror
                class="{{ $fieldClass }} {{ $fieldState('name') }} px-[18px]">
            @error('name')
                <p id="name-error" class="text-sm text-[#B42318]">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex flex-wrap gap-4">
            <div class="flex-[1_1_220px] flex flex-col gap-2">
                <label for="category_id" class="{{ $labelClass }}">Kategori</label>
                <select id="category_id" name="category_id"
                    @error('category_id') aria-invalid="true" aria-describedby="category_id-error" @enderror
                    class="{{ $fieldClass }} {{ $fieldState('category_id') }} px-3.5 cursor-pointer">
                    <option value="">Pilih kategori</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected(old('category_id', $subscription->category_id) == $category->id)>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                @error('category_id')
                    <p id="category_id-error" class="text-sm text-[#B42318]">{{ $message }}</p>
                @enderror
            </div>
            <div class="flex-[1_1_220px] flex flex-col gap-2">
                <label for="payment_method_id" class="{{ $labelClass }}">Metode pembayaran</label>
                <select id="payment_method_id" name="payment_method_id"
                    @error('payment_method_id') aria-invalid="true" aria-describedby="payment_method_id-error" @enderror
                    class="{{ $fieldClass }} {{ $fieldState('payment_method_id') }} px-3.5 cursor-pointer">
                    <option value="">Pilih metode</option>
                    @foreach ($paymentMethods as $pm)
                        <option value="{{ $pm->id }}" @selected(old('payment_method_id', $subscription->payment_method_id) == $pm->id)>
                            {{ $pm->name }}
                        </option>
                    @endforeach
                </select>
                @error('payment_method_id')
                    <p id="payment_method_id-error" class="text-sm text-[#B42318]">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="flex flex-wrap gap-4">
            <div class="flex-[1_1_220px] flex flex-col gap-2">
                <label for="price" class="{{ $labelClass }}">Biaya langganan (Rp) <span class="text-primary" aria-hidden="true">*</span></label>
                <input type="number" id="price" name="price" value="{{ old('price', $subscription->price) }}" required
                    min="0" step="any" inputmode="numeric"
                    @error('price') aria-invalid="true" aria-describedby="price-error" @enderror
                    class="{{ $fieldClass }} {{ $fieldState('price') }} px-[18px]">
                @error('price')
                    <p id="price-error" class="text-sm text-[#B42318]">{{ $message }}</p>
                @enderror
            </div>
            <div class="flex-[1_1_220px] flex flex-col gap-2">
                <label for="billing_period" class="{{ $labelClass }}">Periode pembayaran <span class="text-primary" aria-hidden="true">*</span></label>
                <select id="billing_period" name="billing_period" required
                    @error('billing_period') aria-invalid="true" aria-describedby="billing_period-error" @enderror
                    class="{{ $fieldClass }} {{ $fieldState('billing_period') }} px-3.5 cursor-pointer">
                    @foreach (\App\Enums\BillingPeriod::cases() as $bp)
                        <option value="{{ $bp->value }}" @selected(old('billing_period', $subscription->billing_period->value) === $bp->value)>
                            {{ $bp->label() }}
                        </option>
                    @endforeach
                </select>
                @error('billing_period')
                    <p id="billing_period-error" class="text-sm text-[#B42318]">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="flex flex-wrap gap-4">
            <div class="flex-[1_1_220px] flex flex-col gap-2">
                <label for="next_payment_date" class="{{ $labelClass }}">Tanggal tagihan berikutnya <span class="text-primary" aria-hidden="true">*</span></label>
                <input type="date" id="next_payment_date" name="next_payment_date"
                    value="{{ old('next_payment_date', $subscription->next_payment_date ? $subscription->next_payment_date->format('Y-m-d') : '') }}"
                    required
                    @error('next_payment_date') aria-invalid="true" aria-describedby="next_payment_date-error" @enderror
                    class="{{ $fieldClass }} {{ $fieldState('next_payment_date') }} px-[18px]">
                @error('next_payment_date')
                    <p id="next_payment_date-error" class="text-sm text-[#B42318]">{{ $message }}</p>
                @enderror
            </div>
            <div class="flex-[1_1_220px] flex flex-col gap-2">
                <label for="status" class="{{ $labelClass }}">Status langganan <span class="text-primary" aria-hidden="true">*</span></label>
                <select id="status" name="status" required
                    @error('status') aria-invalid="true" aria-describedby="status-error" @enderror
                    class="{{ $fieldClass }} {{ $fieldState('status') }} px-3.5 cursor-pointer">
                    @foreach (\App\Enums\SubscriptionStatus::cases() as $st)
                        <option value="{{ $st->value }}" @selected(old('status', $subscription->status->value) === $st->value)>
                            {{ $st->label() }}
                        </option>
                    @endforeach
                </select>
                @error('status')
                    <p id="status-error" class="text-sm text-[#B42318]">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="mt-1 flex flex-wrap gap-3">
            <button type="submit"
                class="h-14 px-9 rounded-full bg-primary hover:bg-primary-hover text-white text-base font-bold transition">
                Simpan perubahan
            </button>
            <a href="{{ route('subscriptions.show', $subscription) }}"
                class="h-14 px-7 rounded-full border-[1.5px] border-ink hover:bg-[#F4F4F5] text-ink text-base font-bold inline-flex items-center transition">
                Batal
            </a>
        </div>
    </form>
@endsection
