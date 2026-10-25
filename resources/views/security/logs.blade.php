@extends('layouts.master')

@section('content')
<div class="hk-page-header">
    <h1 class="hk-page-title">Logs de Sécurité</h1>
    <p class="hk-page-subtitle">Journal des événements et erreurs système</p>
</div>

<div class="hk-content-card">
    <div class="hk-card-body">
        <div class="hk-table-wrap">
            <table class="hk-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Message</th>
                        <th>Utilisateur</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($logs as $log)
                    <tr>
                        <td>{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                        <td>{{ $log->type ?? 'error' }}</td>
                        <td>{{ $log->message ?? '-' }}</td>
                        <td>{{ $log->user_id ?? 'Système' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{ $logs->links() }}
    </div>
</div>
@endsection