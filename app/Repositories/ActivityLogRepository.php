<?php

namespace App\Repositories;

use App\Models\ActivityLog;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ActivityLogRepository
{
    /**
     * Ambil riwayat log aktivitas milik user diurutkan dari yang terbaru, dengan paginasi.
     */
    public function getByUser(int $userId, int $perPage = 20): LengthAwarePaginator
    {
        return ActivityLog::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }
}
