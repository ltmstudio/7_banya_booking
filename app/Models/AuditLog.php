<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use HasFactory;

    protected $table = 'audit_logs';

    // Лог никогда не редактируется — только создаётся
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'entity_type',
        'entity_id',
        'action',
        'field',
        'old_value',
        'new_value',
        'ip',
        'user_agent',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    // ─── Связи ───────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Полиморфная связь — получить саму запись (Booking, Client и т.д.)
    public function entity()
    {
        return $this->morphTo(__FUNCTION__, 'entity_type', 'entity_id');
    }

    // ─── Helpers ─────────────────────────────────────

    // Удобный метод для записи лога из любого места
    public static function write(
        string $entityType,
        int $entityId,
        string $action,
        ?string $field = null,
        mixed $oldValue = null,
        mixed $newValue = null,
    ): void {
        static::create([
            'user_id' => auth()->id(),
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'action' => $action,
            'field' => $field,
            'old_value' => $oldValue,
            'new_value' => $newValue,
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now(),
        ]);
    }

    // ─── Scopes ──────────────────────────────────────

    public function scopeForEntity($query, string $type, int $id)
    {
        return $query->where('entity_type', $type)
            ->where('entity_id', $id);
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }
}
