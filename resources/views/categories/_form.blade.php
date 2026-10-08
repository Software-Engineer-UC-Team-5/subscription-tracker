<!-- Gunakan isian yang sama pada popup serta halaman tambah dan edit -->
@php
    $name = old('name', $category?->name ?? '');
    $icon = old('icon', $category?->icon ?? 'tag');
    $color = old('color', $category?->color ?? '#2563eb');
@endphp

<div>
    <label for="category-name" class="block text-sm font-medium text-slate-700 mb-2">Nama kategori</label>
    <input type="text" id="category-name" name="name" required maxlength="100" autofocus
        value="{{ is_string($name) ? $name : '' }}" placeholder="Contoh: Hiburan"
        aria-describedby="category-name-error" @error('name') aria-invalid="true" @enderror
        class="w-full px-3 py-2 text-sm rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
    <p id="category-name-error" role="alert" data-category-error @unless($errors->has('name')) hidden @endunless
        class="mt-2 text-sm text-rose-700">@error('name') {{ $message }} @enderror</p>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
    <div>
        <label for="category-icon" class="block text-sm font-medium text-slate-700 mb-2">Ikon</label>
        <select id="category-icon" name="icon" aria-describedby="category-icon-error"
            @error('icon') aria-invalid="true" @enderror
            class="w-full px-3 py-2 text-sm rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            @foreach (\App\Models\Category::ICONS as $value => $label)
                <option value="{{ $value }}" @selected($icon === $value)>{{ $label }}</option>
            @endforeach
        </select>
        <p id="category-icon-error" role="alert" data-category-error @unless($errors->has('icon')) hidden @endunless
            class="mt-2 text-sm text-rose-700">@error('icon') {{ $message }} @enderror</p>
    </div>
    <div>
        <label for="category-color" class="block text-sm font-medium text-slate-700 mb-2">Warna label</label>
        <input type="color" id="category-color" name="color"
            value="{{ is_string($color) && preg_match('/^#[0-9a-fA-F]{6}$/', $color) ? $color : '#2563eb' }}"
            aria-describedby="category-color-error" @error('color') aria-invalid="true" @enderror
            class="w-full h-10 p-1 rounded-lg border border-slate-300 bg-white cursor-pointer focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
        <p id="category-color-error" role="alert" data-category-error @unless($errors->has('color')) hidden @endunless
            class="mt-2 text-sm text-rose-700">@error('color') {{ $message }} @enderror</p>
    </div>
</div>
