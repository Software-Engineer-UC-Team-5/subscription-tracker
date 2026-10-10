<!-- Gunakan isian yang sama pada popup serta halaman tambah dan edit -->
@php
    $name = old('name', $category?->name ?? '');
    $icon = old('icon', $category?->icon ?? 'tag');
    $color = old('color', $category?->color ?? '#D93A40');
    $color = is_string($color) && preg_match('/^#[0-9a-fA-F]{6}$/', $color) ? strtolower($color) : '#d93a40';
    $icon = is_string($icon) && array_key_exists($icon, \App\Models\Category::ICONS) ? $icon : 'tag';

    // Pilihan warna cepat dari desain; nilai tetap dikirim lewat satu input name="color"
    $colorPresets = [
        '#d93a40' => 'Merah',
        '#f5a524' => 'Kuning',
        '#d7e84f' => 'Hijau limau',
        '#2fb67c' => 'Hijau',
        '#3b82f6' => 'Biru',
        '#8b5cf6' => 'Ungu',
        '#71717a' => 'Abu-abu',
    ];

    $inputClass = 'w-full h-[52px] rounded-2xl text-base text-ink outline-none transition focus:bg-white focus:border-2 focus:border-primary';
    $inputState = fn (string $field) => $errors->has($field)
        ? 'bg-white border-2 border-[#B42318]'
        : 'bg-[#F4F4F5] border-[1.5px] border-[#E4E4E7]';
@endphp

<div data-category-fields class="flex flex-col">
    <div class="flex flex-col gap-2">
        <label for="category-name" class="text-sm font-semibold text-ink">Nama kategori</label>
        <input type="text" id="category-name" name="name" required maxlength="100" autofocus
            value="{{ is_string($name) ? $name : '' }}" placeholder="Contoh: Hiburan"
            aria-describedby="category-name-error" @error('name') aria-invalid="true" @enderror
            class="{{ $inputClass }} {{ $inputState('name') }} px-[18px] placeholder:text-[#A1A1AA]">
        <p id="category-name-error" role="alert" data-category-error @unless($errors->has('name')) hidden @endunless
            class="text-sm text-[#B42318]">@error('name') {{ $message }} @enderror</p>
    </div>

    <div class="mt-[18px] flex flex-col gap-2">
        <label for="category-icon" class="text-sm font-semibold text-ink">Ikon</label>
        <select id="category-icon" name="icon" aria-describedby="category-icon-error"
            @error('icon') aria-invalid="true" @enderror
            class="{{ $inputClass }} {{ $inputState('icon') }} px-3.5 cursor-pointer">
            @foreach (\App\Models\Category::ICONS as $value => $label)
                <option value="{{ $value }}" @selected($icon === $value)>{{ $label }}</option>
            @endforeach
        </select>
        <p id="category-icon-error" role="alert" data-category-error @unless($errors->has('icon')) hidden @endunless
            class="text-sm text-[#B42318]">@error('icon') {{ $message }} @enderror</p>
    </div>

    <fieldset class="mt-[18px] flex flex-col gap-2">
        <legend class="text-sm font-semibold text-ink mb-2">Warna label</legend>
        <div class="flex flex-wrap gap-2.5">
            @foreach ($colorPresets as $presetColor => $presetLabel)
                <button type="button" data-color-swatch="{{ $presetColor }}" aria-label="{{ $presetLabel }}"
                    aria-pressed="{{ $color === $presetColor ? 'true' : 'false' }}"
                    class="w-11 h-11 rounded-full cursor-pointer transition aria-pressed:border-[3px] aria-pressed:border-ink aria-pressed:shadow-[inset_0_0_0_3px_#fff]"
                    style="background-color: {{ $presetColor }}"></button>
            @endforeach
            <!-- Warna bebas di luar pilihan cepat; ini input yang sebenarnya dikirim ke server -->
            <label for="category-color" title="Warna lain"
                class="relative w-11 h-11 rounded-full border-[1.5px] border-dashed border-[#A1A1AA] flex items-center justify-center cursor-pointer overflow-hidden has-[:focus-visible]:ring-2 has-[:focus-visible]:ring-primary">
                <span class="sr-only">Warna lain</span>
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#52525B" stroke-width="2.4"
                    stroke-linecap="round" aria-hidden="true">
                    <path d="M12 5v14M5 12h14"></path>
                </svg>
                <input type="color" id="category-color" name="color" value="{{ $color }}"
                    aria-describedby="category-color-error" @error('color') aria-invalid="true" @enderror
                    class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
            </label>
        </div>
        <p id="category-color-error" role="alert" data-category-error @unless($errors->has('color')) hidden @endunless
            class="text-sm text-[#B42318]">@error('color') {{ $message }} @enderror</p>
    </fieldset>

    <!-- Pratinjau memakai gaya badge yang sama dengan daftar kategori -->
    <div class="mt-5 px-4 py-3.5 rounded-2xl bg-[#F4F4F5] flex items-center gap-3 min-w-0">
        <span class="text-[13px] font-semibold text-[#6B6B73] flex-none">Pratinjau</span>
        <span data-category-preview
            class="inline-flex items-center gap-2 min-w-0 h-8 px-3.5 rounded-2xl border text-sm font-bold text-ink"
            style="background-color: {{ $color }}1a; border-color: {{ $color }}66">
            <span data-category-preview-icon class="inline-flex" style="color: {{ $color }}">
                @include('categories._icon', ['icon' => $icon])
            </span>
            <span data-category-preview-name class="truncate">{{ is_string($name) && $name !== '' ? $name : 'Nama kategori' }}</span>
        </span>
    </div>
</div>

<script>
    {
        // Hubungkan pilihan warna, input, dan pratinjau di formulir kategori terdekat.
        const fields = document.currentScript.previousElementSibling;
        const form = fields.closest('form');
        const colorInput = fields.querySelector('#category-color');
        const preview = fields.querySelector('[data-category-preview]');
        const previewIcon = fields.querySelector('[data-category-preview-icon]');
        const previewName = fields.querySelector('[data-category-preview-name]');
        const swatches = fields.querySelectorAll('[data-color-swatch]');

        /**
         * Perbarui pratinjau dan tanda warna terpilih dari nilai formulir saat ini.
         */
        const render = () => {
            const color = colorInput.value.toLowerCase();
            const name = form.elements.name.value.trim();
            preview.style.backgroundColor = color + '1a';
            preview.style.borderColor = color + '66';
            previewIcon.style.color = color;
            previewIcon.querySelector('use')?.setAttribute('href', '/icons/categories.svg#' + form.elements.icon.value);
            // textContent menjaga nama tetap teks biasa, bukan HTML.
            previewName.textContent = name || 'Nama kategori';
            swatches.forEach(swatch => {
                swatch.setAttribute('aria-pressed', swatch.dataset.colorSwatch === color ? 'true' : 'false');
            });
        };

        swatches.forEach(swatch => {
            swatch.addEventListener('click', () => {
                colorInput.value = swatch.dataset.colorSwatch;
                render();
            });
        });

        // Popup mengisi ulang nilai lalu mengirim event input agar pratinjau ikut berubah.
        form.addEventListener('input', render);
        form.addEventListener('change', render);
        form.addEventListener('reset', () => setTimeout(render));
    }
</script>
