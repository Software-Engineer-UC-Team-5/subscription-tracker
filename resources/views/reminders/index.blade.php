@extends('layouts.app')

@section('title', 'Pengaturan Pengingat')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-5 border-b border-slate-200 gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight">Pengaturan Pengingat (Reminders)</h1>
                <p class="text-sm text-slate-500 mt-1">Atur jadwal pengingat sebelum tagihan autodebit diproses atau masa uji coba gratis berakhir.</p>
            </div>
        </div>

    @if ($errors->any())
        <div class="mb-6 p-4 rounded-lg bg-rose-50 border border-rose-200 text-rose-800 text-sm">
            <p class="font-semibold mb-1">Gagal menyimpan pengingat:</p>
            <ul class="list-disc list-inside space-y-0.5 text-xs">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Formulir Tambah Pengingat Baru -->
        <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm h-fit">
            <h2 class="text-sm font-bold uppercase tracking-wider text-slate-800 border-b border-slate-100 pb-2 mb-4">
                Buat Pengingat Baru
            </h2>

            @if ($subscriptions->isEmpty())
                <div class="p-4 bg-amber-50 border border-amber-200 rounded-lg text-amber-800 text-xs">
                    <p class="font-semibold mb-1">Tidak Ada Langganan Aktif</p>
                    <p>Anda belum memiliki layanan langganan aktif. Silakan tambahkan langganan terlebih dahulu sebelum mengatur pengingat.</p>
                    <a href="{{ route('subscriptions.create') }}" class="mt-2 inline-block font-semibold text-blue-600 hover:underline">
                        + Tambah Langganan Baru &rarr;
                    </a>
                </div>
            @else
                <form action="{{ route('reminders.store') }}" method="POST" class="space-y-4">
                    @csrf

                    <!-- Pilih Subscription -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Pilih Layanan Langganan <span class="text-rose-500">*</span></label>
                        <select name="subscription_id" required
                            class="w-full px-3 py-2 text-sm rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                            <option value="">-- Pilih Langganan --</option>
                            @foreach ($subscriptions as $sub)
                                <option value="{{ $sub->id }}" {{ old('subscription_id') == $sub->id ? 'selected' : '' }}>
                                    {{ $sub->name }} (Jatuh tempo: {{ $sub->next_payment_date ? $sub->next_payment_date->format('d M') : '-' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Jenis Pengingat -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Jenis Pengingat <span class="text-rose-500">*</span></label>
                        <select name="type" required
                            class="w-full px-3 py-2 text-sm rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                            @foreach (\App\Enums\ReminderType::cases() as $type)
                                <option value="{{ $type->value }}" {{ old('type', 'PAYMENT_DUE') === $type->value ? 'selected' : '' }}>
                                    {{ $type->label() }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Berapa Hari Sebelumnya -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Waktu Pemberitahuan (H- Hari) <span class="text-rose-500">*</span></label>
                        <select name="notify_before_days" required
                            class="w-full px-3 py-2 text-sm rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                            <option value="1" {{ old('notify_before_days', 3) == 1 ? 'selected' : '' }}>H-1 Hari Sebelumnya</option>
                            <option value="2" {{ old('notify_before_days', 3) == 2 ? 'selected' : '' }}>H-2 Hari Sebelumnya</option>
                            <option value="3" {{ old('notify_before_days', 3) == 3 ? 'selected' : '' }}>H-3 Hari Sebelumnya (Rekomendasi)</option>
                            <option value="5" {{ old('notify_before_days', 3) == 5 ? 'selected' : '' }}>H-5 Hari Sebelumnya</option>
                            <option value="7" {{ old('notify_before_days', 3) == 7 ? 'selected' : '' }}>H-7 Hari (1 Minggu Sebelumnya)</option>
                        </select>
                    </div>

                    <div class="flex items-center gap-2 pt-2">
                        <input type="checkbox" id="is_active" name="is_active" value="1" checked
                            class="w-4 h-4 text-blue-600 rounded border-slate-300 focus:ring-blue-500">
                        <label for="is_active" class="text-xs font-semibold text-slate-700 cursor-pointer">
                            Aktifkan pengingat ini sekarang
                        </label>
                    </div>

                    <button type="submit"
                        class="w-full mt-2 px-4 py-2.5 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-lg shadow-sm transition">
                        Simpan Pengingat
                    </button>
                </form>
            @endif
        </div>

        <!-- Daftar Pengingat Aktif -->
        <div class="lg:col-span-2 bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h2 class="text-base font-semibold text-slate-900">Daftar Pengingat Anda</h2>
                    <p class="text-xs text-slate-500">Pemberitahuan otomatis yang aktif dikelola oleh sistem scheduler</p>
                </div>
                <span class="text-xs font-bold px-2 py-0.5 rounded-full bg-slate-100 text-slate-700">
                    Total: {{ $reminders->count() }}
                </span>
            </div>

            @if ($reminders->isEmpty())
                <div class="py-12 px-4 text-center">
                    <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-slate-100 text-slate-400 mb-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                    </div>
                    <p class="text-sm font-medium text-slate-700">Belum ada pengingat yang dikonfigurasi.</p>
                    <p class="text-xs text-slate-400 mt-1">Gunakan formulir di sebelah kiri untuk mengatur pengingat tagihan langganan Anda.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-600">
                        <thead class="bg-slate-50 text-xs uppercase font-semibold text-slate-500 border-b border-slate-200">
                            <tr>
                                <th class="px-6 py-3.5">Layanan</th>
                                <th class="px-6 py-3.5">Jenis Pemicu</th>
                                <th class="px-6 py-3.5">Waktu Kirim</th>
                                <th class="px-6 py-3.5">Status</th>
                                <th class="px-6 py-3.5 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($reminders as $reminder)
                                <tr class="hover:bg-slate-50/75 transition">
                                    <td class="px-6 py-4 font-semibold text-slate-900">
                                        <a href="{{ route('subscriptions.show', $reminder->subscription) }}" class="hover:text-blue-600">
                                            {{ $reminder->subscription->name ?? 'Langganan Telah Dihapus' }}
                                        </a>
                                        <span class="text-xs text-slate-400 block font-normal">
                                            Jatuh tempo: {{ $reminder->subscription->next_payment_date ? $reminder->subscription->next_payment_date->format('d M Y') : '-' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-slate-100 text-slate-800">
                                            {{ $reminder->type->label() }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 font-medium text-slate-700">
                                        H-{{ $reminder->notify_before_days }} Hari
                                    </td>
                                    <td class="px-6 py-4">
                                        @if ($reminder->is_active)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                Aktif
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                                                Mati
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <form action="{{ route('reminders.destroy', $reminder) }}" method="POST" class="inline"
                                            onsubmit="return confirm('Hapus konfigurasi pengingat ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-xs font-semibold text-rose-600 hover:text-rose-800 transition">
                                                Hapus
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection