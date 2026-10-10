<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Subscription Tracker')</title>
    <!-- Font Schibsted Grotesk sesuai desain -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Schibsted+Grotesk:wght@400;500;600;700&display=swap">
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Schibsted Grotesk"', 'system-ui', 'sans-serif'],
                    },
                    colors: {
                        // Merah utama desain; halaman lama yang memakai bg-primary ikut berganti warna
                        primary: {
                            DEFAULT: '#D93A40',
                            hover: '#B92E34',
                            light: '#FBEAEB',
                            dark: '#B92E34',
                        },
                        // Palet hitam desain
                        ink: {
                            DEFAULT: '#0D0D0F',
                            900: '#17171A',
                            800: '#26262B',
                        },
                        // Warna rose 500 dipakai badge notifikasi (dicek test), diarahkan ke merah desain
                        rose: {
                            500: '#D93A40',
                        },
                    }
                }
            }
        }
    </script>
</head>

<body class="bg-[#F4F4F5] text-[#0D0D0F] min-h-screen font-sans antialiased">
    @auth
        @php
            // Inisial dari dua kata pertama nama, contoh "Dessica Hartono" menjadi "DH"
            $userInitials = collect(preg_split('/\s+/', trim(auth()->user()->name)))
                ->filter()
                ->take(2)
                ->map(fn ($word) => mb_strtoupper(mb_substr($word, 0, 1)))
                ->join('');
            $unreadNotificationsCount = auth()->user()->unreadNotificationsCount();
        @endphp
    @endauth

    <!-- Latar gelap saat menu dibuka di layar kecil -->
    <div id="mobile-backdrop" onclick="toggleMobileMenu()"
        class="fixed inset-0 bg-black/60 z-40 transition-opacity duration-300 opacity-0 pointer-events-none lg:hidden">
    </div>

    <!-- Sidebar: tetap di kiri pada desktop, menjadi drawer pada layar kecil -->
    <aside id="mobile-sidebar" aria-label="Menu utama"
        class="fixed inset-y-0 left-0 z-50 w-[260px] bg-ink border-r border-ink-800 flex flex-col px-4 pt-7 pb-5 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out">
        <div class="flex items-center justify-between gap-2 px-2">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 min-w-0">
                <span class="w-10 h-10 rounded-[13px] bg-primary flex items-center justify-center flex-none">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#FFFFFF" stroke-width="2.2"
                        stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M20 12a8 8 0 1 1-2.6-5.9"></path>
                        <path d="M20 4v4.5h-4.5"></path>
                        <circle cx="12" cy="12" r="1.6" fill="#FFFFFF"></circle>
                    </svg>
                </span>
                <span class="text-[17px] font-bold text-white tracking-tight truncate">Subscription Tracker</span>
            </a>
            <!-- Tombol tutup drawer (layar kecil saja) -->
            <button onclick="toggleMobileMenu()" type="button" aria-label="Tutup Menu Navigasi"
                class="lg:hidden w-10 h-10 rounded-full flex items-center justify-center text-zinc-400 hover:text-white hover:bg-ink-900 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Menu navigasi -->
        <nav class="flex-1 overflow-y-auto mt-7">
            @include('layouts.partials.nav_links')
        </nav>

        <!-- Profil pengguna dan tombol keluar -->
        <div class="pt-6">
            @include('layouts.partials.user_profile')
        </div>
    </aside>

    <!-- Area konten utama di kanan sidebar -->
    <div class="lg:pl-[260px] min-h-screen flex flex-col">
        <!--
            Header gelap. Halaman dapat mengisi hero judul dengan salah satu cara:
            1. @section('page_title'), opsional @section('page_subtitle') dan @section('page_actions')
            2. @section('header') untuk hero kustom (misalnya ringkasan di dashboard)
            Jika tidak diisi, header hanya menampilkan bilah atas.
        -->
        @php
            $hasHero = View::hasSection('header') || View::hasSection('page_title');
        @endphp
        <header class="bg-ink px-4 sm:px-8 lg:px-12 pt-7 {{ $hasHero ? 'pb-[104px]' : 'pb-7' }} flex flex-col">
            <!-- Bilah atas: nama halaman, notifikasi, dan avatar -->
            <div class="flex items-center justify-between gap-4">
                <div class="flex items-center gap-3 min-w-0">
                    <button onclick="toggleMobileMenu()" type="button" aria-label="Buka Menu Navigasi"
                        class="lg:hidden w-11 h-11 rounded-full border border-ink-800 bg-ink-900 flex items-center justify-center text-white flex-none">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    <span class="text-[15px] font-semibold text-zinc-400 truncate">@yield('title', 'Dashboard')</span>
                </div>

                <div class="flex items-center gap-2.5 flex-none">
                    <!-- Tombol notifikasi cepat dengan badge jumlah belum dibaca -->
                    <a href="{{ route('notifications.index') }}" title="Lihat Notifikasi" aria-label="Notifikasi"
                        class="relative w-11 h-11 rounded-full border bg-ink-900 flex items-center justify-center hover:bg-ink-800 transition {{ request()->routeIs('notifications.*') ? 'border-primary' : 'border-ink-800' }}">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#FFFFFF"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M6 8a6 6 0 0 1 12 0c0 7 3 8 3 8H3s3-1 3-8"></path>
                            <path d="M10.3 21a1.9 1.9 0 0 0 3.4 0"></path>
                        </svg>

                        @auth
                            @if ($unreadNotificationsCount > 0)
                                <span
                                    class="absolute -top-1 -right-1 inline-flex items-center justify-center min-w-[1.25rem] h-5 px-1 text-[11px] font-bold leading-none text-white bg-rose-500 rounded-full ring-2 ring-ink">
                                    {{ $unreadNotificationsCount > 99 ? '99+' : $unreadNotificationsCount }}
                                </span>
                            @endif
                        @endauth
                    </a>

                    @auth
                        <!-- Avatar pengguna -->
                        <span title="{{ auth()->user()->name }}"
                            class="w-11 h-11 rounded-full bg-primary text-white text-[15px] font-bold flex items-center justify-center">
                            {{ $userInitials }}
                        </span>
                    @endauth
                </div>
            </div>

            <!-- Hero judul halaman -->
            @hasSection('header')
                <div class="mt-8">
                    @yield('header')
                </div>
            @elseif (View::hasSection('page_title'))
                <div class="mt-8 flex flex-wrap items-end justify-between gap-6">
                    <div class="min-w-0">
                        <h1 class="m-0 text-4xl sm:text-5xl sm:leading-[56px] font-bold text-white tracking-[-0.7px] break-words">
                            @yield('page_title')
                        </h1>
                        @hasSection('page_subtitle')
                            <p class="mt-1.5 text-base text-zinc-400">@yield('page_subtitle')</p>
                        @endif
                    </div>
                    @hasSection('page_actions')
                        <div class="flex flex-wrap items-center gap-3">
                            @yield('page_actions')
                        </div>
                    @endif
                </div>
            @endif
        </header>

        <!-- Kartu putih konten; naik menimpa header jika halaman memakai hero -->
        <main
            class="flex-1 flex flex-col min-w-0 {{ $hasHero ? '-mt-16 mx-4 sm:mx-8 lg:mx-10 mb-10 bg-white rounded-[32px] p-6 sm:p-8' : 'p-4 sm:p-8 lg:p-10' }}">
            <!-- Pesan sukses -->
            @if (session('success'))
                <div role="status"
                    class="mb-6 px-5 py-4 rounded-2xl bg-ink text-white text-sm font-semibold flex items-center gap-3">
                    <span class="w-7 h-7 rounded-full bg-primary flex items-center justify-center flex-none">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                        </svg>
                    </span>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <!-- Pesan error -->
            @if (session('error'))
                <div role="alert"
                    class="mb-6 px-5 py-4 rounded-2xl bg-[#FEF3F2] border border-[#FECDCA] text-[#B42318] text-sm font-semibold flex items-center gap-3">
                    <svg class="w-5 h-5 flex-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v4m0 4h.01M10.3 3.9L1.8 18a2 2 0 001.7 3h17a2 2 0 001.7-3L13.7 3.9a2 2 0 00-3.4 0z" />
                    </svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <!-- Isi halaman -->
            @yield('content')
        </main>
    </div>

    <!-- Pengendali drawer navigasi pada layar kecil -->
    <script>
        function toggleMobileMenu() {
            const sidebar = document.getElementById('mobile-sidebar');
            const backdrop = document.getElementById('mobile-backdrop');
            if (!sidebar || !backdrop) return;

            // toggle() mengembalikan false saat class dilepas, artinya drawer sekarang terbuka
            const isOpen = !sidebar.classList.toggle('-translate-x-full');
            backdrop.classList.toggle('opacity-0', !isOpen);
            backdrop.classList.toggle('pointer-events-none', !isOpen);
        }
    </script>
</body>

</html>
