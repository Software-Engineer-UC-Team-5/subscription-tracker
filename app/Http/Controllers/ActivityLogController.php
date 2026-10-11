<?php

namespace App\Http\Controllers;

use App\Repositories\ActivityLogRepository;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * MODUL: Audit Trail / Log Aktivitas
 * Controller untuk melihat catatan audit trail riwayat aktivitas pengguna (NFR-004).
 */
class ActivityLogController extends Controller
{
    public function __construct(
        protected ActivityLogRepository $activityLogRepository
    ) {
    }

    /**
     * Tampilkan riwayat log aktivitas pengguna.
     */
    public function index(Request $request): View
    {
        $logs = $this->activityLogRepository->getByUser(auth()->id());

        return view('activity_logs.index', compact('logs'));
    }
}

