<!-- Gunakan SVG Phosphor lokal dengan gaya regular yang sama untuk seluruh kategori -->
@php
    $icon = is_string($icon) && array_key_exists($icon, \App\Models\Category::ICONS) ? $icon : 'tag';
@endphp
<svg class="w-5 h-5 shrink-0" viewBox="0 0 256 256" fill="currentColor" aria-hidden="true">
    <use href="{{ '/icons/categories.svg#'.$icon }}" />
</svg>
