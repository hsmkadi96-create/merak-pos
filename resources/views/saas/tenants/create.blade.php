@extends('layouts.master')

@section('content')
<div class="hk-page-header">
    <h1 class="hk-page-title">Nouveau Client SAAS</h1>
    <p class="hk-page-subtitle">Créez un nouveau compte entreprise pour MERAK POS</p>
</div>

<div class="hk-content-card" style="max-width: 700px; margin: 0 auto;">
    <div class="hk-card-body">
        <form method="POST" action="{{ route('saas.tenants.store') }}">
            @csrf

            <div class="hk-form-group">
                <label class="hk-form-label">Nom de l'entreprise</label>
                <input type="text" name="name" value="{{ old('name') }}" required class="hk-form-input" placeholder="Ex: Pharmacie du Centre">
            </div>

            <div class="hk-form-group">
                <label class="hk-form-label">Email de contact</label>
                <input type="email" name="email" value="{{ old('email') }}" required class="hk-form-input" placeholder="contact@entreprise.dz">
            </div>

            <div class="hk-form-group">
                <label class="hk-form-label">Sous-domaine</label>
                <input type="text" name="subdomain" value="{{ old('subdomain') }}" class="hk-form-input" placeholder="pharmacie-du-centre" style="text-transform: lowercase;">
                <p style="font-size: 12px; color: var(--hk-text-muted); margin-top: 6px;">L'URL sera : sous-domaine.merakpos.dz</p>
            </div>

            <div class="hk-form-group">
                <label class="hk-form-label">Plan d'abonnement</label>
                <select name="plan" class="hk-form-input">
                    <option value="free">Gratuit (Free)</option>
                    <option value="pro">Professionnel (Pro)</option>
                    <option value="enterprise">Entreprise (Enterprise)</option>
                </select>
            </div>

            <div class="hk-form-group">
                <label class="hk-form-label">Période d'essai (jours)</label>
                <input type="number" name="trial_days" value="30" min="1" max="365" class="hk-form-input" placeholder="30">
                <p style="font-size: 12px; color: var(--hk-text-muted); margin-top: 6px;">Laissez vide pour aucun essai</p>
            </div>

            <hr style="border: none; border-top: 1px solid var(--hk-card-border); margin: 24px 0;">

            <h3 style="font-size: 16px; color: var(--hk-text); margin-bottom: 16px;">Compte Administrateur</h3>

            <div class="hk-form-group">
                <label class="hk-form-label">Nom complet de l'admin</label>
                <input type="text" name="admin_name" value="{{ old('admin_name') }}" required class="hk-form-input" placeholder="Ahmed Benali">
            </div>

            <div class="hk-form-group">
                <label class="hk-form-label">Email de l'admin</label>
                <input type="email" name="admin_email" value="{{ old('admin_email') }}" required class="hk-form-input" placeholder="admin@entreprise.dz">
            </div>

            <div class="hk-form-group">
                <label class="hk-form-label">Mot de passe</label>
                <input type="password" name="admin_password" required class="hk-form-input" placeholder="••••••••">
            </div>

            <div class="hk-form-group">
                <label class="hk-form-label">Confirmer le mot de passe</label>
                <input type="password" name="admin_password_confirmation" required class="hk-form-input" placeholder="••••••••">
            </div>

            <div style="display: flex; gap: 12px; justify-content: flex-end; margin-top: 32px;">
                <a href="{{ route('saas.tenants.index') }}" class="hk-btn hk-btn-ghost">Annuler</a>
                <button type="submit" class="hk-btn hk-btn-primary">Créer le Client</button>
            </div>
        </form>
    </div>
</div>
@endsection