@extends('layouts.app')

@section('title', 'Log Aktivitas Pengguna')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-5 border-b border-slate-200 gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight">Log Aktivitas (Audit Trail)</h1>
                <p class="text-sm text-slate-500 mt-1">Catatan riwayat seluruh mutasi data dan autentikasi akun demi
                    transparansi dan audit keamanan.</p>
            </div>
            <span class="text-xs font-semibold px-3 py-1.5 rounded-full bg-slate-100 text-slate-700">
                Total Log: {{ $logs->total() }}
            </span>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            @if ($logs->isEmpty())
                <div class="py-12 px-4 text-center">
                    <div
                        class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-slate-100 text-slate-400 mb-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <p class="text-sm font-medium text-slate-700">Belum Ada Riwayat Aktivitas</p>
                    <p class="text-xs text-slate-400 mt-1">Setiap aksi penambahan, perubahan, dan login akan dicatat secara
                        otomatis
                        di sini.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-600">
                        <thead class="bg-slate-50 text-xs uppercase font-semibold text-slate-500 border-b border-slate-200">
                            <tr>
                                <th class="px-6 py-3.5">Waktu</th>
                                <th class="px-6 py-3.5">Aksi</th>
                                <th class="px-6 py-3.5">Entitas</th>
                                <th class="px-6 py-3.5">Rincian Aktivitas</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($logs as $log)
                                <tr class="hover:bg-slate-50/75 transition">
                                    <td class="px-6 py-4 text-xs text-slate-500 whitespace-nowrap">
                                        {{ $log->created_at ? $log->created_at->format('d M Y, H:i') : '-' }}
                                        <span class="block text-[10px] text-slate-400">
                                            {{ $log->created_at ? $log->created_at->diffForHumans() : '' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        @php
                                            $actionClass = match ($log->action) {
                                                'CREATE' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                                'UPDATE' => 'bg-blue-50 text-blue-700 border-blue-200',
                                                'DELETE' => 'bg-rose-50 text-rose-700 border-rose-200',
                                                'LOGIN' => 'bg-purple-50 text-purple-700 border-purple-200',
                                                'LOGOUT' => 'bg-slate-100 text-slate-600 border-slate-200',
                                                default => 'bg-slate-100 text-slate-700 border-slate-200',
                                            };
                                        @endphp
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider border {{ $actionClass }}">
                                            {{ $log->action }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 font-medium text-slate-800">
                                        {{ $log->entity ?? '-' }}
                                        @if ($log->entity_id)
                                            <span class="text-xs text-slate-400 font-normal">#{{ $log->entity_id }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-slate-700">
                                        {{ $log->description }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if ($logs->hasPages())
                    <div class="p-4 border-t border-slate-100 bg-slate-50">
                        {{ $logs->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
@endsection