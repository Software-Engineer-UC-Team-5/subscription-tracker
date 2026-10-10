@auth
    <!-- Kartu profil pengguna dan tombol keluar -->
    <div class="pt-4 px-2 border-t border-ink-800 flex items-center gap-3">
        <span
            class="w-11 h-11 rounded-full bg-primary text-white text-[15px] font-bold flex items-center justify-center flex-none">
            {{ $userInitials ?? mb_strtoupper(mb_substr(auth()->user()->name, 0, 2)) }}
        </span>
        <div class="flex-1 min-w-0 flex flex-col">
            <span class="text-[15px] leading-5 font-bold text-white truncate">{{ auth()->user()->name }}</span>
            <span class="text-xs leading-[18px] text-[#8A8A92] truncate">{{ auth()->user()->email }}</span>
        </div>

        <form action="{{ route('logout') }}" method="POST" class="flex-none">
            @csrf
            <button type="submit" aria-label="Keluar" title="Keluar dari Aplikasi"
                class="w-11 h-11 rounded-full flex items-center justify-center text-zinc-400 hover:text-white hover:bg-ink-900 transition">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M9 4H5a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h4M16 8l4 4-4 4M20 12H9"></path>
                </svg>
            </button>
        </form>
    </div>
@else
    <div class="pt-4 border-t border-ink-800 flex items-center gap-2">
        <a href="{{ route('login') }}"
            class="flex-1 h-11 rounded-full flex items-center justify-center text-sm font-semibold text-zinc-200 hover:bg-ink-900 transition">
            Masuk
        </a>
        <a href="{{ route('register') }}"
            class="flex-1 h-11 rounded-full flex items-center justify-center text-sm font-semibold text-white bg-primary hover:bg-primary-hover transition">
            Daftar
        </a>
    </div>
@endauth
