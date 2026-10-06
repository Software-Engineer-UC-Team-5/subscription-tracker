@extends('layouts.app')

@section('title', 'Tambah Langganan Baru')

@section('content')
    <div class="max-w-3xl mx-auto">
        <div class="pb-5 border-b border-slate-200 mb-6">
            <a href="{{ route('subscriptions.index') }}" class="text-xs font-medium text-slate-500 hover:text-slate-800 mb-2 inline-flex items-center gap-1">
                &larr; Kembali ke Daftar Langganan
            </a>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Tambah Langganan Baru</h1>
            <p class="text-sm text-slate-500 mt-1">Masukkan informasi detail layanan berlangganan atau masa uji coba gratis Anda.</p>
        </div>

        @if ($errors->any())
            <div class="mb-6 p-4 rounded-lg bg-rose-50 border border-rose-200 text-rose-800 text-sm">
                <p class="font-semibold mb-1">Terdapat kesalahan pengisian formulir:</p>
                <ul class="list-disc list-inside space-y-0.5 text-xs">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('subscriptions.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Informasi Layanan -->
            <div class="bg-white p-6 rounded-xl border border-slate-200 space-y-4">
                <h2 class="text-sm font-bold uppercase tracking-wider text-slate-700 border-b border-slate-100 pb-2">Informasi Layanan</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Nama Langganan -->
                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Layanan / Platform <span class="text-rose-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" placeholder="Contoh: Netflix Premium, Spotify Family" required
                            class="w-full px-3.5 py-2 text-sm rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                    </div>

                    <!-- Kategori -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Kategori Layanan</label>
                        <select name="category_id"
                            class="w-full px-3.5 py-2 text-sm rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                            <option value="">Pilih Kategori (Opsional)</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Metode Pembayaran -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Metode Pembayaran</label>
                        <select name="payment_method_id"
                            class="w-full px-3.5 py-2 text-sm rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                            <option value="">Pilih Metode Bayar (Opsional)</option>
                            @foreach ($paymentMethods as $pm)
                                <option value="{{ $pm->id }}" {{ old('payment_method_id') == $pm->id ? 'selected' : '' }}>
                                    {{ $pm->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Detail Biaya & Siklus Penagihan -->
            <div class="bg-white p-6 rounded-xl border border-slate-200 space-y-4">
                <h2 class="text-sm font-bold uppercase tracking-wider text-slate-700 border-b border-slate-100 pb-2">Biaya & Jadwal Penagihan</h2>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Biaya -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Biaya Langganan (Rp) <span class="text-rose-500">*</span></label>
                        <input type="number" step="1000" name="price" value="{{ old('price', 0) }}" placeholder="186000" required
                            class="w-full px-3.5 py-2 text-sm rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                    </div>

                    <!-- Siklus Pembayaran -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Periode Pembayaran <span class="text-rose-500">*</span></label>
                        <select name="billing_period" required
                            class="w-full px-3.5 py-2 text-sm rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                            @foreach (\App\Enums\BillingPeriod::cases() as $bp)
                                <option value="{{ $bp->value }}" {{ old('billing_period', 'MONTHLY') === $bp->value ? 'selected' : '' }}>
                                    {{ $bp->label() }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Tanggal Jatuh Tempo Berikutnya -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Tanggal Tagihan Berikutnya <span class="text-rose-500">*</span></label>
                        <input type="date" name="next_payment_date" value="{{ old('next_payment_date', now()->addMonth()->format('Y-m-d')) }}" required
                            class="w-full px-3.5 py-2 text-sm rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                    </div>
                </div>

                <div class="pt-2">
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Status Awal</label>
                    <select name="status"
                        class="w-full md:w-1/2 px-3.5 py-2 text-sm rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                        @foreach (\App\Enums\SubscriptionStatus::cases() as $st)
                            <option value="{{ $st->value }}" {{ old('status', 'ACTIVE') === $st->value ? 'selected' : '' }}>
                                {{ $st->label() }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Bagian Free Trial (Opsional) -->
            <div class="bg-amber-50/50 p-6 rounded-xl border border-amber-200/70 space-y-4">
                <div class="flex items-center gap-2">
                    <input type="checkbox" id="is_free_trial" name="is_free_trial" value="1" {{ old('is_free_trial') ? 'checked' : '' }}
                        class="w-4 h-4 text-blue-600 rounded border-slate-300 focus:ring-blue-500">
                    <label for="is_free_trial" class="text-sm font-bold text-slate-800 cursor-pointer">
                        Layanan ini sedang dalam masa uji coba gratis (Free Trial)
                    </label>
                </div>
                <p class="text-xs text-slate-500">Centang opsi ini jika Anda sedang mencoba layanan dan ingin diingatkan sebelum otomatis dikenakan autodebit.</p>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-2">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Mulai Trial</label>
                        <input type="date" name="trial_start_date" value="{{ old('trial_start_date', now()->format('Y-m-d')) }}"
                            class="w-full px-3.5 py-2 text-sm rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none bg-white">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Berakhir Trial</label>
                        <input type="date" name="trial_end_date" value="{{ old('trial_end_date', now()->addDays(14)->format('Y-m-d')) }}"
                            class="w-full px-3.5 py-2 text-sm rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none bg-white">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Batas Batal (H- hari)</label>
                        <input type="number" min="0" max="7" name="cancel_before_days" value="{{ old('cancel_before_days', 1) }}"
                            class="w-full px-3.5 py-2 text-sm rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none bg-white">
                    </div>
                </div>
            </div>

            <!-- Tombol Simpan -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200">
                <a href="{{ route('subscriptions.index') }}"
                    class="px-5 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-100 rounded-lg transition">
                    Batal
                </a>
                <button type="submit"
                    class="px-5 py-2.5 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-lg shadow-sm transition">
                    Simpan Langganan
                </button>
            </div>
        </form>
    </div>
@endsection
