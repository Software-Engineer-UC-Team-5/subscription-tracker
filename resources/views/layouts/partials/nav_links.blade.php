<!-- Bagian Menu Utama -->
<div>
    <p class="px-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">
        Menu Utama
    </p>
    <div class="space-y-1">
        <!-- Menu Dashboard -->
        <a href="{{ route('dashboard') }}"
            class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium transition {{ request()->routeIs('dashboard') ? 'bg-primary text-white shadow-sm font-semibold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
            <span>Dashboard</span>
        </a>

        <!-- Menu Subscriptions -->
        <a href="{{ route('subscriptions.index') }}"
            class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium transition {{ request()->routeIs('subscriptions.*') ? 'bg-primary text-white shadow-sm font-semibold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
            </svg>
            <span>Subscriptions</span>
        </a>
    </div>
</div>

<!-- Bagian Referensi Master Data -->
<div>
    <p class="px-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">
        Referensi
    </p>
    <div class="space-y-1">
        <!-- Menu Kategori -->
        <a href="{{ route('categories.index') }}"
            class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium transition {{ request()->routeIs('categories.*') ? 'bg-primary text-white shadow-sm font-semibold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
            </svg>
            <span>Kategori</span>
        </a>

        <!-- Menu Metode Bayar -->
        <a href="{{ route('payment-methods.index') }}"
            class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium transition {{ request()->routeIs('payment-methods.*') ? 'bg-primary text-white shadow-sm font-semibold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
            </svg>
            <span>Metode Bayar</span>
        </a>
    </div>
</div>

<!-- Bagian Sistem & Notifikasi -->
<div>
    <p class="px-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">
        Sistem & Audit
    </p>
    <div class="space-y-1">
        <!-- Menu Pengingat -->
        <a href="{{ route('reminders.index') }}"
            class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium transition {{ request()->routeIs('reminders.*') ? 'bg-primary text-white shadow-sm font-semibold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>Pengingat</span>
        </a>

        <!-- Menu Notifikasi -->
        @auth
            @php
                $unreadNavCount = auth()->user()->unreadNotificationsCount();
            @endphp
        @endauth
        <a href="{{ route('notifications.index') }}"
            class="flex items-center justify-between px-3 py-2 rounded-xl text-sm font-medium transition {{ request()->routeIs('notifications.*') ? 'bg-primary text-white shadow-sm font-semibold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
                <span>Notifikasi</span>
            </div>
            @auth
                @if (($unreadNavCount ?? 0) > 0)
                    <span
                        class="inline-flex items-center justify-center px-1.5 py-0.5 text-[10px] font-bold leading-none rounded-full {{ request()->routeIs('notifications.*') ? 'bg-white text-primary' : 'bg-rose-500 text-white' }}">
                        {{ $unreadNavCount > 99 ? '99+' : $unreadNavCount }}
                    </span>
                @endif
            @endauth
        </a>

        <!-- Menu Log Aktivitas -->
        <a href="{{ route('activity-logs.index') }}"
            class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium transition {{ request()->routeIs('activity-logs.*') ? 'bg-primary text-white shadow-sm font-semibold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <span>Log Aktivitas</span>
        </a>
    </div>
</div>