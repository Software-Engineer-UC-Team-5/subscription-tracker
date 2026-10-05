<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * MODUL: Dashboard & Analisis Pengeluaran
 * Controller untuk dashboard dan analisis pengeluaran pengguna (UC04 / FR-005).
 */
class DashboardController extends Controller
{
    public function __construct(
        protected DashboardService $dashboardService
    ) {
    }

    /**
     * Tampilkan ringkasan subscription aktif, upcoming payment, dan estimasi bulanan/tahunan (FR-005).
     */
    public function index(Request $request): View
    {
        $userId = auth()->id();
        $summary = $this->dashboardService->getSummary($userId);

        return view('dashboard.index', compact('summary'));
    }
}

