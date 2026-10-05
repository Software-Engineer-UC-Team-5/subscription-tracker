<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * MODUL: Audit Trail / Log Aktivitas
 */
class ActivityLogController extends Controller
{
    /**
     * Tampilkan riwayat log aktivitas pengguna.
     */
    public function index(Request $request): View
    {
        // TODO: Ambil catatan riwayat aktivitas pengguna yang sedang login
        return view('activity_logs.index');
    }
}
