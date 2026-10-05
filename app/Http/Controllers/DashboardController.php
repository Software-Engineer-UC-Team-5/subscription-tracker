<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * MODUL: Dashboard & Analisis Pengeluaran
 */
class DashboardController extends Controller
{
    /**
     * Tampilkan halaman dashboard utama pengguna.
     */
    public function index(Request $request): View
    {
        // TODO: Ambil ringkasan statistik dan upcoming payment via DashboardService
        return view('dashboard.index');
    }
}
