@extends('layouts.master')

@section('content')
<div class="hk-page-header">
    <h1 class="hk-page-title">Dossier Médical</h1>
    <p class="hk-page-subtitle">Historique des soins de {{ $patient->full_name }}</p>
</div>

<div class="hk-content-card">
    <div class="hk-card-header">
        <h2 class="hk-card-title">{{ $patient->full_name }}</h2>
    </div>

    <div class="hk-card-body">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 24px;">
            <div style="background: rgba(56, 189, 248, 0.1); border: 1px solid rgba(56, 189, 248, 0.2); border-radius: 12px; padding: 16px;">
                <div style="font-size: 12px; color: var(--hk-text-muted); text-transform: uppercase;">Téléphone</div>
                <div style="font-weight: 600;">{{ $patient->phone ?? '-' }}</div>
            </div>
            <div style="background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.2); border-radius: 12px; padding: 16px;">
                <div style="font-size: 12px; color: var(--hk-text-muted); text-transform: uppercase;">Assurance Chifaa</div>
                <div style="font-weight: 600;">{{ $patient->insurance_number ?? '-' }}</div>
            </div>
            <div style="background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.2); border-radius: 12px; padding: 16px;">
                <div style="font-size: 12px; color: var(--hk-text-muted); text-transform: uppercase;">Date de Naissance</div>
                <div style="font-weight: 600;">{{ $patient->date_of_birth ? $patient->date_of_birth->format('d/m/Y') : '-' }}</div>
            </div>
        </div>

        <h3 style="font-size: 16px; font-weight: 700; color: var(--hk-text); margin-bottom: 16px;">Historique des Consultations</h3>

        <div class="hk-table-wrap">
            <table class="hk-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Médecin</th>
                        <th>Symptômes</th>
                        <th>Diagnostic</th>
                        <th>Traitement</th>
                        <th>Prescription</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($records as $record)
                    <tr>
                        <td>{{ $record->record_date->format('d/m/Y') }}</td>
                        <td>{{ $record->doctor->name }}</td>
                        <td>{{ $record->symptoms }}</td>
                        <td>{{ $record->diagnosis }}</td>
                        <td>{{ $record->treatment }}</td>
                        <td>{{ $record->prescription ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection