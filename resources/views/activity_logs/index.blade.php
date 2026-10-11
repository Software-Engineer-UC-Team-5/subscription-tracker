@extends('layouts.app')

@section('title', 'Riwayat aktivitas')

@section('page_title', 'Riwayat aktivitas')
@section('page_subtitle', 'Semua perubahan di akun ini, dari yang terbaru.')

@section('content')
    @php
        // Label cadangan untuk kode aksi yang disimpan backend; nilai di database tidak berubah
        $actionLabels = [
            'CREATE' => 'Dibuat',
            'UPDATE' => 'Diubah',
            'DELETE' => 'Dihapus',
            'LOGIN' => 'Masuk ke akun',
            'LOGOUT' => 'Keluar dari akun',
            'REGISTER' => 'Mendaftar akun baru',
        ];

        // Label jenis entitas; kalau tidak ada di daftar, nama asli dari backend dipakai
        $entityLabels = [
            'Category' => 'Kategori',
            'Subscription' => 'Langganan',
            'PaymentMethod' => 'Metode bayar',
            'Reminder' => 'Pengingat',
            'User' => 'Akun',
        ];
    @endphp

    @if ($logs->isEmpty())
        <!-- Tampilan kosong -->
        <div class="flex-1 flex flex-col items-center justify-center text-center py-10">
            <div class="w-[84px] h-[84px] rounded-full bg-[#F4F4F5] flex items-center justify-center">
                <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#0D0D0F" stroke-width="1.8"
                    stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M4 12a8 8 0 1 0 2.5-5.8L4 8.5"></path>
                    <path d="M4 3.5v5h5M12 8v4.5l3 1.5"></path>
                </svg>
            </div>
            <h2 class="mt-5 text-2xl leading-8 font-bold text-ink tracking-[-0.2px]">Belum ada aktivitas</h2>
            <p class="mt-1.5 max-w-[400px] text-base leading-6 text-[#6B6B73]">
                Setiap penambahan, perubahan, dan login akan dicatat otomatis di sini.
            </p>
        </div>
    @else
        <!-- Dikelompokkan per tanggal (hanya untuk halaman yang sedang tampil) -->
        @php
            $groups = $logs->getCollection()->groupBy(function ($log) {
                return $log->created_at ? $log->created_at->locale('id')->translatedFormat('d M Y') : 'Tanpa tanggal';
            });
        @endphp

        @foreach ($groups as $tanggal => $items)
            <div class="{{ $loop->first ? '' : 'mt-8' }}">
                <h2 class="text-[22px] leading-[30px] font-bold text-ink tracking-[-0.2px]">{{ $tanggal }}</h2>

                <ul class="mt-3 divide-y divide-[#EEEEF0]">
                    @foreach ($items as $log)
                        <li class="py-4 flex gap-4">
                            <!-- Kolom jam di kiri; lebar tetap agar deskripsi tetap sejajar walau jam kosong -->
                            <div class="w-12 shrink-0 text-[13px] leading-[22px] text-[#6B6B73] tabular-nums">
                                @if ($log->created_at)
                                    <time datetime="{{ $log->created_at->toIso8601String() }}">{{ $log->created_at->locale('id')->translatedFormat('H.i') }}</time>
                                @endif
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-[15px] leading-[22px] font-semibold text-ink break-words">
                                    {{ $log->description ?? ($actionLabels[$log->action] ?? ucfirst(strtolower($log->action))) }}
                                </p>
                                <p class="mt-0.5 text-[13px] leading-[18px] text-[#6B6B73]">
                                    {{ $entityLabels[$log->entity] ?? ($log->entity ?? 'Aktivitas') }}
                                </p>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endforeach

        @if ($logs->hasPages())
            <div class="mt-6">
                {{ $logs->links() }}
            </div>
        @endif
    @endif
@endsection
