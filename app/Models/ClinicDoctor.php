<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClinicDoctor extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id', 'user_id', 'name', 'specialty', 'license_number',
        'phone', 'email', 'bio', 'is_active',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(ClinicAppointment::class);
    }

    public function medicalRecords(): HasMany
    {
        return $this->hasMany(ClinicMedicalRecord::class);
    }
}
