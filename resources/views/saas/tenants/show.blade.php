@extends('layouts.master')

@section('content')
<div class="hk-page-header">
    <h1 class="hk-page-title">{{ $tenant->name }}</h1>
    <p class="hk-page-subtitle">Détails du client SAAS</p>
</div>

<div class="hk-grid">
    <div class="hk-stat-card">
        <div class="hk-stat-header">
            <span class="hk-stat-label">Email</span>
        </div>
        <div class="hk-stat-value" style="font-size: 18px; word-break: break-all;">{{ $tenant->email }}</div>
    </div>

    <div class="hk-stat-card">
        <div class="hk-stat-header">
            <span class="hk-stat-label">Sous-domaine</span>
        </div>
        <div class="hk-stat-value" style="font-size: 18px;">{{ $tenant->subdomain }}</div>
    </div>

    <div class="hk-stat-card">
        <div class="hk-stat-header">
            <span class="hk-stat-label">Plan</span>
        </div>
        <div class="hk-stat-value">
            <span class="hk-badge {{ $tenant->plan === 'free' ? 'hk-badge-info' : ($tenant->plan === 'pro' ? 'hk-badge-success' : 'hk-badge-warning') }}">
                {{ ucfirst($tenant->plan) }}
            </span>
        </div>
    </div>

    <div class="hk-stat-card">
        <div class="hk-stat-header">
            <span class="hk-stat-label">Statut</span>
        </div>
        <div class="hk-stat-value">
            <span class="hk-badge {{ $tenant->is_active ? 'hk-badge-success' : 'hk-badge-danger' }}">
                {{ $tenant->is_active ? 'Actif' : 'Inactif' }}
            </span>
        </div>
    </div>
</div>

<div class="hk-content-card">
    <div class="hk-card-header">
        <h2 class="hk-card-title">Utilisateurs de ce tenant</h2>
    </div>
    <div class="hk-card-body">
        <table class="hk-table">
            <thead>
                <tr>
                    <th>Utilisateur</th>
                    <th>Email</th>
                    <th>Rôle</th>
                    <th>Propriétaire</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($tenant->users as $tu)
                <tr>
                    <td>{{ $tu->user->firstname }} {{ $tu->user->lastname }}</td>
                    <td>{{ $tu->user->email }}</td>
                    <td>{{ ucfirst($tu->role) }}</td>
                    <td>
                        <span class="hk-badge {{ $tu->is_owner ? 'hk-badge-success' : 'hk-badge-dark' }}">
                            {{ $tu->is_owner ? 'Oui' : 'Non' }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@endsection