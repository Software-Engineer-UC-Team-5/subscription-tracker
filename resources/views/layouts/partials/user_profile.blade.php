@auth
    <!-- Kartu Profil Pengguna & Aksi Logout -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3 overflow-hidden">
            <div
                class="w-9 h-9 rounded-full bg-primary/10 text-primary font-bold text-xs flex items-center justify-center flex-shrink-0">
                {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
            </div>
            <div class="truncate">
                <span class="text-xs font-semibold text-slate-900 block truncate">{{ auth()->user()->name }}</span>
                <span class="text-[10px] text-slate-400 block truncate">{{ auth()->user()->email }}</span>
            </div>
        </div>

        <form action="{{ route('logout') }}" method="POST" class="inline flex-shrink-0">
            @csrf
            <button type="submit" class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition"
                title="Keluar dari Aplikasi">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
            </button>
        </form>
    </div>
@else
    <div class="flex items-center justify-between gap-2">
        <a href="{{ route('login') }}"
            class="w-full text-center py-2 text-xs font-semibold text-slate-700 hover:bg-slate-100 rounded-lg transition">
            Masuk
        </a>
        <a href="{{ route('register') }}"
            class="w-full text-center py-2 text-xs font-semibold text-white bg-primary hover:bg-primary-hover rounded-lg transition">
            Daftar
        </a>
    </div>
@endauth