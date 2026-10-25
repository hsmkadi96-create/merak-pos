@extends('layouts.master')

@section('content')
<div class="hk-page-header">
    <h1 class="hk-page-title">Rendez-vous</h1>
    <p class="hk-page-subtitle">Planning des consultations médicales</p>
</div>

@if(session('success'))
<div style="background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.3); color: var(--hk-success); padding: 12px 16px; border-radius: 10px; margin-bottom: 24px; font-size: 14px;">
    {{ session('success') }}
</div>
@endif

<div class="hk-content-card">
    <div class="hk-card-header">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <h2 class="hk-card-title">Liste des Rendez-vous</h2>
            <button onclick="openAddAppointmentModal()" class="hk-btn hk-btn-primary">+ Nouveau RDV</button>
        </div>
    </div>

    <div class="hk-card-body">
        <div class="hk-table-wrap">
            <table class="hk-table">
                <thead>
                    <tr>
                        <th>Patient</th>
                        <th>Médecin</th>
                        <th>Date & Heure</th>
                        <th>Type</th>
                        <th>Statut</th>
                        <th>Frais</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($appointments as $appt)
                    <tr>
                        <td>{{ $appt->patient->full_name }}</td>
                        <td>{{ $appt->doctor->name }}</td>
                        <td>{{ $appt->appointment_date->format('d/m/Y H:i') }}</td>
                        <td>
                            <span class="hk-badge hk-badge-info">{{ ucfirst($appt->type) }}</span>
                        </td>
                        <td>
                            <span class="hk-badge {{ $appt->status === 'completed' ? 'hk-badge-success' : 'hk-badge-warning' }}">
                                {{ ucfirst($appt->status) }}
                            </span>
                        </td>
                        <td>{{ number_format($appt->consultation_fee ?? 0, 0) }} DZD</td>
                        <td class="text-right"></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{ $appointments->links() }}
    </div>
</div>

<!-- Modal Ajouter RDV -->
<div id="addAppointmentModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.7); backdrop-filter: blur(8px); z-index: 1000; align-items: center; justify-content: center;">
    <div class="hk-content-card" style="max-width: 500px; width: 90%;">
        <div class="hk-card-header">
            <h2 class="hk-card-title">Nouveau Rendez-vous</h2>
            <button onclick="closeAddAppointmentModal()" style="background: none; border: none; color: var(--hk-text-muted); font-size: 24px; cursor: pointer;">&times;</button>
        </div>
        <div class="hk-card-body">
            <form method="POST" action="{{ route('clinic.appointments.store') }}">
                @csrf
                <div class="hk-form-group">
                    <label class="hk-form-label">Médecin</label>
                    <select name="doctor_id" required class="hk-form-input">
                        @foreach ($doctors as $doctor)
                        <option value="{{ $doctor->id }}">{{ $doctor->name }} - {{ $doctor->specialty }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="hk-form-group">
                    <label class="hk-form-label">Patient</label>
                    <select name="patient_id" required class="hk-form-input">
                        @php $patients = \App\Models\ClinicPatient::where('tenant_id', session('current_tenant_id'))->get(); @endphp
                        @foreach ($patients as $patient)
                        <option value="{{ $patient->id }}">{{ $patient->full_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="hk-form-group">
                    <label class="hk-form-label">Date et Heure</label>
                    <input type="datetime-local" name="appointment_date" required class="hk-form-input">
                </div>
                <div class="hk-form-group">
                    <label class="hk-form-label">Durée (min)</label>
                    <input type="number" name="duration" value="30" class="hk-form-input">
                </div>
                <div class="hk-form-group">
                    <label class="hk-form-label">Type</label>
                    <select name="type" class="hk-form-input">
                        <option value="consultation">Consultation</option>
                        <option value="follow_up">Suivi</option>
                        <option value="emergency">Urgence</option>
                        <option value="procedure">Procédure</option>
                    </select>
                </div>
                <div class="hk-form-group">
                    <label class="hk-form-label">Frais de Consultation (DZD)</label>
                    <input type="number" name="consultation_fee" step="0.01" class="hk-form-input">
                </div>
                <div class="hk-form-group">
                    <label class="hk-form-label">Symptômes</label>
                    <textarea name="symptoms" rows="3" class="hk-form-input"></textarea>
                </div>
                <div style="display: flex; gap: 12px; justify-content: flex-end; margin-top: 24px;">
                    <button type="button" onclick="closeAddAppointmentModal()" class="hk-btn hk-btn-ghost">Annuler</button>
                    <button type="submit" class="hk-btn hk-btn-primary">Fixer le RDV</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openAddAppointmentModal() { document.getElementById('addAppointmentModal').style.display = 'flex'; }
    function closeAddAppointmentModal() { document.getElementById('addAppointmentModal').style.display = 'none'; }
</script>

<style>
    .text-right { text-align: right; }
</style>
@endsection