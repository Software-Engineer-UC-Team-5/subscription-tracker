<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    // Pilihan ikon yang dapat digunakan pada formulir dan daftar kategori.
    public const ICONS = [
        'tag' => 'Umum',
        'tv' => 'Hiburan',
        'briefcase' => 'Produktivitas',
        'cloud' => 'Cloud',
        'academic-cap' => 'Pendidikan',
        'wrench' => 'Utilitas',
        'music' => 'Musik',
        'gamepad' => 'Game',
    ];

    protected $fillable = [
        'user_id',
        'name',
        'icon',
        'color',
    ];

    /**
     * Hubungkan kategori kustom dengan pengguna pemiliknya.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Ambil langganan yang menggunakan kategori ini.
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }
}
