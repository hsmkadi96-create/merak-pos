<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FaceAttendanceLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id', 'employee_id', 'clocked_at', 'type',
        'method', 'device_info', 'ip_address', 'confidence_score', 'notes',
    ];

    protected $casts = [
        'clocked_at' => 'datetime',
        'confidence_score' => 'decimal:2',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
