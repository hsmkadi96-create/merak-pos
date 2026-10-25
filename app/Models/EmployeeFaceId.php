<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeFaceId extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id', 'employee_id', 'face_template', 'face_image_path',
        'is_active', 'last_clock_in', 'last_clock_out',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_clock_in' => 'datetime',
        'last_clock_out' => 'datetime',
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
