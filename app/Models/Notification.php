<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id', 'user_id', 'type', 'title', 'message', 'url',
        'icon', 'color', 'is_read', 'is_priority', 'read_at', 'data',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'is_priority' => 'boolean',
        'read_at' => 'datetime',
        'data' => 'array',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopePriority($query)
    {
        return $query->where('is_priority', true);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function markAsRead()
    {
        $this->update(['is_read' => true, 'read_at' => now()]);
    }
}
