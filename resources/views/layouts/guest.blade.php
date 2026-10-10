<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Masuk') - Subscription Tracker</title>
    <!-- Font Schibsted Grotesk sesuai desain -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Schibsted+Grotesk:wght@400;500;600;700&display=swap">
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        // Sama dengan layouts/app agar warna dan font konsisten di semua halaman
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Schibsted Grotesk"', 'system-ui', 'sans-serif'],
                    },
                    colors: {
                        primary: {
                            DEFAULT: '#D93A40',
                            hover: '#B92E34',
                            light: '#FBEAEB',
                            dark: '#B92E34',
                        },
                        ink: {
                            DEFAULT: '#0D0D0F',
                            900: '#17171A',
                            800: '#26262B',
                        },
                        rose: {
                            500: '#D93A40',
                        },
                    }
                }
            }
        }
    </script>
</head>

<body class="bg-ink text-ink min-h-screen font-sans antialiased">
    <div class="min-h-screen flex flex-col lg:flex-row">
        <!-- Panel kiri: identitas aplikasi dan tagline -->
        <div class="relative overflow-hidden flex flex-col lg:flex-1 min-w-0 px-6 py-8 sm:px-12 lg:px-[72px] lg:py-14">
            <div class="relative flex items-center gap-3.5">
                <span class="w-12 h-12 rounded-2xl bg-primary flex items-center justify-center flex-none">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#FFFFFF" stroke-width="2.2"
                        stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M20 12a8 8 0 1 1-2.6-5.9"></path>
                        <path d="M20 4v4.5h-4.5"></path>
                        <circle cx="12" cy="12" r="1.6" fill="#FFFFFF"></circle>
                    </svg>
                </span>
                <span class="text-xl font-bold text-white tracking-tight">Subscription Tracker</span>
            </div>

            <!-- Tagline disembunyikan di layar kecil agar formulir langsung terlihat -->
            <div class="relative hidden lg:block mt-auto pt-16">
                <p class="max-w-[460px] text-[44px] xl:text-[52px] leading-[58px] font-bold text-white tracking-[-0.8px]">
                    Tahu kapan tagihan datang, sebelum dipotong.
                </p>
                <p class="mt-5 max-w-[420px] text-lg leading-7 text-zinc-400">
                    Catat semua langganan, lihat total per bulan, dan dapat pengingat lewat email.
                </p>
            </div>

            <!-- Ornamen lingkaran dekoratif -->
            <div class="hidden lg:block absolute -right-[120px] top-[120px] w-[360px] h-[360px] rounded-full bg-primary opacity-[0.16]"
                aria-hidden="true"></div>
            <div class="hidden lg:block absolute right-10 top-[300px] w-[120px] h-[120px] rounded-full border-2 border-ink-800"
                aria-hidden="true"></div>
        </div>

        <!-- Panel kanan: formulir autentikasi -->
        <main
            class="flex-1 min-w-0 bg-white rounded-t-[32px] lg:rounded-t-none lg:rounded-l-[40px] px-6 py-10 sm:px-12 lg:px-[72px] lg:py-14 flex flex-col items-center justify-center">
            <div class="w-full max-w-[400px] flex flex-col">
                <!-- Pesan sukses -->
                @if (session('success'))
                    <div role="status"
                        class="mb-6 px-5 py-4 rounded-2xl bg-ink text-white text-sm font-semibold flex items-center gap-3">
                        <span class="w-7 h-7 rounded-full bg-primary flex items-center justify-center flex-none">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                        </span>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                <!-- Pesan error -->
                @if (session('error'))
                    <div role="alert"
                        class="mb-6 px-5 py-4 rounded-2xl bg-[#FEF3F2] border border-[#FECDCA] text-[#B42318] text-sm font-semibold">
                        {{ session('error') }}
                    </div>
                @endif

                @yield('content')
            </div>

            <p class="mt-12 text-xs text-[#6B6B73]">&copy; 2026 Subscription Tracker</p>
        </main>
    </div>
</body>

</html>
