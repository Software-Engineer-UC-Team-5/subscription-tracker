@extends('layouts.app')

@section('title', 'Kategori')

@section('content')
    <!-- Header halaman dan tombol tambah kategori -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-slate-200 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Kategori</h1>
            <p class="text-sm text-slate-600 mt-1">Kelola kategori.</p>
        </div>
        <a href="{{ route('categories.create', [], false) }}"
            data-category-action="{{ route('categories.store', [], false) }}"
            aria-haspopup="dialog" aria-controls="category-dialog"
            class="inline-flex items-center px-4 py-2 bg-primary hover:bg-primary-hover text-white text-sm font-medium rounded-lg shadow-sm transition">
            + Tambah Kategori
        </a>
    </div>

    <!-- Pesan penolakan penghapusan jika kategori masih digunakan -->
    @error('category')
        <p role="alert" class="mb-6 p-4 rounded-lg bg-rose-50 border border-rose-200 text-rose-800 text-sm">
            {{ $message }}
        </p>
    @enderror

    <!-- Tampilkan kategori milik akun aktif beserta aksi edit dan hapus -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        @if ($categories->isEmpty())
            <div class="py-12 px-4 text-center">
                <p class="text-sm font-medium text-slate-700">Belum ada kategori.</p>
                <p class="text-sm text-slate-600 mt-1">Klik Tambah Kategori untuk menambahkan kategori Anda.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 text-xs uppercase text-slate-600 border-b border-slate-200">
                        <tr>
                            <th scope="col" class="px-6 py-3.5">Kategori</th>
                            <th scope="col" class="px-6 py-3.5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($categories as $category)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="px-6 py-4">
                                    @php
                                        $categoryColor = preg_match('/^#[0-9a-fA-F]{6}$/', $category->color ?? '')
                                            ? $category->color : '#2563eb';
                                    @endphp
                                    <div class="flex items-center gap-3">
                                        <span class="inline-flex shrink-0" style="color: {{ $categoryColor }}">
                                            @include('categories._icon', ['icon' => $category->icon])
                                        </span>
                                        <span class="font-medium text-slate-900">
                                            {{ $category->name }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-end gap-4">
                                        <a href="{{ route('categories.edit', $category, false) }}"
                                            data-category-action="{{ route('categories.update', $category, false) }}"
                                            data-category-id="{{ $category->id }}"
                                            data-category-name="{{ $category->name }}"
                                            data-category-icon="{{ $category->icon ?? 'tag' }}"
                                            data-category-color="{{ $categoryColor }}"
                                            aria-haspopup="dialog" aria-controls="category-dialog"
                                            aria-label="Edit kategori {{ $category->name }}"
                                            class="font-medium text-blue-700 hover:underline">Edit</a>
                                        <!-- Minta konfirmasi sebelum mengirim permintaan hapus dengan token CSRF -->
                                        <form action="{{ route('categories.destroy', $category, false) }}" method="POST"
                                            data-category-delete="{{ $category->name }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                aria-haspopup="dialog" aria-controls="category-delete-dialog"
                                                aria-label="Hapus kategori {{ $category->name }}"
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

    <!-- Popup tambah dan edit mengirim nama, ikon, dan warna melalui formulir biasa -->
    <dialog id="category-dialog" aria-labelledby="category-title"
        class="m-auto w-[90vw] max-w-lg max-h-[90dvh] overflow-y-auto p-6 rounded-xl border border-slate-200 bg-white shadow-xl backdrop:bg-slate-900/40">
        <h2 id="category-title" class="text-xl font-bold text-slate-900 mb-5">
            {{ $editingCategory ? 'Edit Kategori' : 'Tambah Kategori' }}
        </h2>
        <form id="category-form"
            action="{{ $editingCategory ? route('categories.update', $editingCategory, false) : route('categories.store', [], false) }}"
            method="POST" class="space-y-5">
            @csrf
            <input type="hidden" name="_method" value="{{ $editingCategory ? 'PUT' : 'POST' }}">
            <!-- Simpan pilihan edit agar popup yang sama dapat dibuka kembali setelah validasi gagal -->
            <input type="hidden" name="category_id" value="{{ $editingCategory?->id }}">

            @include('categories._form', ['category' => $editingCategory])

            <div class="flex items-center justify-end gap-3">
                <button type="button" id="category-cancel"
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

    <!-- Konfirmasi hapus menampilkan kategori yang dipilih sebelum formulir DELETE dikirim -->
    <dialog id="category-delete-dialog" aria-labelledby="category-delete-title"
        aria-describedby="category-delete-description"
        class="m-auto w-[90vw] max-w-lg max-h-[90dvh] overflow-y-auto p-6 rounded-xl border border-slate-200 bg-white shadow-xl backdrop:bg-slate-900/40">
        <h2 id="category-delete-title" class="text-xl font-bold text-slate-900 mb-3">Hapus Kategori?</h2>
        <p id="category-delete-description" class="text-sm text-slate-600 mb-6">
            Kategori <span id="category-delete-name" class="font-medium text-slate-900 break-words"></span>
            akan dihapus.
        </p>
        <form method="dialog" class="flex items-center justify-end gap-3">
            <button type="submit" id="category-delete-cancel" autofocus
                class="px-4 py-2 text-sm font-medium text-slate-700 bg-white hover:bg-slate-50 border border-slate-300 rounded-lg transition disabled:opacity-50">
                Batal
            </button>
            <button type="button" id="category-delete-confirm"
                class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white text-sm font-medium rounded-lg transition disabled:opacity-50 disabled:cursor-wait">
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

            // Pasang pembuka popup pada tautan tambah dan edit di daftar.
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
                    nameInput.value = link.dataset.categoryName || '';
                    form.elements.icon.value = link.dataset.categoryIcon || 'tag';
                    form.elements.color.value = link.dataset.categoryColor || '#2563eb';
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
             * Cegah pengiriman ganda saat browser mengirim formulir dan memuat ulang halaman.
             */
            form.addEventListener('submit', event => {
                if (submit.disabled) {
                    event.preventDefault();
                    return;
                }
                submit.disabled = cancel.disabled = true;
                submit.textContent = 'Menyimpan…';
            });

            // Laravel mengembalikan error dan isian lewat sesi; buka lagi popup setelah redirect validasi.
            @if ($errors->hasAny(['name', 'icon', 'color']))
                dialog.showModal();
            @endif
        }
    </script>
@endsection
