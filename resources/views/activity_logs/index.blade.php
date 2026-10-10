@extends('layouts.app')

@section('title', 'Riwayat aktivitas')

@section('page_title', 'Riwayat aktivitas')
@section('page_subtitle', 'Catatan perubahan data dan login di akunmu.')

@section('content')
    @php
        // Label tampilan untuk kode aksi yang disimpan backend; nilai di database tidak berubah
        $actionLabels = [
            'CREATE' => 'Dibuat',
            'UPDATE' => 'Diubah',
            'DELETE' => 'Dihapus',
            'LOGIN' => 'Masuk',
            'LOGOUT' => 'Keluar',
            'REGISTER' => 'Daftar',
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
        <div class="flex items-center justify-between gap-4">
            <h2 class="text-[22px] leading-[30px] font-bold text-ink tracking-[-0.2px]">Semua aktivitas</h2>
            <span class="text-[13px] font-semibold text-[#6B6B73]">Total log: {{ $logs->total() }}</span>
        </div>

        <!-- Tabel log; bisa digeser horizontal di layar kecil -->
        <div class="mt-5 overflow-x-auto">
            <table class="w-full min-w-[720px] text-left border-separate border-spacing-0">
                <thead>
                    <tr class="text-[13px] font-bold text-[#52525B]">
                        <th scope="col" class="h-11 px-5 bg-[#F4F4F5] rounded-l-[14px] w-[19%]">Waktu</th>
                        <th scope="col" class="h-11 px-5 bg-[#F4F4F5] w-[16%]">Jenis</th>
                        <th scope="col" class="h-11 px-5 bg-[#F4F4F5] w-[18%]">Entitas</th>
                        <th scope="col" class="h-11 px-5 bg-[#F4F4F5] rounded-r-[14px]">Rincian</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($logs as $log)
                        <tr class="align-middle">
                            <td class="px-5 py-[18px] border-b border-[#EEEEF0]">
                                @if ($log->created_at)
                                    <span class="block text-[15px] leading-[22px] font-bold text-ink whitespace-nowrap">
                                        {{ $log->created_at->locale('id')->translatedFormat('d M Y, H.i') }}
                                    </span>
                                    <span class="block text-[13px] leading-[18px] text-[#6B6B73]">
                                        {{ $log->created_at->locale('id')->diffForHumans() }}
                                    </span>
                                @else
                                    <span class="text-[15px] text-[#6B6B73]">-</span>
                                @endif
                            </td>
                            <td class="px-5 py-[18px] border-b border-[#EEEEF0]">
                                <span
                                    class="h-8 px-3.5 rounded-2xl bg-[#F4F4F5] text-[#52525B] text-[13px] font-bold inline-flex items-center whitespace-nowrap">
                                    {{ $actionLabels[$log->action] ?? ucfirst(strtolower($log->action)) }}
                                </span>
                            </td>
                            <td class="px-5 py-[18px] border-b border-[#EEEEF0] text-sm text-[#52525B] break-words">
                                {{ $log->entity ?? '-' }}@if ($log->entity_id) #{{ $log->entity_id }}@endif
                            </td>
                            <td class="px-5 py-[18px] border-b border-[#EEEEF0] text-[15px] leading-[22px] text-[#3F3F46] break-words">
                                {{ $log->description ?? '-' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if ($logs->hasPages())
            <div class="mt-6">
                {{ $logs->links() }}
            </div>
        @endif
    @endif
@endsection
