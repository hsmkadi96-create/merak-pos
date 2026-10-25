@extends('layouts.master')

@section('content')
<div class="hk-page-header">
    <h1 class="hk-page-title">Gestion Clinique & Cabinet Médical</h1>
    <p class="hk-page-subtitle">Dossiers patients, rendez-vous et médecins</p>
</div>

<div class="hk-stats-grid">
    <div class="hk-stat-card">
        <div class="hk-stat-header">
            <span class="hk-stat-label">Médecins</span>
            <div class="hk-stat-icon">👨‍⚕️</div>
        </div>
        <div class="hk-stat-value">{{ $stats['doctors'] }}</div>
    </div>
    <div class="hk-stat-card">
        <div class="hk-stat-header">
            <span class="hk-stat-label">Patients</span>
            <div class="hk-stat-icon">👤</div>
        </div>
        <div class="hk-stat-value">{{ $stats['patients'] }}</div>
    </div>
    <div class="hk-stat-card">
        <div class="hk-stat-header">
            <span class="hk-stat-label">RDJ Aujourd'hui</span>
            <div class="hk-stat-icon">📅</div>
        </div>
        <div class="hk-stat-value">{{ $stats['appointments_today'] }}</div>
    </div>
    <div class="hk-stat-card">
        <div class="hk-stat-header">
            <span class="hk-stat-label">RDJ en Attente</span>
            <div class="hk-stat-icon">⏳</div>
        </div>
        <div class="hk-stat-value">{{ $stats['pending_appointments'] }}</div>
    </div>
</div>

<div class="hk-grid">
    <div class="hk-content-card" style="grid-column: span 2;">
        <div class="hk-card-header">
            <h2 class="hk-card-title">Rendez-vous Récents</h2>
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
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($recentAppointments as $appt)
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
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="hk-content-card">
        <div class="hk-card-header">
            <h2 class="hk-card-title">Actions Rapides</h2>
        </div>
        <div class="hk-card-body" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
            <div style="background: rgba(56, 189, 248, 0.1); border: 1px solid rgba(56, 189, 248, 0.2); border-radius: 12px; padding: 20px; text-align: center;">
                <div style="font-size: 32px; margin-bottom: 8px;">👤</div>
                <div style="font-weight: 700; color: var(--hk-text);">Nouveau Patient</div>
                <a href="{{ route('clinic.patients') }}" class="hk-btn hk-btn-ghost hk-btn-sm" style="margin-top: 12px;">Voir les patients</a>
            </div>
            <div style="background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.2); border-radius: 12px; padding: 20px; text-align: center;">
                <div style="font-size: 32px; margin-bottom: 8px;">👨‍⚕️</div>
                <div style="font-weight: 700; color: var(--hk-text);">Nouveau Médecin</div>
                <a href="{{ route('clinic.doctors') }}" class="hk-btn hk-btn-ghost hk-btn-sm" style="margin-top: 12px;">Voir les médecins</a>
            </div>
            <div style="background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.2); border-radius: 12px; padding: 20px; text-align: center;">
                <div style="font-size: 32px; margin-bottom: 8px;">📅</div>
                <div style="font-weight: 700; color: var(--hk-text);">Nouveau Rendez-vous</div>
                <a href="{{ route('clinic.appointments') }}" class="hk-btn hk-btn-ghost hk-btn-sm" style="margin-top: 12px;">Voir les RDV</a>
            </div>
        </div>
    </div>
</div>
@endsection