@php
    // Satu sumber kelas untuk item menu: aktif berwarna merah, selain itu teks terang di atas hitam
    $navItem = fn (bool $active) => 'h-12 px-4 rounded-3xl flex items-center gap-3.5 text-[15px] transition '
        . ($active ? 'bg-primary text-white font-bold' : 'text-zinc-200 font-semibold hover:bg-ink-900 hover:text-white');
    $navIcon = fn (bool $active) => $active ? '#FFFFFF' : '#A1A1AA';
@endphp

<!-- Menu utama -->
<div class="flex flex-col gap-1">
    <a href="{{ route('dashboard') }}" class="{{ $navItem(request()->routeIs('dashboard')) }}"
        @if (request()->routeIs('dashboard')) aria-current="page" @endif>
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="{{ $navIcon(request()->routeIs('dashboard')) }}"
            stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M3 11l9-8 9 8"></path>
            <path d="M5 10v10h5v-6h4v6h5V10"></path>
        </svg>
        Dashboard
    </a>
    <a href="{{ route('subscriptions.index') }}" class="{{ $navItem(request()->routeIs('subscriptions.*')) }}"
        @if (request()->routeIs('subscriptions.*')) aria-current="page" @endif>
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
            stroke="{{ $navIcon(request()->routeIs('subscriptions.*')) }}" stroke-width="2" stroke-linecap="round"
            stroke-linejoin="round" aria-hidden="true">
            <rect x="3" y="5" width="18" height="15" rx="3"></rect>
            <path d="M3 10h18M8 3v4M16 3v4"></path>
        </svg>
        Langganan
    </a>
</div>

<!-- Data referensi -->
<p class="mt-6 mb-1.5 ml-4 text-[13px] font-semibold text-[#8A8A92]">Atur</p>
<div class="flex flex-col gap-1">
    <a href="{{ route('categories.index') }}" class="{{ $navItem(request()->routeIs('categories.*')) }}"
        @if (request()->routeIs('categories.*')) aria-current="page" @endif>
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
            stroke="{{ $navIcon(request()->routeIs('categories.*')) }}" stroke-width="2" stroke-linecap="round"
            stroke-linejoin="round" aria-hidden="true">
            <path d="M3 12V4h8l10 10-8 8z"></path>
            <circle cx="7.5" cy="8.5" r="1.3"></circle>
        </svg>
        Kategori
    </a>
    <a href="{{ route('payment-methods.index') }}" class="{{ $navItem(request()->routeIs('payment-methods.*')) }}"
        @if (request()->routeIs('payment-methods.*')) aria-current="page" @endif>
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
            stroke="{{ $navIcon(request()->routeIs('payment-methods.*')) }}" stroke-width="2" stroke-linecap="round"
            stroke-linejoin="round" aria-hidden="true">
            <rect x="2.5" y="5" width="19" height="14" rx="3"></rect>
            <path d="M2.5 10h19M6 15h4"></path>
        </svg>
        Metode bayar
    </a>
</div>

<!-- Aktivitas akun -->
<p class="mt-6 mb-1.5 ml-4 text-[13px] font-semibold text-[#8A8A92]">Aktivitas</p>
<div class="flex flex-col gap-1">
    <a href="{{ route('reminders.index') }}" class="{{ $navItem(request()->routeIs('reminders.*')) }}"
        @if (request()->routeIs('reminders.*')) aria-current="page" @endif>
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
            stroke="{{ $navIcon(request()->routeIs('reminders.*')) }}" stroke-width="2" stroke-linecap="round"
            stroke-linejoin="round" aria-hidden="true">
            <circle cx="12" cy="13" r="8"></circle>
            <path d="M12 9v4l2.5 2M9 2.5h6"></path>
        </svg>
        Pengingat
    </a>
    <a href="{{ route('notifications.index') }}" class="{{ $navItem(request()->routeIs('notifications.*')) }}"
        @if (request()->routeIs('notifications.*')) aria-current="page" @endif>
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
            stroke="{{ $navIcon(request()->routeIs('notifications.*')) }}" stroke-width="2" stroke-linecap="round"
            stroke-linejoin="round" aria-hidden="true">
            <path d="M6 8a6 6 0 0 1 12 0c0 7 3 8 3 8H3s3-1 3-8"></path>
            <path d="M10.3 21a1.9 1.9 0 0 0 3.4 0"></path>
        </svg>
        <span class="flex-1">Notifikasi</span>
        @auth
            {{-- $unreadNotificationsCount disiapkan sekali di layouts/app --}}
            @if (($unreadNotificationsCount ?? 0) > 0)
                <span
                    class="inline-flex items-center justify-center min-w-[1.25rem] h-5 px-1.5 text-[11px] font-bold leading-none rounded-full {{ request()->routeIs('notifications.*') ? 'bg-white text-primary' : 'bg-primary text-white' }}">
                    {{ $unreadNotificationsCount > 99 ? '99+' : $unreadNotificationsCount }}
                </span>
            @endif
        @endauth
    </a>
    <a href="{{ route('activity-logs.index') }}" class="{{ $navItem(request()->routeIs('activity-logs.*')) }}"
        @if (request()->routeIs('activity-logs.*')) aria-current="page" @endif>
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
            stroke="{{ $navIcon(request()->routeIs('activity-logs.*')) }}" stroke-width="2" stroke-linecap="round"
            stroke-linejoin="round" aria-hidden="true">
            <path d="M4 12a8 8 0 1 0 2.5-5.8L4 8.5"></path>
            <path d="M4 3.5v5h5M12 8v4.5l3 1.5"></path>
        </svg>
        Riwayat aktivitas
    </a>
</div>
