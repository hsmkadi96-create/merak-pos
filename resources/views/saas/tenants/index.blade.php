@extends('layouts.master')

@section('content')
<div class="hk-page-header">
    <h1 class="hk-page-title">Gestion des Clients SAAS</h1>
    <p class="hk-page-subtitle">Créez et gérez les entreprises clientes de MERAK POS</p>
</div>

<div class="hk-grid">
    <!-- Stat Cards -->
    <div class="hk-stat-card">
        <div class="hk-stat-header">
            <span class="hk-stat-label">Clients totaux</span>
            <div class="hk-stat-icon">🏢</div>
        </div>
        <div class="hk-stat-value">{{ $tenants->total() }}</div>
        <div class="hk-stat-change up">
            <svg class="hk-icon-up" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>
            <span>Actifs cette semaine</span>
        </div>
    </div>

    <div class="hk-stat-card">
        <div class="hk-stat-header">
            <span class="hk-stat-label">Plan Gratuit</span>
            <div class="hk-stat-icon">🆓</div>
        </div>
        <div class="hk-stat-value">{{ $tenants->where('plan', 'free')->count() }}</div>
        <div class="hk-stat-change down">
            <span>Aucun changement</span>
        </div>
    </div>

    <div class="hk-stat-card">
        <div class="hk-stat-header">
            <span class="hk-stat-label">Plan Pro</span>
            <div class="hk-stat-icon">💎</div>
        </div>
        <div class="hk-stat-value">{{ $tenants->where('plan', 'pro')->count() }}</div>
        <div class="hk-stat-change up">
            <span>Nouveaux abonnements</span>
        </div>
    </div>

    <div class="hk-stat-card">
        <div class="hk-stat-header">
            <span class="hk-stat-label">Plan Enterprise</span>
            <div class="hk-stat-icon">🚀</div>
        </div>
        <div class="hk-stat-value">{{ $tenants->where('plan', 'enterprise')->count() }}</div>
        <div class="hk-stat-change up">
            <span>Clients premium</span>
        </div>
    </div>
</div>

<div class="hk-content-card">
    <div class="hk-card-header">
        <div>
            <h2 class="hk-card-title">Liste des Tenants</h2>
        </div>
        <a href="{{ route('saas.tenants.create') }}" class="hk-btn hk-btn-primary">
            + Nouveau Client
        </a>
    </div>

    <div class="hk-card-body">
        <form method="GET" class="hk-form-group" style="margin-bottom: 24px; display: flex; gap: 12px; flex-wrap: wrap;">
            <input type="text" name="search" placeholder="Rechercher un client..." value="{{ request('search') }}" class="hk-form-input" style="flex: 1; min-width: 250px;">
            <select name="plan" class="hk-form-input" style="width: 180px;">
                <option value="">Tous les plans</option>
                <option value="free" {{ request('plan') === 'free' ? 'selected' : '' }}>Gratuit</option>
                <option value="pro" {{ request('plan') === 'pro' ? 'selected' : '' }}>Pro</option>
                <option value="enterprise" {{ request('plan') === 'enterprise' ? 'selected' : '' }}>Enterprise</option>
            </select>
            <select name="status" class="hk-form-input" style="width: 180px;">
                <option value="">Tous les statuts</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Actifs</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactifs</option>
            </select>
            <button type="submit" class="hk-btn hk-btn-primary">Filtrer</button>
        </form>

        <div class="hk-table-wrap">
            <table class="hk-table">
                <thead>
                    <tr>
                        <th>Client</th>
                        <th>Email</th>
                        <th>Sous-domaine</th>
                        <th>Plan</th>
                        <th>Statut</th>
                        <th>Créé le</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($tenants as $tenant)
                    <tr>
                        <td>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <div style="width: 36px; height: 36px; border-radius: 10px; background: linear-gradient(135deg, var(--hk-primary), var(--hk-accent)); display: flex; align-items: center; justify-content: center; color: #fff; font-weight: 700; font-size: 14px;">
                                    {{ str($tenant->name)->substr(0, 1) }}
                                </div>
                                <div>
                                    <div style="font-weight: 600;">{{ $tenant->name }}</div>
                                    <div style="font-size: 12px; color: var(--hk-text-muted);">{{ $tenant->domain ?? $tenant->subdomain . '.merakpos.dz' }}</div>
                                </div>
                            </div>
                        </td>
                        <td>{{ $tenant->email }}</td>
                        <td><code style="color: var(--hk-primary);">{{ $tenant->subdomain }}</code></td>
                        <td>
                            <span class="hk-badge {{ $tenant->plan === 'free' ? 'hk-badge-info' : ($tenant->plan === 'pro' ? 'hk-badge-success' : 'hk-badge-warning') }}">
                                {{ ucfirst($tenant->plan) }}
                            </span>
                        </td>
                        <td>
                            <span class="hk-badge {{ $tenant->is_active ? 'hk-badge-success' : 'hk-badge-danger' }}">
                                {{ $tenant->is_active ? 'Actif' : 'Inactif' }}
                            </span>
                        </td>
                        <td>{{ $tenant->created_at->format('d/m/Y') }}</td>
                        <td class="text-right">
                            <a href="{{ route('saas.tenants.show', $tenant) }}" class="hk-btn hk-btn-ghost hk-btn-sm" style="padding: 6px 12px;">Voir</a>
                            <a href="{{ route('saas.tenants.edit', $tenant) }}" class="hk-btn hk-btn-ghost hk-btn-sm" style="padding: 6px 12px;">Modifier</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{ $tenants->links() }}
    </div>
</div>

<style>
    .hk-icon-up { width: 14px; height: 14px; }
    .text-right { text-align: right; }
</style>
@endsection