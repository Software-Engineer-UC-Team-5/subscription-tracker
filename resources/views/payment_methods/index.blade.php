@extends('layouts.app')

@section('title', 'Metode Pembayaran')

@section('content')
    <!-- Header halaman dan tombol tambah metode pembayaran -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-slate-200 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Metode Pembayaran</h1>
            <p class="text-sm text-slate-600 mt-1">Kelola metode pembayaran.</p>
        </div>
        <a href="{{ route('payment-methods.create') }}"
            data-payment-method-action="{{ route('payment-methods.store') }}"
            aria-haspopup="dialog" aria-controls="payment-method-dialog"
            class="inline-flex items-center px-4 py-2 bg-primary hover:bg-primary-hover text-white text-sm font-medium rounded-lg shadow-sm transition">
            + Tambah Metode
        </a>
    </div>

    <!-- Pesan penolakan penghapusan jika metode pembayaran masih digunakan -->
    @error('payment_method')
        <p role="alert" class="mb-6 p-4 rounded-lg bg-rose-50 border border-rose-200 text-rose-800 text-sm">
            {{ $message }}
        </p>
    @enderror

    <!-- Daftar metode pembayaran milik pengguna beserta aksi edit dan hapus -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        @if ($paymentMethods->isEmpty())
            <div class="py-12 px-4 text-center">
                <p class="text-sm font-medium text-slate-700">Belum ada metode pembayaran.</p>
                <p class="text-sm text-slate-600 mt-1">Klik Tambah Metode untuk menambahkan metode pembayaran Anda.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 text-xs uppercase text-slate-600 border-b border-slate-200">
                        <tr>
                            <th scope="col" class="px-6 py-3.5">Metode pembayaran</th>
                            <th scope="col" class="px-6 py-3.5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($paymentMethods as $paymentMethod)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="px-6 py-4 font-medium text-slate-900">{{ $paymentMethod->name }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-end gap-4">
                                        <a href="{{ route('payment-methods.edit', $paymentMethod) }}"
                                            data-payment-method-action="{{ route('payment-methods.update', $paymentMethod) }}"
                                            data-payment-method-name="{{ $paymentMethod->name }}"
                                            aria-haspopup="dialog" aria-controls="payment-method-dialog"
                                            aria-label="Edit metode pembayaran {{ $paymentMethod->name }}"
                                            class="font-medium text-blue-700 hover:underline">Edit</a>
                                        <!-- Minta konfirmasi sebelum mengirim permintaan hapus dengan token CSRF -->
                                        <form action="{{ route('payment-methods.destroy', $paymentMethod) }}" method="POST"
                                            onsubmit="return confirm('Hapus metode pembayaran ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                aria-label="Hapus metode pembayaran {{ $paymentMethod->name }}"
                                                class="font-medium text-rose-700 hover:underline">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <!-- Popup digunakan bersama untuk tambah dan edit; input hanya meminta nama metode pembayaran -->
    <dialog id="payment-method-dialog" aria-labelledby="payment-method-title"
        class="m-auto w-[90vw] max-w-lg max-h-[90dvh] overflow-y-auto p-6 rounded-xl border border-slate-200 bg-white shadow-xl backdrop:bg-slate-900/40">
        <h2 id="payment-method-title" class="text-xl font-bold text-slate-900 mb-5">Tambah Metode Pembayaran</h2>
        <form id="payment-method-form" action="{{ route('payment-methods.store') }}" method="POST" class="space-y-5">
            @csrf
            <input type="hidden" name="_method" value="POST">

            <div>
                <label for="payment-method-name" class="block text-sm font-medium text-slate-700 mb-2">
                    Nama metode pembayaran
                </label>
                <input type="text" id="payment-method-name" name="name" required maxlength="100" autofocus
                    placeholder="Contoh: BCA Debit" aria-describedby="payment-method-help payment-method-error"
                    class="w-full px-3 py-2 text-sm rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <p id="payment-method-help" class="mt-2 text-sm text-slate-600">
                    Jangan masukkan nomor kartu, CVV, atau PIN.
                </p>
                <p id="payment-method-error" role="alert" hidden class="mt-2 text-sm text-rose-700"></p>
            </div>

            <div class="flex items-center justify-end gap-3">
                <button type="button" id="payment-method-cancel"
                    class="px-4 py-2 text-sm font-medium text-slate-700 bg-white hover:bg-slate-50 border border-slate-300 rounded-lg transition">
                    Batal
                </button>
                <button type="submit"
                    class="px-4 py-2 bg-primary hover:bg-primary-hover text-white text-sm font-medium rounded-lg transition disabled:opacity-50 disabled:cursor-wait">
                    Simpan
                </button>
            </div>
        </form>
    </dialog>

    <script>
        {
            // Ambil elemen popup dan formulir tanpa menambahkan variabel ke lingkup global.
            const dialog = document.getElementById('payment-method-dialog');
            const form = document.getElementById('payment-method-form');
            const nameInput = document.getElementById('payment-method-name');
            const error = document.getElementById('payment-method-error');
            const cancel = document.getElementById('payment-method-cancel');
            const submit = form.querySelector('[type="submit"]');

            // Pasang pembuka popup pada tautan tambah dan edit di daftar.
            document.querySelectorAll('[data-payment-method-action]').forEach(link => {
                /**
                 * Buka popup sesuai aksi dan isi formulir edit dari data metode pembayaran.
                 */
                link.addEventListener('click', event => {
                    // Pertahankan navigasi biasa jika dialog tidak didukung atau pengguna membuka tab lain.
                    if (!dialog.showModal || event.ctrlKey || event.metaKey || event.shiftKey || event.altKey) return;
                    event.preventDefault();
                    form.reset();
                    form.action = link.dataset.paymentMethodAction;
                    const editing = link.hasAttribute('data-payment-method-name');
                    form.elements._method.value = editing ? 'PUT' : 'POST';
                    nameInput.value = link.dataset.paymentMethodName || '';
                    document.getElementById('payment-method-title').textContent = editing
                        ? 'Edit Metode Pembayaran' : 'Tambah Metode Pembayaran';
                    error.hidden = true;
                    error.textContent = '';
                    nameInput.removeAttribute('aria-invalid');
                    dialog.showModal();
                });
            });

            /**
             * Tutup popup saat pengguna membatalkan pengisian formulir.
             */
            cancel.addEventListener('click', () => dialog.close());

            /**
             * Cegah tombol Escape menutup popup selama permintaan simpan masih berlangsung.
             */
            dialog.addEventListener('cancel', event => {
                if (submit.disabled) event.preventDefault();
            });

            /**
             * Kirim formulir sebagai permintaan JSON dan tampilkan error tanpa menutup popup.
             */
            form.addEventListener('submit', async event => {
                event.preventDefault();
                // Nonaktifkan tombol selama penyimpanan untuk mencegah pengiriman ganda.
                if (submit.disabled) return;
                submit.disabled = cancel.disabled = true;
                submit.textContent = 'Menyimpan…';
                error.hidden = true;
                nameInput.removeAttribute('aria-invalid');

                try {
                    // FormData menyertakan token CSRF dan _method untuk membedakan tambah dari edit.
                    const response = await fetch(form.action, {
                        method: 'POST',
                        headers: { 'Accept': 'application/json' },
                        body: new FormData(form),
                    });
                    const data = await response.json();
                    if (response.ok) {
                        // Muat daftar agar data terbaru dan pesan sukses dari sesi terlihat.
                        window.location.assign(data.redirect);
                        return;
                    }
                    if (response.status === 422 && data.errors?.name) {
                        error.textContent = data.errors.name[0];
                        nameInput.setAttribute('aria-invalid', 'true');
                    } else {
                        error.textContent = 'Tidak dapat menyimpan. Muat ulang halaman, lalu coba lagi.';
                    }
                } catch {
                    error.textContent = 'Tidak dapat menyimpan. Periksa koneksi Anda, lalu coba lagi.';
                }

                // Izinkan perbaikan input dan percobaan ulang setelah penyimpanan gagal.
                submit.disabled = cancel.disabled = false;
                submit.textContent = 'Simpan';
                error.hidden = false;
                nameInput.focus();
            });
        }
    </script>
@endsection
