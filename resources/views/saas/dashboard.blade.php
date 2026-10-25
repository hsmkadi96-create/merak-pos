@extends('layouts.master')

@section('content')
<div class="hk-page-header">
    <h1 class="hk-page-title">Tableau de Bord</h1>
    <p class="hk-page-subtitle">Bienvenue sur MERAK POS — {{ $tenant->name }}</p>
</div>

<div class="hk-stats-grid">
    <div class="hk-stat-card">
        <div class="hk-stat-header">
            <span class="hk-stat-label">Produits en Stock</span>
            <div class="hk-stat-icon">📦</div>
        </div>
        <div class="hk-stat-value">{{ $stats['total_products'] }}</div>
    </div>

    <div class="hk-stat-card">
        <div class="hk-stat-header">
            <span class="hk-stat-label">Ventes Aujourd'hui</span>
            <div class="hk-stat-icon">💰</div>
        </div>
        <div class="hk-stat-value">{{ $stats['total_sales'] }}</div>
        <div class="hk-stat-change up">
            <span>+12% vs hier</span>
        </div>
    </div>

    <div class="hk-stat-card">
        <div class="hk-stat-header">
            <span class="hk-stat-label">Clients</span>
            <div class="hk-stat-icon">👥</div>
        </div>
        <div class="hk-stat-value">{{ $stats['total_clients'] }}</div>
    </div>

    <div class="hk-stat-card">
        <div class="hk-stat-header">
            <span class="hk-stat-label">Valeur du Stock</span>
            <div class="hk-stat-icon">💎</div>
        </div>
        <div class="hk-stat-value">{{ number_format($stats['total_stock_value'], 0, ',', ' ') }} DZD</div>
    </div>
</div>

<div class="hk-grid">
    <div class="hk-content-card" style="grid-column: span 2;">
        <div class="hk-card-header">
            <h2 class="hk-card-title">Activité Récente</h2>
        </div>
        <div class="hk-card-body">
            <p style="color: var(--hk-text-muted); text-align: center; padding: 40px 0;">
                Aucune activité récente pour le moment. Commencez par ajouter des produits ou effectuer des ventes.
            </p>
        </div>
    </div>
</div>

@endsection