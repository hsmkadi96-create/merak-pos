<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClinicPatient extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id', 'first_name', 'last_name', 'phone', 'email',
        'date_of_birth', 'gender', 'address', 'emergency_contact',
        'emergency_phone', 'medical_history', 'insurance_number',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(ClinicAppointment::class);
    }

    public function medicalRecords(): HasMany
    {
        return $this->hasMany(ClinicMedicalRecord::class);
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }
}
