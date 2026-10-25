@extends('layouts.master')

@section('content')
<div class="hk-page-header">
    <h1 class="hk-page-title">Rapport Zakat</h1>
    <p class="hk-page-subtitle">Calcul et suivi de la zakat sur vos stocks et liquidités</p>
</div>

@if(session('success'))
<div style="background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.3); color: var(--hk-success); padding: 12px 16px; border-radius: 10px; margin-bottom: 24px; font-size: 14px;">
    {{ session('success') }}
</div>
@endif

<div class="hk-stats-grid">
    <div class="hk-stat-card">
        <div class="hk-stat-header">
            <span class="hk-stat-label">Seuil Nisab</span>
            <div class="hk-stat-icon">📊</div>
        </div>
        <div class="hk-stat-value">{{ $currentCalculation['nisab_threshold'] ?? 0 }} DZD</div>
    </div>

    <div class="hk-stat-card">
        <div class="hk-stat-header">
            <span class="hk-stat-label">Base Zakat</span>
            <div class="hk-stat-icon">💰</div>
        </div>
        <div class="hk-stat-value">{{ number_format($currentCalculation['zakat_base'] ?? 0, 2) }} DZD</div>
    </div>

    <div class="hk-stat-card {{ $currentCalculation['eligible'] ? '' : '' }}">
        <div class="hk-stat-header">
            <span class="hk-stat-label">Montant Zakat (2.5%)</span>
            <div class="hk-stat-icon">🕌</div>
        </div>
        <div class="hk-stat-value" style="color: {{ $currentCalculation['eligible'] ? 'var(--hk-success)' : 'var(--hk-text-muted)' }};">
            {{ number_format($currentCalculation['zakat_amount'] ?? 0, 2) }} DZD
        </div>
    </div>

    <div class="hk-stat-card">
        <div class="hk-stat-header">
            <span class="hk-stat-label">Éligible</span>
            <div class="hk-stat-icon">{{ $currentCalculation['eligible'] ? '✅' : '❌' }}</div>
        </div>
        <div class="hk-stat-value" style="text-transform: uppercase;">
            {{ $currentCalculation['eligible'] ? 'Oui' : 'Non' }}
        </div>
    </div>
</div>

<div class="hk-grid">
    <div class="hk-content-card" style="grid-column: span 2;">
        <div class="hk-card-header">
            <h2 class="hk-card-title">Nouveau Calcul Zakat</h2>
        </div>
        <div class="hk-card-body">
            <form method="POST" action="{{ route('zakat.store') }}">
                @csrf
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
                    <div class="hk-form-group">
                        <label class="hk-form-label">Période (ex: 2026)</label>
                        <input type="text" name="period" value="{{ now()->year }}" required class="hk-form-input">
                    </div>
                    <div class="hk-form-group">
                        <label class="hk-form-label">Seuil Nisab (DZD)</label>
                        <input type="number" name="nisab_threshold" value="50000" required class="hk-form-input">
                    </div>
                    <div class="hk-form-group">
                        <label class="hk-form-label">Valeur du Stock (DZD)</label>
                        <input type="number" name="total_stock_value" step="0.01" required class="hk-form-input">
                    </div>
                    <div class="hk-form-group">
                        <label class="hk-form-label">Liquidités (DZD)</label>
                        <input type="number" name="total_cash" step="0.01" value="0" class="hk-form-input">
                    </div>
                    <div class="hk-form-group">
                        <label class="hk-form-label">Créances (DZD)</label>
                        <input type="number" name="total_receivables" step="0.01" value="0" class="hk-form-input">
                    </div>
                    <div class="hk-form-group" style="grid-column: 1 / -1;">
                        <label class="hk-form-label">Notes</label>
                        <textarea name="notes" rows="3" class="hk-form-input" placeholder="Notes sur ce calcul de zakat..."></textarea>
                    </div>
                </div>
                <div style="display: flex; gap: 12px; justify-content: flex-end; margin-top: 24px;">
                    <button type="submit" class="hk-btn hk-btn-primary">Calculer et Enregistrer</button>
                </div>
            </form>
        </div>
    </div>

    <div class="hk-content-card" style="grid-column: span 2;">
        <div class="hk-card-header">
            <h2 class="hk-card-title">Historique des Rapports Zakat</h2>
        </div>
        <div class="hk-card-body">
            <div class="hk-table-wrap">
                <table class="hk-table">
                    <thead>
                        <tr>
                            <th>Période</th>
                            <th>Base Zakat</th>
                            <th>Montant Zakat</th>
                            <th>Statut</th>
                            <th>Date</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($reports as $report)
                        <tr>
                            <td>{{ $report->period }}</td>
                            <td>{{ number_format($report->zakat_base, 2) }} DZD</td>
                            <td>{{ number_format($report->zakat_amount, 2) }} DZD</td>
                            <td>
                                <span class="hk-badge {{ $report->is_paid ? 'hk-badge-success' : 'hk-badge-warning' }}">
                                    {{ $report->is_paid ? 'Payé' : 'En attente' }}
                                </span>
                            </td>
                            <td>{{ $report->created_at->format('d/m/Y') }}</td>
                            <td class="text-right">
                                @if(!$report->is_paid)
                                <form method="POST" action="{{ route('zakat.mark-paid', $report) }}" style="display: inline;">
                                    @csrf @method('POST')
                                    <button type="submit" class="hk-btn hk-btn-ghost hk-btn-sm" style="padding: 4px 10px; font-size: 11px;">Marquer Payé</button>
                                </form>
                                @endif
                                <a href="{{ route('zakat.pdf', $report) }}" class="hk-btn hk-btn-ghost hk-btn-sm" style="padding: 4px 10px; font-size: 11px;">PDF</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    .text-right { text-align: right; }
</style>
@endsection