@extends('layouts.app')

@section('title', 'Metode bayar')

@section('page_title', 'Metode bayar')
@section('page_subtitle', 'Catat langgananmu dibayar lewat apa, supaya tahu saldo mana yang akan terpotong.')

@section('page_actions')
    <a href="{{ route('payment-methods.create') }}"
        data-payment-method-action="{{ route('payment-methods.store', [], false) }}"
        aria-haspopup="dialog" aria-controls="payment-method-dialog"
        class="h-14 px-7 rounded-full bg-primary hover:bg-primary-hover text-white text-base font-bold inline-flex items-center gap-2 transition">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6"
            stroke-linecap="round" aria-hidden="true">
            <path d="M12 5v14M5 12h14"></path>
        </svg>
        Tambah metode bayar
    </a>
@endsection

@section('content')
    <!-- Pesan penolakan penghapusan jika metode pembayaran masih digunakan -->
    @error('payment_method')
        <p role="alert"
            class="mb-6 px-5 py-4 rounded-2xl bg-[#FEF3F2] border border-[#FECDCA] text-[#B42318] text-sm font-semibold">
            {{ $message }}
        </p>
    @enderror

    @if ($paymentMethods->isEmpty())
        <!-- Tampilan kosong; saran hanya mengisi nama di popup tambah -->
        <div class="flex-1 flex flex-col items-center justify-center text-center py-10">
            <div class="w-[84px] h-[84px] rounded-full bg-[#F4F4F5] flex items-center justify-center">
                <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#0D0D0F" stroke-width="1.8"
                    stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <rect x="2.5" y="5" width="19" height="14" rx="3"></rect>
                    <path d="M2.5 10h19M6 15h4"></path>
                </svg>
            </div>
            <h2 class="mt-5 text-2xl leading-8 font-bold text-ink tracking-[-0.2px]">Belum ada metode pembayaran.</h2>
            <p class="mt-1.5 max-w-[440px] text-base leading-6 text-[#6B6B73]">
                Pilih jenis yang kamu pakai. Cukup namanya saja, nomor kartu tidak pernah diminta.
            </p>
            <div class="mt-7 max-w-[560px] flex flex-wrap justify-center gap-2.5">
                @foreach (['Kartu kredit', 'Kartu debit', 'GoPay', 'OVO', 'DANA', 'Transfer bank'] as $suggestion)
                    <a href="{{ route('payment-methods.create') }}"
                        data-payment-method-action="{{ route('payment-methods.store', [], false) }}"
                        data-payment-method-suggest="{{ $suggestion }}"
                        aria-haspopup="dialog" aria-controls="payment-method-dialog"
                        class="h-12 px-[22px] rounded-3xl border-[1.5px] border-[#E4E4E7] bg-white hover:border-ink text-ink text-[15px] font-semibold inline-flex items-center transition">
                        {{ $suggestion }}
                    </a>
                @endforeach
            </div>
            <a href="{{ route('payment-methods.create') }}"
                data-payment-method-action="{{ route('payment-methods.store', [], false) }}"
                aria-haspopup="dialog" aria-controls="payment-method-dialog"
                class="mt-8 h-14 px-8 rounded-full bg-primary hover:bg-primary-hover text-white text-base font-bold inline-flex items-center transition">
                Tambah metode bayar
            </a>
        </div>
    @else
        <div class="flex items-center justify-between gap-4">
            <h2 class="text-[22px] leading-[30px] font-bold text-ink tracking-[-0.2px]">Semua metode bayar</h2>
            <span class="text-[13px] font-semibold text-[#6B6B73]">Total: {{ $paymentMethods->count() }}</span>
        </div>

        <!-- Daftar metode pembayaran milik pengguna beserta aksi edit dan hapus -->
        <div class="mt-5 overflow-x-auto">
            <table class="w-full min-w-[480px] text-left border-separate border-spacing-0">
                <thead>
                    <tr class="text-[13px] font-bold text-[#52525B]">
                        <th scope="col" class="h-11 px-5 bg-[#F4F4F5] rounded-l-[14px]">Metode pembayaran</th>
                        <th scope="col" class="h-11 px-5 bg-[#F4F4F5] rounded-r-[14px] text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($paymentMethods as $paymentMethod)
                        <tr>
                            <td class="px-5 py-3.5 border-b border-[#EEEEF0]">
                                <div class="flex items-center gap-3 min-w-0">
                                    <span class="w-10 h-10 rounded-xl bg-ink flex items-center justify-center flex-none">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#FFFFFF"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                            <rect x="2.5" y="5" width="19" height="14" rx="3"></rect>
                                            <path d="M2.5 10h19M6 15h4"></path>
                                        </svg>
                                    </span>
                                    <span class="text-base font-bold text-ink break-words min-w-0">{{ $paymentMethod->name }}</span>
                                </div>
                            </td>
                            <td class="px-5 py-3.5 border-b border-[#EEEEF0]">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('payment-methods.edit', $paymentMethod) }}"
                                        data-payment-method-action="{{ route('payment-methods.update', $paymentMethod, false) }}"
                                        data-payment-method-id="{{ $paymentMethod->id }}"
                                        data-payment-method-name="{{ $paymentMethod->name }}"
                                        aria-haspopup="dialog" aria-controls="payment-method-dialog"
                                        aria-label="Edit metode pembayaran {{ $paymentMethod->name }}"
                                        class="h-9 px-3.5 rounded-xl bg-ink hover:bg-ink-800 text-white text-[13px] font-semibold inline-flex items-center transition">Edit</a>
                                    <!-- Minta konfirmasi sebelum mengirim permintaan hapus dengan token CSRF -->
                                    <form action="{{ route('payment-methods.destroy', $paymentMethod) }}" method="POST"
                                        data-payment-method-delete="{{ $paymentMethod->name }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            aria-haspopup="dialog" aria-controls="payment-method-delete-dialog"
                                            aria-label="Hapus metode pembayaran {{ $paymentMethod->name }}"
                                            class="h-9 px-3.5 rounded-xl text-[#B42318] hover:bg-[#FEF3F2] text-[13px] font-semibold transition">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <!-- Popup digunakan bersama untuk tambah dan edit; input hanya meminta nama metode pembayaran -->
    <dialog id="payment-method-dialog" aria-labelledby="payment-method-title"
        class="m-auto w-[calc(100vw-2rem)] max-w-[520px] max-h-[90dvh] overflow-y-auto p-8 rounded-[28px] bg-white shadow-[0_24px_64px_rgba(0,0,0,0.35)] backdrop:bg-[rgba(13,13,15,0.62)]">
        <div class="flex items-center justify-between gap-4">
            <h2 id="payment-method-title" class="text-2xl leading-8 font-bold text-ink tracking-[-0.2px]">
                {{ $editingPaymentMethod ? 'Edit Metode Pembayaran' : 'Tambah Metode Pembayaran' }}
            </h2>
            <button type="button" id="payment-method-close" aria-label="Tutup"
                class="w-11 h-11 -mr-2 rounded-full flex items-center justify-center hover:bg-[#F4F4F5] transition disabled:opacity-50">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#52525B" stroke-width="2.2"
                    stroke-linecap="round" aria-hidden="true">
                    <path d="M6 6l12 12M18 6L6 18"></path>
                </svg>
            </button>
        </div>
        <form id="payment-method-form"
            action="{{ $editingPaymentMethod ? route('payment-methods.update', $editingPaymentMethod) : route('payment-methods.store') }}"
            method="POST" class="mt-5">
            @csrf
            <input type="hidden" name="_method" value="{{ $editingPaymentMethod ? 'PUT' : 'POST' }}">
            <!-- Simpan pilihan edit agar popup yang sama dapat dibuka kembali setelah validasi gagal -->
            <input type="hidden" name="payment_method_id" value="{{ $editingPaymentMethod?->id }}">

            <div class="flex flex-col gap-2">
                <label for="payment-method-name" class="text-sm font-semibold text-ink">
                    Nama metode pembayaran
                </label>
                <input type="text" id="payment-method-name" name="name" required maxlength="100" autofocus
                    value="{{ is_string(old('name')) ? old('name') : '' }}"
                    placeholder="Contoh: BCA Debit" aria-describedby="payment-method-help payment-method-error"
                    @error('name') aria-invalid="true" @enderror
                    class="w-full h-[52px] rounded-2xl px-[18px] text-base text-ink placeholder:text-[#A1A1AA] outline-none transition focus:bg-white focus:border-2 focus:border-primary bg-[#F4F4F5] border-[1.5px] border-[#E4E4E7] aria-[invalid=true]:bg-white aria-[invalid=true]:border-2 aria-[invalid=true]:border-[#B42318]">
                <p id="payment-method-help" class="text-[13px] leading-5 text-[#6B6B73]">
                    Jangan masukkan nomor kartu, CVV, atau PIN.
                </p>
                <p id="payment-method-error" role="alert" @unless($errors->has('name')) hidden @endunless
                    class="text-sm text-[#B42318]">@error('name') {{ $message }} @enderror</p>
            </div>

            <div class="mt-7 flex flex-wrap items-center justify-end gap-3">
                <button type="button" id="payment-method-cancel"
                    class="h-[52px] px-7 rounded-full border-[1.5px] border-ink bg-white hover:bg-[#F4F4F5] text-ink text-[15px] font-bold transition disabled:opacity-50">
                    Batal
                </button>
                <button type="submit"
                    class="h-[52px] px-8 rounded-full bg-primary hover:bg-primary-hover text-white text-[15px] font-bold transition disabled:opacity-50 disabled:cursor-wait">
                    {{ $editingPaymentMethod ? 'Simpan perubahan' : 'Simpan metode' }}
                </button>
            </div>
        </form>
    </dialog>

    <!-- Konfirmasi hapus menampilkan metode yang dipilih sebelum formulir DELETE dikirim -->
    <dialog id="payment-method-delete-dialog" aria-labelledby="payment-method-delete-title"
        aria-describedby="payment-method-delete-description"
        class="m-auto w-[calc(100vw-2rem)] max-w-[480px] max-h-[90dvh] overflow-y-auto p-8 rounded-[28px] bg-white shadow-[0_24px_64px_rgba(0,0,0,0.35)] backdrop:bg-[rgba(13,13,15,0.62)]">
        <h2 id="payment-method-delete-title" class="text-2xl leading-8 font-bold text-ink tracking-[-0.2px]">Hapus metode bayar?</h2>
        <p id="payment-method-delete-description" class="mt-2 text-base leading-6 text-[#6B6B73]">
            Metode pembayaran <span id="payment-method-delete-name" class="font-bold text-ink break-words"></span>
            akan dihapus.
        </p>
        <form method="dialog" class="mt-7 flex flex-wrap items-center justify-end gap-3">
            <button type="submit" id="payment-method-delete-cancel" autofocus
                class="h-[52px] px-7 rounded-full border-[1.5px] border-ink bg-white hover:bg-[#F4F4F5] text-ink text-[15px] font-bold transition disabled:opacity-50">
                Batal
            </button>
            <button type="button" id="payment-method-delete-confirm"
                class="h-[52px] px-8 rounded-full bg-[#B42318] hover:bg-[#912018] text-white text-[15px] font-bold transition disabled:opacity-50 disabled:cursor-wait">
                Hapus
            </button>
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
            const close = document.getElementById('payment-method-close');
            const submit = form.querySelector('[type="submit"]');
            const deleteDialog = document.getElementById('payment-method-delete-dialog');
            const deleteName = document.getElementById('payment-method-delete-name');
            const deleteCancel = document.getElementById('payment-method-delete-cancel');
            const deleteConfirm = document.getElementById('payment-method-delete-confirm');
            let deleteForm;

            // Pasang konfirmasi pada setiap formulir hapus di daftar.
            document.querySelectorAll('[data-payment-method-delete]').forEach(targetForm => {
                /**
                 * Tunda penghapusan dan tampilkan nama metode pembayaran di popup konfirmasi.
                 */
                targetForm.addEventListener('submit', event => {
                    event.preventDefault();
                    deleteForm = targetForm;
                    // textContent memastikan nama metode pembayaran tidak ditafsirkan sebagai HTML.
                    deleteName.textContent = targetForm.dataset.paymentMethodDelete;
                    deleteDialog.showModal();
                });
            });

            /**
             * Kirim formulir DELETE yang dipilih setelah pengguna mengonfirmasi penghapusan.
             */
            deleteConfirm.addEventListener('click', () => {
                if (!deleteForm || deleteConfirm.disabled) return;
                deleteConfirm.disabled = deleteCancel.disabled = true;
                deleteConfirm.textContent = 'Menghapus…';
                // submit() mengirim formulir beserta token CSRF tanpa membuka konfirmasi lagi.
                deleteForm.submit();
            });

            /**
             * Cegah Escape menutup konfirmasi selama permintaan hapus masih berlangsung.
             */
            deleteDialog.addEventListener('cancel', event => {
                if (deleteConfirm.disabled) event.preventDefault();
            });

            // Pasang pembuka popup pada tombol tambah, saran metode, dan tautan edit di daftar.
            document.querySelectorAll('[data-payment-method-action]').forEach(link => {
                /**
                 * Buka popup sesuai aksi dan isi formulir edit dari data metode pembayaran.
                 */
                link.addEventListener('click', event => {
                    // Pertahankan navigasi biasa jika dialog tidak didukung atau pengguna membuka tab lain.
                    if (!dialog.showModal || event.ctrlKey || event.metaKey || event.shiftKey || event.altKey) return;
                    event.preventDefault();
                    form.reset();
                    // Path relatif mengikuti protokol halaman, termasuk HTTPS di belakang proxy.
                    form.action = link.dataset.paymentMethodAction;
                    const editing = link.hasAttribute('data-payment-method-name');
                    form.elements._method.value = editing ? 'PUT' : 'POST';
                    form.elements.payment_method_id.value = link.dataset.paymentMethodId || '';
                    nameInput.value = link.dataset.paymentMethodName || link.dataset.paymentMethodSuggest || '';
                    submit.textContent = editing ? 'Simpan perubahan' : 'Simpan metode';
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
            close.addEventListener('click', () => dialog.close());

            /**
             * Cegah tombol Escape menutup popup selama permintaan simpan masih berlangsung.
             */
            dialog.addEventListener('cancel', event => {
                if (submit.disabled) event.preventDefault();
            });

            /**
             * Cegah pengiriman ganda saat browser mengirim formulir dan memuat ulang halaman.
             */
            form.addEventListener('submit', event => {
                if (submit.disabled) {
                    event.preventDefault();
                    return;
                }
                submit.disabled = cancel.disabled = close.disabled = true;
                submit.textContent = 'Menyimpan…';
            });

            // Laravel mengembalikan error dan isian lewat sesi; buka lagi popup setelah redirect validasi.
            @if ($errors->has('name'))
                dialog.showModal();
            @endif
        }
    </script>
@endsection
