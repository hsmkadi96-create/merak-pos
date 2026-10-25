@extends('layouts.master')

@section('content')
<div class="hk-page-header">
    <h1 class="hk-page-title">Modifier le Client</h1>
    <p class="hk-page-subtitle">{{ $tenant->name }}</p>
</div>

<div class="hk-content-card" style="max-width: 700px; margin: 0 auto;">
    <div class="hk-card-body">
        <form method="POST" action="{{ route('saas.tenants.update', $tenant) }}">
            @csrf
            @method('PUT')

            <div class="hk-form-group">
                <label class="hk-form-label">Nom de l'entreprise</label>
                <input type="text" name="name" value="{{ old('name', $tenant->name) }}" required class="hk-form-input">
            </div>

            <div class="hk-form-group">
                <label class="hk-form-label">Email de contact</label>
                <input type="email" name="email" value="{{ old('email', $tenant->email) }}" required class="hk-form-input">
            </div>

            <div class="hk-form-group">
                <label class="hk-form-label">Sous-domaine</label>
                <input type="text" name="subdomain" value="{{ old('subdomain', $tenant->subdomain) }}" class="hk-form-input">
            </div>

            <div class="hk-form-group">
                <label class="hk-form-label">Plan d'abonnement</label>
                <select name="plan" class="hk-form-input">
                    <option value="free" {{ old('plan', $tenant->plan) === 'free' ? 'selected' : '' }}>Gratuit (Free)</option>
                    <option value="pro" {{ old('plan', $tenant->plan) === 'pro' ? 'selected' : '' }}>Professionnel (Pro)</option>
                    <option value="enterprise" {{ old('plan', $tenant->plan) === 'enterprise' ? 'selected' : '' }}>Entreprise (Enterprise)</option>
                </select>
            </div>

            <div class="hk-form-group">
                <label class="hk-form-label">Statut</label>
                <select name="is_active" class="hk-form-input">
                    <option value="1" {{ old('is_active', $tenant->is_active ? 1 : 0) == 1 ? 'selected' : '' }}>Actif</option>
                    <option value="0" {{ old('is_active', $tenant->is_active ? 1 : 0) == 0 ? 'selected' : '' }}>Inactif</option>
                </select>
            </div>

            <div style="display: flex; gap: 12px; justify-content: flex-end; margin-top: 32px;">
                <a href="{{ route('saas.tenants.index') }}" class="hk-btn hk-btn-ghost">Annuler</a>
                <button type="submit" class="hk-btn hk-btn-primary">Enregistrer</button>
            </div>
        </form>
    </div>
</div>
@endsection