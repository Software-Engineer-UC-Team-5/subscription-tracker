<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'action',
        'entity',
        'entity_id',
        'description',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Helper 1 baris untuk mencatat log aktivitas pengguna tanpa ribet.
     */
    public static function record(int $userId, string $action, string $entity, ?int $entityId = null, ?string $description = null): self
    {
        return self::create([
            'user_id' => $userId,
            'action' => strtoupper($action),
            'entity' => $entity,
            'entity_id' => $entityId,
            'description' => $description,
            'created_at' => now(),
        ]);
    }
}
