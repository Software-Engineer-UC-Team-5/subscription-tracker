<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * MODUL: Audit Trail / Log Aktivitas
 * Controller untuk melihat catatan audit trail riwayat aktivitas pengguna (NFR-004).
 */
class ActivityLogController extends Controller
{
    /**
     * Tampilkan riwayat log aktivitas pengguna.
     */
    public function index(Request $request): View
    {
        $logs = ActivityLog::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('activity_logs.index', compact('logs'));
    }
}

