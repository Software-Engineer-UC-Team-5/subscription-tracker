@extends('layouts.app')

@section('title', 'Tambah Langganan')

@section('page_title', 'Tambah langganan')
@section('page_subtitle', 'Isi yang kamu tahu dulu. Sisanya bisa dilengkapi nanti.')

@section('content')
    @php
        $labelClass = 'text-sm font-semibold text-ink';
        $fieldClass = 'w-full h-[52px] rounded-2xl text-base text-ink placeholder:text-[#A1A1AA] outline-none transition focus:bg-white focus:border-2 focus:border-primary';
        $fieldState = fn (string $field, string $idle = 'bg-[#F4F4F5]') => $errors->has($field)
            ? 'bg-white border-2 border-[#B42318]'
            : $idle . ' border-[1.5px] border-[#E4E4E7]';
        $periodLabels = [
            'DAILY' => 'per hari',
            'WEEKLY' => 'per minggu',
            'MONTHLY' => 'per bulan',
            'QUARTERLY' => 'per 3 bulan',
            'YEARLY' => 'per tahun',
        ];
        $selectedPeriod = old('billing_period', 'MONTHLY');
        $isTrial = (bool) old('is_free_trial');

        // Error yang tidak punya field di formulir ini tetap ditampilkan di atas
        $formFields = ['name', 'price', 'billing_period', 'next_payment_date', 'status', 'category_id',
            'payment_method_id', 'trial_start_date', 'trial_end_date', 'cancel_before_days'];
        $otherErrors = collect($errors->getMessages())->except($formFields)->flatten();
    @endphp

    <div class="flex flex-wrap gap-8">
        <form action="{{ route('subscriptions.store') }}" method="POST" id="subscription-form"
            class="flex-[1.6_1_440px] min-w-0 flex flex-col gap-5">
            @csrf

            <a href="{{ route('subscriptions.index') }}"
                class="self-start min-h-11 inline-flex items-center gap-2 text-sm font-bold text-ink hover:underline">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"
                    stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M15 6l-6 6 6 6"></path>
                </svg>
                Kembali ke daftar langganan
            </a>

            @if ($otherErrors->isNotEmpty())
                <div role="alert"
                    class="px-5 py-4 rounded-2xl bg-[#FEF3F2] border border-[#FECDCA] text-[#B42318] text-sm font-semibold">
                    @foreach ($otherErrors as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <h2 class="text-xl leading-7 font-bold text-ink">Informasi layanan</h2>
            <div class="flex flex-col gap-2">
                <label for="name" class="{{ $labelClass }}">Nama layanan <span class="text-primary" aria-hidden="true">*</span></label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required maxlength="100"
                    placeholder="Netflix, Spotify, iCloud…"
                    @error('name') aria-invalid="true" aria-describedby="name-error" @enderror
                    class="{{ $fieldClass }} {{ $fieldState('name') }} px-[18px]">
                @error('name')
                    <p id="name-error" class="text-sm text-[#B42318]">{{ $message }}</p>
                @enderror
            </div>

            <h2 class="mt-3 pt-6 border-t border-[#EEEEF0] text-xl leading-7 font-bold text-ink">Biaya dan jadwal penagihan</h2>
            <div class="flex flex-wrap gap-4">
                <div class="flex-[1_1_200px] flex flex-col gap-2">
                    <label for="price" class="{{ $labelClass }}">Biaya (Rp) <span class="text-primary" aria-hidden="true">*</span></label>
                    <input type="number" id="price" name="price" value="{{ old('price') }}" required min="0" step="any"
                        inputmode="numeric" placeholder="54000"
                        @error('price') aria-invalid="true" aria-describedby="price-error" @enderror
                        class="{{ $fieldClass }} {{ $fieldState('price') }} px-[18px]">
                    @error('price')
                        <p id="price-error" class="text-sm text-[#B42318]">{{ $message }}</p>
                    @enderror
                </div>
                <div class="flex-[1_1_200px] flex flex-col gap-2">
                    <label for="next_payment_date" class="{{ $labelClass }}">Tagihan berikutnya <span class="text-primary" aria-hidden="true">*</span></label>
                    <input type="date" id="next_payment_date" name="next_payment_date"
                        value="{{ old('next_payment_date', now()->addMonth()->format('Y-m-d')) }}" required
                        @error('next_payment_date') aria-invalid="true" aria-describedby="next_payment_date-error" @enderror
                        class="{{ $fieldClass }} {{ $fieldState('next_payment_date') }} px-[18px]">
                    @error('next_payment_date')
                        <p id="next_payment_date-error" class="text-sm text-[#B42318]">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Siklus tagihan berupa pilihan tombol (radio) -->
            <fieldset class="flex flex-col gap-2">
                <legend class="{{ $labelClass }} mb-2">Siklus tagihan <span class="text-primary" aria-hidden="true">*</span></legend>
                <div class="flex flex-wrap gap-2">
                    @foreach (\App\Enums\BillingPeriod::cases() as $bp)
                        <label
                            class="h-12 px-[22px] rounded-3xl inline-flex items-center text-[15px] cursor-pointer transition border-[1.5px] border-[#E4E4E7] bg-white text-ink font-semibold hover:border-ink has-[:checked]:bg-ink has-[:checked]:border-ink has-[:checked]:text-white has-[:checked]:font-bold has-[:focus-visible]:ring-2 has-[:focus-visible]:ring-primary has-[:focus-visible]:ring-offset-2">
                            <input type="radio" name="billing_period" value="{{ $bp->value }}" class="sr-only" required
                                data-period-label="{{ $periodLabels[$bp->value] ?? $bp->label() }}"
                                @checked($selectedPeriod === $bp->value)>
                            {{ $bp->label() }}
                        </label>
                    @endforeach
                </div>
                @error('billing_period')
                    <p class="text-sm text-[#B42318]">{{ $message }}</p>
                @enderror
            </fieldset>

            <div class="flex flex-wrap gap-4">
                <div class="flex-[1_1_200px] flex flex-col gap-2">
                    <label for="category_id" class="{{ $labelClass }}">Kategori</label>
                    <select id="category_id" name="category_id"
                        @error('category_id') aria-invalid="true" aria-describedby="category_id-error" @enderror
                        class="{{ $fieldClass }} {{ $fieldState('category_id') }} px-3.5 cursor-pointer">
                        <option value="">Pilih kategori</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <p id="category_id-error" class="text-sm text-[#B42318]">{{ $message }}</p>
                    @enderror
                </div>
                <div class="flex-[1_1_200px] flex flex-col gap-2">
                    <label for="payment_method_id" class="{{ $labelClass }}">Metode bayar</label>
                    <select id="payment_method_id" name="payment_method_id"
                        @error('payment_method_id') aria-invalid="true" aria-describedby="payment_method_id-error" @enderror
                        class="{{ $fieldClass }} {{ $fieldState('payment_method_id') }} px-3.5 cursor-pointer">
                        <option value="">Pilih metode</option>
                        @foreach ($paymentMethods as $pm)
                            <option value="{{ $pm->id }}" @selected(old('payment_method_id') == $pm->id)>{{ $pm->name }}</option>
                        @endforeach
                    </select>
                    @error('payment_method_id')
                        <p id="payment_method_id-error" class="text-sm text-[#B42318]">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex flex-col gap-2">
                <label for="status" class="{{ $labelClass }}">Status awal <span class="text-primary" aria-hidden="true">*</span></label>
                <select id="status" name="status"
                    @error('status') aria-invalid="true" aria-describedby="status-error" @enderror
                    class="{{ $fieldClass }} {{ $fieldState('status') }} px-3.5 cursor-pointer">
                    @foreach (\App\Enums\SubscriptionStatus::cases() as $st)
                        <option value="{{ $st->value }}" @selected(old('status', 'ACTIVE') === $st->value)>{{ $st->label() }}</option>
                    @endforeach
                </select>
                @error('status')
                    <p id="status-error" class="text-sm text-[#B42318]">{{ $message }}</p>
                @enderror
            </div>

            <!-- Masa uji coba gratis (opsional) -->
            <div class="p-5 rounded-3xl bg-[#F4F4F5] flex flex-col gap-4">
                <label for="is_free_trial" class="min-h-11 flex items-center gap-3 text-[15px] font-semibold text-ink cursor-pointer">
                    <input type="checkbox" id="is_free_trial" name="is_free_trial" value="1" @checked($isTrial)
                        aria-controls="trial-fields" class="w-5 h-5 m-0 accent-primary">
                    Layanan ini sedang dalam masa uji coba gratis
                </label>
                <!-- Isian trial dinonaktifkan saat kotak tidak dicentang, jadi tidak ikut terkirim -->
                <fieldset id="trial-fields" class="flex flex-col gap-4 disabled:hidden" @disabled(! $isTrial)>
                    <div class="flex flex-wrap gap-4">
                        <div class="flex-[1_1_160px] flex flex-col gap-2">
                            <label for="trial_start_date" class="{{ $labelClass }}">Mulai trial</label>
                            <input type="date" id="trial_start_date" name="trial_start_date"
                                value="{{ old('trial_start_date', now()->format('Y-m-d')) }}"
                                @error('trial_start_date') aria-invalid="true" aria-describedby="trial_start_date-error" @enderror
                                class="{{ $fieldClass }} {{ $fieldState('trial_start_date', 'bg-white') }} px-[18px]">
                            @error('trial_start_date')
                                <p id="trial_start_date-error" class="text-sm text-[#B42318]">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="flex-[1_1_160px] flex flex-col gap-2">
                            <label for="trial_end_date" class="{{ $labelClass }}">Berakhir trial</label>
                            <input type="date" id="trial_end_date" name="trial_end_date"
                                value="{{ old('trial_end_date', now()->addDays(14)->format('Y-m-d')) }}"
                                @error('trial_end_date') aria-invalid="true" aria-describedby="trial_end_date-error" @enderror
                                class="{{ $fieldClass }} {{ $fieldState('trial_end_date', 'bg-white') }} px-[18px]">
                            @error('trial_end_date')
                                <p id="trial_end_date-error" class="text-sm text-[#B42318]">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="flex-[1_1_160px] flex flex-col gap-2">
                            <label for="cancel_before_days" class="{{ $labelClass }}">Batas batal (H-hari)</label>
                            <input type="number" id="cancel_before_days" name="cancel_before_days" min="0" max="7"
                                inputmode="numeric" value="{{ old('cancel_before_days', 1) }}"
                                @error('cancel_before_days') aria-invalid="true" aria-describedby="cancel_before_days-error" @enderror
                                class="{{ $fieldClass }} {{ $fieldState('cancel_before_days', 'bg-white') }} px-[18px]">
                            @error('cancel_before_days')
                                <p id="cancel_before_days-error" class="text-sm text-[#B42318]">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <p class="text-[13px] leading-5 text-[#6B6B73]">
                        Kami ingatkan sebelum batas batal supaya kamu tidak ditagih tanpa sadar.
                    </p>
                </fieldset>
            </div>

            <p class="text-[13px] text-[#6B6B73]"><span class="text-primary">*</span> wajib diisi</p>

            <div class="mt-1 flex flex-wrap gap-3">
                <button type="submit"
                    class="h-14 px-9 rounded-full bg-primary hover:bg-primary-hover text-white text-base font-bold transition">
                    Simpan langganan
                </button>
                <a href="{{ route('subscriptions.index') }}"
                    class="h-14 px-7 rounded-full border-[1.5px] border-ink hover:bg-[#F4F4F5] text-ink text-base font-bold inline-flex items-center transition">
                    Batal
                </a>
            </div>
        </form>

        <!-- Pratinjau langsung dari isian formulir -->
        <aside aria-label="Pratinjau" class="flex-[1_1_280px] min-w-0 self-start rounded-[28px] bg-ink p-7 flex flex-col lg:sticky lg:top-6">
            <span class="text-[13px] font-semibold text-zinc-400">Pratinjau</span>
            <div class="mt-5 flex items-center gap-3.5 min-w-0">
                <span data-preview="initial"
                    class="w-[52px] h-[52px] rounded-2xl bg-ink-800 text-white text-xl font-bold flex items-center justify-center flex-none">?</span>
                <span data-preview="name" class="text-xl leading-7 font-bold text-white break-words min-w-0">Nama layanan</span>
            </div>
            <p data-preview="price" class="mt-7 text-[44px] leading-[52px] font-bold text-white tracking-[-0.7px] break-words">Rp 0</p>
            <p data-preview="period" class="mt-0.5 text-sm text-zinc-400">per bulan</p>
            <div class="mt-6 pt-[18px] border-t border-ink-800 flex items-center gap-2.5 text-sm text-zinc-200">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#A1A1AA" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <rect x="3" y="5" width="18" height="15" rx="3"></rect>
                    <path d="M3 10h18M8 3v4M16 3v4"></path>
                </svg>
                <span>Tagihan berikutnya: <span data-preview="date">-</span></span>
            </div>
        </aside>
    </div>

    <script>
        {
            const form = document.getElementById('subscription-form');
            const trialToggle = document.getElementById('is_free_trial');
            const trialFields = document.getElementById('trial-fields');
            const preview = name => document.querySelector(`[data-preview="${name}"]`);
            const dateFormat = new Intl.DateTimeFormat('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });

            /**
             * Perbarui kartu pratinjau dari nilai formulir; textContent menjaga isian tetap teks biasa.
             */
            const render = () => {
                const name = form.elements.name.value.trim();
                const price = Number(form.elements.price.value) || 0;
                const period = form.querySelector('[name="billing_period"]:checked');
                const date = form.elements.next_payment_date.value;
                preview('initial').textContent = name ? name.charAt(0).toUpperCase() : '?';
                preview('name').textContent = name || 'Nama layanan';
                preview('price').textContent = 'Rp ' + Math.round(price).toLocaleString('id-ID');
                preview('period').textContent = period ? period.dataset.periodLabel : '';
                preview('date').textContent = date ? dateFormat.format(new Date(date + 'T00:00:00')) : '-';
            };

            // Tampilkan isian trial hanya saat kotak dicentang.
            trialToggle.addEventListener('change', () => {
                trialFields.disabled = !trialToggle.checked;
            });
            form.addEventListener('input', render);
            form.addEventListener('change', render);
            render();
        }
    </script>
@endsection
