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

<body class="bg-slate-50 text-slate-800 min-h-screen flex flex-col font-sans antialiased">
    <!-- Navbar -->
    <nav class="bg-white border-b border-slate-200 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Brand / Logo -->
                <div class="flex items-center gap-8">
                    <a href="{{ route('dashboard') }}"
                        class="text-lg font-bold text-primary hover:text-primary-hover flex items-center gap-2">
                        <span>Subscription Tracker</span>
                    </a>

                    @auth
                        <!-- Main Menu Items -->
                        <div class="hidden md:flex items-center space-x-1">
                            <a href="{{ route('dashboard') }}"
                                class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('dashboard') ? 'bg-primary-light text-primary-dark font-semibold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                Dashboard
                            </a>
                            <a href="{{ route('subscriptions.index') }}"
                                class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('subscriptions.*') ? 'bg-primary-light text-primary-dark font-semibold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                Subscriptions
                            </a>
                            <a href="{{ route('categories.index') }}"
                                class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('categories.*') ? 'bg-primary-light text-primary-dark font-semibold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                Kategori
                            </a>
                            <a href="{{ route('payment-methods.index') }}"
                                class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('payment-methods.*') ? 'bg-primary-light text-primary-dark font-semibold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                Metode Bayar
                            </a>
                            <a href="{{ route('reminders.index') }}"
                                class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('reminders.*') ? 'bg-primary-light text-primary-dark font-semibold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                Pengingat
                            </a>
                            <a href="{{ route('notifications.index') }}"
                                class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('notifications.*') ? 'bg-primary-light text-primary-dark font-semibold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                Notifikasi
                            </a>
                            <a href="{{ route('activity-logs.index') }}"
                                class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('activity-logs.*') ? 'bg-primary-light text-primary-dark font-semibold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                Log
                            </a>
                        </div>
                    @endauth
                </div>

                <!-- Right Side Actions -->
                <div class="flex items-center space-x-3">
                    @auth
                        <span class="text-sm text-slate-500 hidden sm:inline-block">Halo, {{ auth()->user()->name }}</span>
                        <form action="{{ route('logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit"
                                class="px-3 py-1.5 text-xs font-semibold text-rose-600 hover:text-white border border-rose-300 hover:bg-rose-600 rounded-lg transition">
                                Logout
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}"
                            class="px-4 py-2 text-sm font-medium text-slate-700 hover:text-slate-900 hover:bg-slate-100 rounded-lg transition">
                            Login
                        </a>
                        <a href="{{ route('register') }}"
                            class="px-4 py-2 text-sm font-medium text-white bg-primary hover:bg-primary-hover rounded-lg shadow-sm transition">
                            Daftar
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Container -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex-1 w-full">
        <!-- Flash Notifications -->
        @if (session('success'))
            <div
                class="{{ auth()->check() ? 'w-full' : 'max-w-md mx-auto' }} mb-6 p-4 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-medium flex items-center justify-between">
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div
                class="{{ auth()->check() ? 'w-full' : 'max-w-md mx-auto' }} mb-6 p-4 rounded-lg bg-rose-50 border border-rose-200 text-rose-800 text-sm font-medium flex items-center justify-between">
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @auth
            <!-- Card Container untuk Halaman Internal -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 sm:p-8">
                @yield('content')
            </div>
        @else
            <!-- Card Mandiri untuk Halaman Tamu (Login / Register) -->
            @yield('content')
        @endauth
    </main>

    <!-- Footer -->
    <footer class="py-6 text-center text-xs text-slate-400 mt-auto border-t border-slate-200 bg-white">
        <p>&copy; 2026 Subscription Tracker</p>
    </footer>
</body>

</html>