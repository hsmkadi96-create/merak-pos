<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClinicMedicalRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id', 'patient_id', 'doctor_id', 'appointment_id',
        'record_date', 'symptoms', 'diagnosis', 'treatment',
        'prescription', 'lab_results', 'notes',
    ];

    protected $casts = [
        'record_date' => 'date',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(ClinicPatient::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(ClinicDoctor::class);
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(ClinicAppointment::class);
    }
}
