@extends('layouts.master')

@section('content')
<div class="hk-page-header">
    <h1 class="hk-page-title">Gestion Restaurant</h1>
    <p class="hk-page-subtitle">Plan de salle, tables et commandes en temps réel</p>
</div>

<div class="hk-stats-grid">
    <div class="hk-stat-card">
        <div class="hk-stat-header">
            <span class="hk-stat-label">Tables Totales</span>
            <div class="hk-stat-icon">🍽️</div>
        </div>
        <div class="hk-stat-value">{{ $stats['total_tables'] }}</div>
    </div>
    <div class="hk-stat-card">
        <div class="hk-stat-header">
            <span class="hk-stat-label">Tables Libres</span>
            <div class="hk-stat-icon">✅</div>
        </div>
        <div class="hk-stat-value" style="color: var(--hk-success)">{{ $stats['free_tables'] }}</div>
    </div>
    <div class="hk-stat-card">
        <div class="hk-stat-header">
            <span class="hk-stat-label">Tables Occupées</span>
            <div class="hk-stat-icon">🔴</div>
        </div>
        <div class="hk-stat-value" style="color: var(--hk-danger)">{{ $stats['occupied_tables'] }}</div>
    </div>
    <div class="hk-stat-card">
        <div class="hk-stat-header">
            <span class="hk-stat-label">Commandes Actives</span>
            <div class="hk-stat-icon">📋</div>
        </div>
        <div class="hk-stat-value">{{ $stats['active_orders'] }}</div>
    </div>
</div>

<div class="hk-grid">
    <div class="hk-content-card" style="grid-column: span 2;">
        <div class="hk-card-header">
            <h2 class="hk-card-title">Plan de Salle</h2>
        </div>
        <div class="hk-card-body">
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 16px;">
                @foreach ($tables as $table)
                <div style="
                    background: var(--hk-card);
                    border: 1px solid var(--hk-card-border);
                    border-radius: 16px;
                    padding: 20px;
                    text-align: center;
                    cursor: pointer;
                    transition: var(--hk-transition);
                    @if($table->status === 'occupied')
                        border-color: var(--hk-danger);
                        background: rgba(239, 68, 68, 0.1);
                    @elseif($table->status === 'reserved')
                        border-color: var(--hk-warning);
                        background: rgba(245, 158, 11, 0.1);
                    @elseif($table->status === 'free')
                        border-color: var(--hk-success);
                        background: rgba(16, 185, 129, 0.1);
                    @else
                        border-color: var(--hk-text-muted);
                    @endif
                ">
                    <div style="font-size: 32px; margin-bottom: 8px;">
                        @if($table->status === 'occupied') 🔴
                        @elseif($table->status === 'reserved') 🟠
                        @elseif($table->status === 'free') 🟢
                        @else 🔵
                        @endif
                    </div>
                    <div style="font-weight: 800; color: var(--hk-text); margin-bottom: 4px;">{{ $table->name }}</div>
                    <div style="font-size: 12px; color: var(--hk-text-muted);">{{ $table->capacity }} places</div>
                    <div style="font-size: 11px; text-transform: uppercase; color: var(--hk-text-muted); margin-top: 8px;">
                        {{ ucfirst($table->status) }}
                    </div>
                    @if($table->status === 'free')
                    <button onclick="openOrderModal({{ $table->id }})" class="hk-btn hk-btn-primary" style="margin-top: 12px; width: 100%; padding: 8px; font-size: 13px;">
                        Nouvelle Commande
                    </button>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- Modal Nouvelle Commande -->
<div id="orderModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.7); backdrop-filter: blur(8px); z-index: 1000; align-items: center; justify-content: center;">
    <div class="hk-content-card" style="max-width: 600px; width: 90%; max-height: 90vh; overflow-y: auto;">
        <div class="hk-card-header">
            <h2 class="hk-card-title">Nouvelle Commande</h2>
            <button onclick="closeOrderModal()" style="background: none; border: none; color: var(--hk-text-muted); font-size: 24px; cursor: pointer;">&times;</button>
        </div>
        <div class="hk-card-body">
            <form id="restaurantOrderForm">
                <input type="hidden" name="table_id" id="modalTableId">
                <div class="hk-form-group">
                    <label class="hk-form-label">Serveur</label>
                    <select name="waiter_id" required class="hk-form-input">
                        @foreach ($waiters as $waiter)
                        <option value="{{ $waiter->id }}">{{ $waiter->firstname }} {{ $waiter->lastname }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="hk-form-group">
                    <label class="hk-form-label">Produits</label>
                    <div id="orderItemsContainer">
                        <!-- Items ajoutés dynamiquement -->
                    </div>
                    <button type="button" onclick="addOrderItem()" class="hk-btn hk-btn-ghost" style="padding: 8px 16px;">+ Ajouter un produit</button>
                </div>
                <div style="display: flex; gap: 12px; justify-content: flex-end; margin-top: 24px;">
                    <button type="button" onclick="closeOrderModal()" class="hk-btn hk-btn-ghost">Annuler</button>
                    <button type="submit" class="hk-btn hk-btn-primary">Valider la Commande</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openOrderModal(tableId) {
        document.getElementById('modalTableId').value = tableId;
        document.getElementById('orderModal').style.display = 'flex';
    }
    function closeOrderModal() {
        document.getElementById('orderModal').style.display = 'none';
    }
    function addOrderItem() {
        // À implémenter avec sélection de produits depuis le stock
        alert('Sélection de produits à implémenter');
    }
    document.getElementById('restaurantOrderForm').onsubmit = function(e) {
        e.preventDefault();
        // Soumission AJAX
    };
</script>
@endsection