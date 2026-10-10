@extends('layouts.app')

@section('title', 'Kategori')

@section('page_title', 'Kategori')
@section('page_subtitle', 'Kelompokkan langganan untuk melihat ke mana uangmu paling banyak pergi.')

@section('page_actions')
    <a href="{{ route('categories.create', [], false) }}"
        data-category-action="{{ route('categories.store', [], false) }}"
        aria-haspopup="dialog" aria-controls="category-dialog"
        class="h-14 px-7 rounded-full bg-primary hover:bg-primary-hover text-white text-base font-bold inline-flex items-center gap-2 transition">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6"
            stroke-linecap="round" aria-hidden="true">
            <path d="M12 5v14M5 12h14"></path>
        </svg>
        Tambah kategori
    </a>
@endsection

@section('content')
    <!-- Pesan penolakan penghapusan jika kategori masih digunakan -->
    @error('category')
        <p role="alert"
            class="mb-6 px-5 py-4 rounded-2xl bg-[#FEF3F2] border border-[#FECDCA] text-[#B42318] text-sm font-semibold">
            {{ $message }}
        </p>
    @enderror

    @if ($categories->isEmpty())
        @php
            // Saran kategori hanya mengisi popup tambah; data tetap disimpan lewat formulir biasa
            $suggestions = [
                'Hiburan' => 'tv',
                'Musik' => 'music',
                'Produktivitas' => 'briefcase',
                'Penyimpanan cloud' => 'cloud',
                'Belajar' => 'academic-cap',
                'Kebugaran' => 'tag',
            ];
        @endphp
        <!-- Tampilan kosong dengan saran kategori -->
        <div class="flex-1 flex flex-col items-center justify-center text-center py-10">
            <div class="w-[84px] h-[84px] rounded-full bg-[#F4F4F5] flex items-center justify-center">
                <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#0D0D0F" stroke-width="1.8"
                    stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M3 12V4h8l10 10-8 8z"></path>
                    <circle cx="7.5" cy="8.5" r="1.3"></circle>
                </svg>
            </div>
            <h2 class="mt-5 text-2xl leading-8 font-bold text-ink tracking-[-0.2px]">Belum ada kategori.</h2>
            <p class="mt-1.5 max-w-[440px] text-base leading-6 text-[#6B6B73]">
                Mulai dengan yang umum, atau buat sendiri sesuai kebutuhanmu.
            </p>
            <div class="mt-7 max-w-[560px] flex flex-wrap justify-center gap-2.5">
                @foreach ($suggestions as $suggestionName => $suggestionIcon)
                    <a href="{{ route('categories.create', [], false) }}"
                        data-category-action="{{ route('categories.store', [], false) }}"
                        data-category-suggest="{{ $suggestionName }}" data-category-icon="{{ $suggestionIcon }}"
                        aria-haspopup="dialog" aria-controls="category-dialog"
                        class="h-12 px-[22px] rounded-3xl border-[1.5px] border-[#E4E4E7] bg-white hover:border-ink text-ink text-[15px] font-semibold inline-flex items-center transition">
                        {{ $suggestionName }}
                    </a>
                @endforeach
            </div>
            <a href="{{ route('categories.create', [], false) }}"
                data-category-action="{{ route('categories.store', [], false) }}"
                aria-haspopup="dialog" aria-controls="category-dialog"
                class="mt-8 h-14 px-8 rounded-full border-[1.5px] border-ink bg-white hover:bg-[#F4F4F5] text-ink text-base font-bold inline-flex items-center transition">
                Buat kategori sendiri
            </a>
        </div>
    @else
        <div class="flex items-center justify-between gap-4">
            <h2 class="text-[22px] leading-[30px] font-bold text-ink tracking-[-0.2px]">Semua kategori</h2>
            <span class="text-[13px] font-semibold text-[#6B6B73]">Total: {{ $categories->count() }}</span>
        </div>

        <!-- Tampilkan kategori milik akun aktif beserta aksi edit dan hapus -->
        <div class="mt-5 overflow-x-auto">
            <table class="w-full min-w-[480px] text-left border-separate border-spacing-0">
                <thead>
                    <tr class="text-[13px] font-bold text-[#52525B]">
                        <th scope="col" class="h-11 px-5 bg-[#F4F4F5] rounded-l-[14px]">Kategori</th>
                        <th scope="col" class="h-11 px-5 bg-[#F4F4F5] rounded-r-[14px] text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($categories as $category)
                        @php
                            $categoryColor = preg_match('/^#[0-9a-fA-F]{6}$/', $category->color ?? '')
                                ? $category->color : '#71717a';
                        @endphp
                        <tr>
                            <td class="px-5 py-3.5 border-b border-[#EEEEF0]">
                                <div class="flex items-center gap-3 min-w-0">
                                    <span class="w-10 h-10 rounded-xl flex items-center justify-center flex-none"
                                        style="background-color: {{ $categoryColor }}1a">
                                        <span class="inline-flex" style="color: {{ $categoryColor }}">
                                            @include('categories._icon', ['icon' => $category->icon])
                                        </span>
                                    </span>
                                    <!-- Badge memakai latar transparan agar teks gelap tetap terbaca pada warna terang -->
                                    <span class="inline-flex items-center gap-2 max-w-full min-h-8 px-3.5 py-1 rounded-2xl border text-sm font-bold text-ink"
                                        style="background-color: {{ $categoryColor }}1a; border-color: {{ $categoryColor }}66">
                                        <span class="w-2 h-2 rounded-full shrink-0" style="background-color: {{ $categoryColor }}"
                                            aria-hidden="true"></span>
                                        <span class="break-words min-w-0">{{ $category->name }}</span>
                                    </span>
                                </div>
                            </td>
                            <td class="px-5 py-3.5 border-b border-[#EEEEF0]">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('categories.edit', $category, false) }}"
                                        data-category-action="{{ route('categories.update', $category, false) }}"
                                        data-category-id="{{ $category->id }}"
                                        data-category-name="{{ $category->name }}"
                                        data-category-icon="{{ $category->icon ?? 'tag' }}"
                                        data-category-color="{{ $categoryColor }}"
                                        aria-haspopup="dialog" aria-controls="category-dialog"
                                        aria-label="Edit kategori {{ $category->name }}"
                                        class="h-9 px-3.5 rounded-xl bg-ink hover:bg-ink-800 text-white text-[13px] font-semibold inline-flex items-center transition">Edit</a>
                                    <!-- Minta konfirmasi sebelum mengirim permintaan hapus dengan token CSRF -->
                                    <form action="{{ route('categories.destroy', $category, false) }}" method="POST"
                                        data-category-delete="{{ $category->name }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            aria-haspopup="dialog" aria-controls="category-delete-dialog"
                                            aria-label="Hapus kategori {{ $category->name }}"
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

    <!-- Popup tambah dan edit mengirim nama, ikon, dan warna melalui formulir biasa -->
    <dialog id="category-dialog" aria-labelledby="category-title"
        class="m-auto w-[calc(100vw-2rem)] max-w-[520px] max-h-[90dvh] overflow-y-auto p-8 rounded-[28px] bg-white shadow-[0_24px_64px_rgba(0,0,0,0.35)] backdrop:bg-[rgba(13,13,15,0.62)]">
        <div class="flex items-center justify-between gap-4">
            <h2 id="category-title" class="text-2xl leading-8 font-bold text-ink tracking-[-0.2px]">
                {{ $editingCategory ? 'Edit Kategori' : 'Tambah Kategori' }}
            </h2>
            <button type="button" id="category-close" aria-label="Tutup"
                class="w-11 h-11 -mr-2 rounded-full flex items-center justify-center hover:bg-[#F4F4F5] transition disabled:opacity-50">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#52525B" stroke-width="2.2"
                    stroke-linecap="round" aria-hidden="true">
                    <path d="M6 6l12 12M18 6L6 18"></path>
                </svg>
            </button>
        </div>
        <form id="category-form"
            action="{{ $editingCategory ? route('categories.update', $editingCategory, false) : route('categories.store', [], false) }}"
            method="POST" class="mt-5">
            @csrf
            <input type="hidden" name="_method" value="{{ $editingCategory ? 'PUT' : 'POST' }}">
            <!-- Simpan pilihan edit agar popup yang sama dapat dibuka kembali setelah validasi gagal -->
            <input type="hidden" name="category_id" value="{{ $editingCategory?->id }}">

            @include('categories._form', ['category' => $editingCategory])

            <div class="mt-7 flex flex-wrap items-center justify-end gap-3">
                <button type="button" id="category-cancel"
                    class="h-[52px] px-7 rounded-full border-[1.5px] border-ink bg-white hover:bg-[#F4F4F5] text-ink text-[15px] font-bold transition disabled:opacity-50">
                    Batal
                </button>
                <button type="submit"
                    class="h-[52px] px-8 rounded-full bg-primary hover:bg-primary-hover text-white text-[15px] font-bold transition disabled:opacity-50 disabled:cursor-wait">
                    Simpan
                </button>
            </div>
        </form>
    </dialog>

    <!-- Konfirmasi hapus menampilkan kategori yang dipilih sebelum formulir DELETE dikirim -->
    <dialog id="category-delete-dialog" aria-labelledby="category-delete-title"
        aria-describedby="category-delete-description"
        class="m-auto w-[calc(100vw-2rem)] max-w-[480px] max-h-[90dvh] overflow-y-auto p-8 rounded-[28px] bg-white shadow-[0_24px_64px_rgba(0,0,0,0.35)] backdrop:bg-[rgba(13,13,15,0.62)]">
        <h2 id="category-delete-title" class="text-2xl leading-8 font-bold text-ink tracking-[-0.2px]">Hapus kategori?</h2>
        <p id="category-delete-description" class="mt-2 text-base leading-6 text-[#6B6B73]">
            Kategori <span id="category-delete-name" class="font-bold text-ink break-words"></span>
            akan dihapus.
        </p>
        <form method="dialog" class="mt-7 flex flex-wrap items-center justify-end gap-3">
            <button type="submit" id="category-delete-cancel" autofocus
                class="h-[52px] px-7 rounded-full border-[1.5px] border-ink bg-white hover:bg-[#F4F4F5] text-ink text-[15px] font-bold transition disabled:opacity-50">
                Batal
            </button>
            <button type="button" id="category-delete-confirm"
                class="h-[52px] px-8 rounded-full bg-[#B42318] hover:bg-[#912018] text-white text-[15px] font-bold transition disabled:opacity-50 disabled:cursor-wait">
                Hapus
            </button>
        </form>
    </dialog>

    <script>
        {
            // Ambil elemen popup dan formulir tanpa menambahkan variabel ke lingkup global.
            const dialog = document.getElementById('category-dialog');
            const form = document.getElementById('category-form');
            const nameInput = document.getElementById('category-name');
            const cancel = document.getElementById('category-cancel');
            const close = document.getElementById('category-close');
            const submit = form.querySelector('[type="submit"]');
            const deleteDialog = document.getElementById('category-delete-dialog');
            const deleteName = document.getElementById('category-delete-name');
            const deleteCancel = document.getElementById('category-delete-cancel');
            const deleteConfirm = document.getElementById('category-delete-confirm');
            let deleteForm;

            // Pasang konfirmasi pada setiap formulir hapus di daftar.
            document.querySelectorAll('[data-category-delete]').forEach(targetForm => {
                /**
                 * Tunda penghapusan dan tampilkan nama kategori di popup konfirmasi.
                 */
                targetForm.addEventListener('submit', event => {
                    event.preventDefault();
                    deleteForm = targetForm;
                    // textContent memastikan nama kategori tidak ditafsirkan sebagai HTML.
                    deleteName.textContent = targetForm.dataset.categoryDelete;
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

            // Pasang pembuka popup pada tombol tambah, saran kategori, dan tautan edit di daftar.
            document.querySelectorAll('[data-category-action]').forEach(link => {
                /**
                 * Buka popup sesuai aksi dan isi formulir edit dari data kategori.
                 */
                link.addEventListener('click', event => {
                    // Pertahankan navigasi biasa jika dialog tidak didukung atau pengguna membuka tab lain.
                    if (!dialog.showModal || event.ctrlKey || event.metaKey || event.shiftKey || event.altKey) return;
                    event.preventDefault();
                    form.reset();
                    form.action = link.dataset.categoryAction;
                    const editing = link.hasAttribute('data-category-name');
                    form.elements._method.value = editing ? 'PUT' : 'POST';
                    form.elements.category_id.value = link.dataset.categoryId || '';
                    nameInput.value = link.dataset.categoryName || link.dataset.categorySuggest || '';
                    form.elements.icon.value = link.dataset.categoryIcon || 'tag';
                    form.elements.color.value = link.dataset.categoryColor || '#d93a40';
                    document.getElementById('category-title').textContent = editing
                        ? 'Edit Kategori' : 'Tambah Kategori';
                    // Hapus pesan validasi lama ketika pengguna memilih formulir baru.
                    for (const error of form.querySelectorAll('[data-category-error]')) {
                        error.hidden = true;
                        error.textContent = '';
                    }
                    for (const input of form.querySelectorAll('[aria-invalid]')) {
                        input.removeAttribute('aria-invalid');
                    }
                    // Beri tahu pratinjau di _form bahwa nilai formulir sudah berganti.
                    form.dispatchEvent(new Event('input'));
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
            @if ($errors->hasAny(['name', 'icon', 'color']))
                dialog.showModal();
            @endif
        }
    </script>
@endsection
