@extends('layouts.master')

@section('content')
<div class="hk-page-header">
    <h1 class="hk-page-title">Patients</h1>
    <p class="hk-page-subtitle">Gestion des dossiers patients de la clinique</p>
</div>

@if(session('success'))
<div style="background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.3); color: var(--hk-success); padding: 12px 16px; border-radius: 10px; margin-bottom: 24px; font-size: 14px;">
    {{ session('success') }}
</div>
@endif

<div class="hk-content-card">
    <div class="hk-card-header">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <h2 class="hk-card-title">Liste des Patients</h2>
            <button onclick="openAddPatientModal()" class="hk-btn hk-btn-primary">+ Nouveau Patient</button>
        </div>
    </div>

    <div class="hk-card-body">
        <form method="GET" class="hk-form-group" style="margin-bottom: 24px; display: flex; gap: 12px; flex-wrap: wrap;">
            <input type="text" name="search" placeholder="Rechercher un patient..." value="{{ request('search') }}" class="hk-form-input" style="flex: 1; min-width: 250px;">
            <button type="submit" class="hk-btn hk-btn-primary">Rechercher</button>
        </form>

        <div class="hk-table-wrap">
            <table class="hk-table">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Téléphone</th>
                        <th>Email</th>
                        <th>Date Naissance</th>
                        <th>Genre</th>
                        <th>Assurance Chifaa</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($patients as $patient)
                    <tr>
                        <td>{{ $patient->full_name }}</td>
                        <td>{{ $patient->phone ?? '-' }}</td>
                        <td>{{ $patient->email ?? '-' }}</td>
                        <td>{{ $patient->date_of_birth ? $patient->date_of_birth->format('d/m/Y') : '-' }}</td>
                        <td>{{ $patient->gender ?? '-' }}</td>
                        <td>{{ $patient->insurance_number ?? '-' }}</td>
                        <td class="text-right">
                            <a href="{{ route('clinic.medical-record', $patient) }}" class="hk-btn hk-btn-ghost hk-btn-sm">Dossier</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{ $patients->links() }}
    </div>
</div>

<!-- Modal Ajouter Patient -->
<div id="addPatientModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.7); backdrop-filter: blur(8px); z-index: 1000; align-items: center; justify-content: center;">
    <div class="hk-content-card" style="max-width: 500px; width: 90%;">
        <div class="hk-card-header">
            <h2 class="hk-card-title">Nouveau Patient</h2>
            <button onclick="closeAddPatientModal()" style="background: none; border: none; color: var(--hk-text-muted); font-size: 24px; cursor: pointer;">&times;</button>
        </div>
        <div class="hk-card-body">
            <form method="POST" action="{{ route('clinic.patients.store') }}">
                @csrf
                <div class="hk-form-group">
                    <label class="hk-form-label">Prénom</label>
                    <input type="text" name="first_name" required class="hk-form-input">
                </div>
                <div class="hk-form-group">
                    <label class="hk-form-label">Nom</label>
                    <input type="text" name="last_name" required class="hk-form-input">
                </div>
                <div class="hk-form-group">
                    <label class="hk-form-label">Téléphone</label>
                    <input type="text" name="phone" class="hk-form-input">
                </div>
                <div class="hk-form-group">
                    <label class="hk-form-label">Email</label>
                    <input type="email" name="email" class="hk-form-input">
                </div>
                <div class="hk-form-group">
                    <label class="hk-form-label">Date de Naissance</label>
                    <input type="date" name="date_of_birth" class="hk-form-input">
                </div>
                <div class="hk-form-group">
                    <label class="hk-form-label">Genre</label>
                    <select name="gender" class="hk-form-input">
                        <option value="M">Masculin</option>
                        <option value="F">Féminin</option>
                        <option value="Other">Autre</option>
                    </select>
                </div>
                <div class="hk-form-group">
                    <label class="hk-form-label">Numéro Assurance / Carte Chifaa</label>
                    <input type="text" name="insurance_number" class="hk-form-input" placeholder="Numéro carte Chifaa">
                </div>
                <div style="display: flex; gap: 12px; justify-content: flex-end; margin-top: 24px;">
                    <button type="button" onclick="closeAddPatientModal()" class="hk-btn hk-btn-ghost">Annuler</button>
                    <button type="submit" class="hk-btn hk-btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openAddPatientModal() { document.getElementById('addPatientModal').style.display = 'flex'; }
    function closeAddPatientModal() { document.getElementById('addPatientModal').style.display = 'none'; }
</script>

<style>
    .text-right { text-align: right; }
</style>
@endsection