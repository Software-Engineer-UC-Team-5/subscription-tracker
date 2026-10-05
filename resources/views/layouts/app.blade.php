<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Subscription Tracker')</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            DEFAULT: '#2563eb', // Biru standar (blue-600)
                            hover: '#1d4ed8',   // blue-700
                            light: '#eff6ff',   // blue-50
                            dark: '#1e40af',    // blue-800
                        }
                    }
                }
            }
        }
    </script>
</head>

<body class="bg-slate-50 text-slate-800 min-h-screen flex font-sans antialiased">
    <!-- Backdrop Overlay untuk Mobile Drawer -->
    <div id="mobile-backdrop" onclick="toggleMobileMenu()"
        class="fixed inset-0 bg-slate-900/50 z-40 transition-opacity duration-300 opacity-0 pointer-events-none lg:hidden">
    </div>

    <!-- Sidebar Mobile Drawer (Off-Canvas) -->
    <aside id="mobile-sidebar"
        class="fixed inset-y-0 left-0 z-50 w-72 bg-white border-r border-slate-200 transform -translate-x-full transition-transform duration-300 ease-in-out lg:hidden flex flex-col">
        <!-- Header Drawer Mobile -->
        <div class="h-16 px-6 border-b border-slate-200 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div
                    class="w-9 h-9 rounded-xl bg-primary text-white flex items-center justify-center font-bold shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                </div>
                <span class="font-bold text-slate-900 tracking-tight text-base">Subscription Tracker</span>
            </div>
            <button onclick="toggleMobileMenu()" type="button"
                class="p-2 text-slate-400 hover:text-slate-600 rounded-lg hover:bg-slate-100 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Menu Navigasi Mobile -->
        <div class="flex-1 overflow-y-auto px-4 py-4 space-y-6">
            @include('layouts.partials.nav_links')
        </div>

        <!-- Profil Pengguna Mobile -->
        <div class="p-4 border-t border-slate-200 bg-slate-50/50">
            @include('layouts.partials.user_profile')
        </div>
    </aside>

    <!-- Sidebar Desktop (Fixed di Kiri) -->
    <aside class="hidden lg:flex lg:flex-col lg:w-64 lg:fixed lg:inset-y-0 bg-white border-r border-slate-200 z-30">
        <!-- Header Sidebar Desktop -->
        <div class="h-16 px-6 border-b border-slate-200 flex items-center justify-between">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group">
                <div
                    class="w-9 h-9 rounded-xl bg-primary text-white flex items-center justify-center font-bold shadow-sm group-hover:scale-105 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                </div>
                <div>
                    <span class="font-bold text-slate-900 tracking-tight text-base block">Subscription Tracker</span>
                    <span class="text-[10px] text-slate-400 font-medium block">Subscription System</span>
                </div>
            </a>
        </div>

        <!-- Menu Navigasi Desktop -->
        <div class="flex-1 overflow-y-auto px-4 py-5 space-y-6">
            @include('layouts.partials.nav_links')
        </div>

        <!-- Profil Pengguna & Logout Desktop -->
        <div class="p-4 border-t border-slate-200 bg-slate-50/50">
            @include('layouts.partials.user_profile')
        </div>
    </aside>

    <!-- Area Konten Utama (Di Kanan Sidebar) -->
    <div class="lg:pl-64 flex flex-col flex-1 min-h-screen w-full">
        <!-- Bilah Header Atas (Top Bar) -->
        <header
            class="sticky top-0 z-20 bg-white/95 backdrop-blur border-b border-slate-200 px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <!-- Tombol Hamburger Mobile -->
                <button onclick="toggleMobileMenu()" type="button"
                    class="p-2 text-slate-500 hover:text-slate-800 hover:bg-slate-100 rounded-lg lg:hidden transition"
                    aria-label="Buka Menu Navigasi">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>

                <!-- Label Halaman / Subtitle -->
                <div class="hidden sm:block">
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Subscription
                        Tracker</span>
                    <span class="text-xs text-slate-300 mx-2">/</span>
                    <span class="text-xs font-medium text-slate-600">@yield('title', 'Dashboard')</span>
                </div>
            </div>

            <!-- Aksi Header Kanan -->
            <div class="flex items-center gap-4">
                @auth
                    @php
                        $unreadNotificationsCount = auth()->user()->unreadNotificationsCount();
                    @endphp
                @endauth

                <!-- Tombol Notifikasi Cepat -->
                <a href="{{ route('notifications.index') }}"
                    class="relative p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg transition"
                    title="Lihat Notifikasi">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>

                    @auth
                        @if ($unreadNotificationsCount > 0)
                            <span class="absolute top-0 right-0 inline-flex items-center justify-center min-w-[1.125rem] h-[1.125rem] px-1 text-[10px] font-bold leading-none text-white bg-rose-500 rounded-full shadow-sm">
                                {{ $unreadNotificationsCount > 99 ? '99+' : $unreadNotificationsCount }}
                            </span>
                        @endif
                    @endauth
                </a>

                @auth
                    <!-- Avatar Pengguna -->
                    <div class="flex items-center gap-3 pl-3 border-l border-slate-200">
                        <div
                            class="w-8 h-8 rounded-full bg-blue-100 text-primary font-bold text-xs flex items-center justify-center">
                            {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                        </div>
                        <div class="hidden md:block text-left">
                            <span
                                class="text-xs font-semibold text-slate-800 block leading-tight">{{ auth()->user()->name }}</span>
                            <span class="text-[10px] text-slate-400 block leading-tight">{{ auth()->user()->email }}</span>
                        </div>
                    </div>
                @endauth
            </div>
        </header>

        <!-- Kanvas Utama Konten (Fluid Full-Width untuk Monitor Besar & Ultrawide) -->
        <main class="flex-1 w-full p-4 sm:p-6 lg:p-8">
            <!-- Flash Notifikasi Sukses -->
            @if (session('success'))
                <div
                    class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-medium flex items-center justify-between shadow-sm">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            <!-- Flash Notifikasi Error -->
            @if (session('error'))
                <div
                    class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm font-medium flex items-center justify-between shadow-sm">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-rose-600 flex-shrink-0" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        <span>{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            <!-- Isi Halaman Dinamis -->
            @yield('content')
        </main>

        <!-- Footer Aplikasi -->
        <footer class="py-5 px-6 text-center text-xs text-slate-400 border-t border-slate-200 bg-white/50 mt-auto">
            <p>&copy; 2026 Subscription Tracker. All right not yet reserved.</p>
        </footer>
    </div>

    <!-- Script Pengendali Drawer Mobile -->
    <script>
        // Fungsi untuk membuka dan menutup drawer navigasi mobile
        function toggleMobileMenu() {
            const sidebar = document.getElementById('mobile-sidebar');
            const backdrop = document.getElementById('mobile-backdrop');
            if (!sidebar || !backdrop) return;

            const isClosed = sidebar.classList.contains('-translate-x-full');
            if (isClosed) {
                sidebar.classList.remove('-translate-x-full');
                backdrop.classList.remove('opacity-0', 'pointer-events-none');
                backdrop.classList.add('opacity-100');
            } else {
                sidebar.classList.add('-translate-x-full');
                backdrop.classList.add('opacity-0', 'pointer-events-none');
                backdrop.classList.remove('opacity-100');
            }
        }
    </script>
</body>

</html>