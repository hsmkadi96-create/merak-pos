<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClinicAppointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id', 'doctor_id', 'patient_id', 'appointment_date',
        'duration', 'status', 'type', 'consultation_fee',
        'symptoms', 'diagnosis', 'prescription', 'notes',
    ];

    protected $casts = [
        'appointment_date' => 'datetime',
        'duration' => 'integer',
        'consultation_fee' => 'decimal:4',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(ClinicDoctor::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(ClinicPatient::class);
    }

    public function medicalRecord(): BelongsTo
    {
        return $this->hasOne(ClinicMedicalRecord::class, 'appointment_id');
    }
}
