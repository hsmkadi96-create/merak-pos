@extends('layouts.master')

@section('content')
<div class="hk-page-header">
    <h1 class="hk-page-title">Médecins</h1>
    <p class="hk-page-subtitle">Gestion du personnel médical</p>
</div>

@if(session('success'))
<div style="background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.3); color: var(--hk-success); padding: 12px 16px; border-radius: 10px; margin-bottom: 24px; font-size: 14px;">
    {{ session('success') }}
</div>
@endif

<div class="hk-content-card">
    <div class="hk-card-header">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <h2 class="hk-card-title">Liste des Médecins</h2>
            <button onclick="openAddDoctorModal()" class="hk-btn hk-btn-primary">+ Nouveau Médecin</button>
        </div>
    </div>

    <div class="hk-card-body">
        <div class="hk-table-wrap">
            <table class="hk-table">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Spécialité</th>
                        <th>Licence</th>
                        <th>Téléphone</th>
                        <th>Email</th>
                        <th>Statut</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($doctors as $doctor)
                    <tr>
                        <td>{{ $doctor->name }}</td>
                        <td>{{ $doctor->specialty }}</td>
                        <td>{{ $doctor->license_number ?? '-' }}</td>
                        <td>{{ $doctor->phone ?? '-' }}</td>
                        <td>{{ $doctor->email ?? '-' }}</td>
                        <td>
                            <span class="hk-badge {{ $doctor->is_active ? 'hk-badge-success' : 'hk-badge-danger' }}">
                                {{ $doctor->is_active ? 'Actif' : 'Inactif' }}
                            </span>
                        </td>
                        <td class="text-right"></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Ajouter Médecin -->
<div id="addDoctorModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.7); backdrop-filter: blur(8px); z-index: 1000; align-items: center; justify-content: center;">
    <div class="hk-content-card" style="max-width: 500px; width: 90%;">
        <div class="hk-card-header">
            <h2 class="hk-card-title">Nouveau Médecin</h2>
            <button onclick="closeAddDoctorModal()" style="background: none; border: none; color: var(--hk-text-muted); font-size: 24px; cursor: pointer;">&times;</button>
        </div>
        <div class="hk-card-body">
            <form method="POST" action="{{ route('clinic.doctors.store') }}">
                @csrf
                <div class="hk-form-group">
                    <label class="hk-form-label">Nom Complet</label>
                    <input type="text" name="name" required class="hk-form-input">
                </div>
                <div class="hk-form-group">
                    <label class="hk-form-label">Spécialité</label>
                    <input type="text" name="specialty" required class="hk-form-input" placeholder="Ex: Cardiologie, Pédiatrie...">
                </div>
                <div class="hk-form-group">
                    <label class="hk-form-label">Numéro de Licence</label>
                    <input type="text" name="license_number" class="hk-form-input">
                </div>
                <div class="hk-form-group">
                    <label class="hk-form-label">Téléphone</label>
                    <input type="text" name="phone" class="hk-form-input">
                </div>
                <div class="hk-form-group">
                    <label class="hk-form-label">Email</label>
                    <input type="email" name="email" class="hk-form-input">
                </div>
                <div style="display: flex; gap: 12px; justify-content: flex-end; margin-top: 24px;">
                    <button type="button" onclick="closeAddDoctorModal()" class="hk-btn hk-btn-ghost">Annuler</button>
                    <button type="submit" class="hk-btn hk-btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openAddDoctorModal() { document.getElementById('addDoctorModal').style.display = 'flex'; }
    function closeAddDoctorModal() { document.getElementById('addDoctorModal').style.display = 'none'; }
</script>

<style>
    .text-right { text-align: right; }
</style>
@endsection