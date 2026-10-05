<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Masuk') - Subscription Tracker</title>
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

<body
    class="bg-slate-50 text-slate-800 min-h-screen flex flex-col justify-center items-center py-12 px-4 sm:px-6 lg:px-8 font-sans antialiased">
    <!-- Kontainer Formulir Autentikasi -->
    <div class="w-full max-w-md">
        <!-- Logo & Identitas Aplikasi -->
        <div class="text-center mb-8">
            <div
                class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-primary text-white shadow-md mb-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                </svg>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Subscription Tracker</h1>
            <p class="text-sm text-slate-500 mt-1">Platform Manajemen & Pengingat Beban Langganan</p>
        </div>

        <!-- Flash Message -->
        @if (session('success'))
            <div class="mb-4 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-medium">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-4 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm font-medium">
                {{ session('error') }}
            </div>
        @endif

        <!-- Card Form -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 sm:p-8">
            @yield('content')
        </div>

        <!-- Footer Ringkas -->
        <p class="text-center text-xs text-slate-400 mt-8">
            &copy; 2026 Subscription Tracker. Seluruh hak cipta dilindungi.
        </p>
    </div>
</body>

</html>