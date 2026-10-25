@extends('layouts.master')

@section('content')
<div class="hk-page-header">
    <h1 class="hk-page-title">Logs de Pointage</h1>
    <p class="hk-page-subtitle">Historique des pointages faciaux des employés</p>
</div>

<div class="hk-content-card">
    <div class="hk-card-body">
        <div class="hk-table-wrap">
            <table class="hk-table">
                <thead>
                    <tr>
                        <th>Employé</th>
                        <th>Type</th>
                        <th>Date & Heure</th>
                        <th>Méthode</th>
                        <th>Confiance</th>
                        <th>Appareil</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($logs as $log)
                    <tr>
                        <td>{{ $log->employee->firstname }} {{ $log->employee->lastname }}</td>
                        <td>
                            <span class="hk-badge {{ $log->type === 'clock_in' ? 'hk-badge-success' : 'hk-badge-info' }}">
                                {{ ucfirst($log->type) }}
                            </span>
                        </td>
                        <td>{{ $log->clocked_at->format('d/m/Y H:i:s') }}</td>
                        <td>{{ ucfirst($log->method) }}</td>
                        <td>{{ number_format($log->confidence_score ?? 0, 1) }}%</td>
                        <td>{{ $log->device_info ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{ $logs->links() }}
    </div>
</div>
@endsection