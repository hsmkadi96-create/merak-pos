@extends('layouts.master')

@section('content')
<div class="hk-page-header">
    <h1 class="hk-page-title">Audit de Sécurité</h1>
    <p class="hk-page-subtitle">Détection des failles et recommandations de correction</p>
</div>

@if(count($issues) > 0)
<div class="hk-content-card">
    <div class="hk-card-header">
        <h2 class="hk-card-title">Problèmes Détectés ({{ count($issues) }})</h2>
    </div>
    <div class="hk-card-body">
        <div class="hk-table-wrap">
            <table class="hk-table">
                <thead>
                    <tr>
                        <th>Niveau</th>
                        <th>Problème</th>
                        <th>Description</th>
                        <th>Correction</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($issues as $issue)
                    <tr>
                        <td>
                            <span class="hk-badge {{ $issue['level'] === 'critical' ? 'hk-badge-danger' : 'hk-badge-warning' }}">
                                {{ strtoupper($issue['level']) }}
                            </span>
                        </td>
                        <td>{{ $issue['title'] }}</td>
                        <td>{{ $issue['description'] }}</td>
                        <td style="font-size: 12px; color: var(--hk-text-muted);">{{ $issue['fix'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@else
<div class="hk-content-card" style="text-align: center; padding: 60px 24px;">
    <div style="font-size: 48px; margin-bottom: 16px;">✅</div>
    <h2 style="color: var(--hk-success); font-weight: 700;">Aucune faille détectée</h2>
    <p style="color: var(--hk-text-muted);">Votre installation MERAK POS est sécurisée.</p>
</div>
@endif

<div class="hk-content-card" style="margin-top: 24px;">
    <div class="hk-card-header">
        <h2 class="hk-card-title">Erreurs Récentes (7 jours)</h2>
    </div>
    <div class="hk-card-body">
        <p style="font-size: 18px; font-weight: 800; color: var(--hk-text);">{{ $recentErrors }} erreur(s)</p>
        <a href="{{ route('security.logs') }}" class="hk-btn hk-btn-primary">Voir les logs détaillés</a>
    </div>
</div>
@endsection