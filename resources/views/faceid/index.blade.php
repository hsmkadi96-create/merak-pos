@extends('layouts.master')

@section('content')
<div class="hk-page-header">
    <h1 class="hk-page-title">Pointage ID Facial</h1>
    <p class="hk-page-subtitle">Reconnaissance faciale pour le pointage des employés</p>
</div>

<div class="hk-stats-grid">
    <div class="hk-stat-card">
        <div class="hk-stat-header">
            <span class="hk-stat-label">Employés Totaux</span>
            <div class="hk-stat-icon">👥</div>
        </div>
        <div class="hk-stat-value">{{ $totalEmployees }}</div>
    </div>
    <div class="hk-stat-card">
        <div class="hk-stat-header">
            <span class="hk-stat-label">Visages Enregistrés</span>
            <div class="hk-stat-icon">😊</div>
        </div>
        <div class="hk-stat-value" style="color: var(--hk-success)">{{ $faceEnabled }}</div>
    </div>
    <div class="hk-stat-card">
        <div class="hk-stat-header">
            <span class="hk-stat-label">En attente d'enregistrement</span>
            <div class="hk-stat-icon">⏳</div>
        </div>
        <div class="hk-stat-value" style="color: var(--hk-warning)">{{ $totalEmployees - $faceEnabled }}</div>
    </div>
</div>

<div class="hk-grid">
    <div class="hk-content-card">
        <div class="hk-card-header">
            <h2 class="hk-card-title">Employés</h2>
        </div>
        <div class="hk-card-body">
            <div class="hk-table-wrap">
                <table class="hk-table">
                    <thead>
                        <tr>
                            <th>Employé</th>
                            <th>Visage Enregistré</th>
                            <th>Dernier Pointage IN</th>
                            <th>Dernier Pointage OUT</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($employees as $emp)
                        <?php $face = \App\Models\EmployeeFaceId::where('tenant_id', session('current_tenant_id'))->where('employee_id', $emp->id)->first(); ?>
                        <tr>
                            <td>{{ $emp->firstname }} {{ $emp->lastname }}</td>
                            <td>
                                <span class="hk-badge {{ $face && $face->is_active ? 'hk-badge-success' : 'hk-badge-danger' }}">
                                    {{ $face && $face->is_active ? 'Oui' : 'Non' }}
                                </span>
                            </td>
                            <td>{{ $face ? ($face->last_clock_in ? $face->last_clock_in->format('d/m/Y H:i') : '-') : '-' }}</td>
                            <td>{{ $face ? ($face->last_clock_out ? $face->last_clock_out->format('d/m/Y H:i') : '-') : '-' }}</td>
                            <td class="text-right">
                                @if($face && $face->is_active)
                                <a href="{{ route('faceid.employee.face', $emp) }}" class="hk-btn hk-btn-ghost hk-btn-sm">Ré-enregistrer</a>
                                @else
                                <button onclick="openFaceModal({{ $emp->id }})" class="hk-btn hk-btn-primary hk-btn-sm">Enregistrer Visage</button>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="hk-content-card">
        <div class="hk-card-header">
            <h2 class="hk-card-title">Pointage Rapide</h2>
        </div>
        <div class="hk-card-body" style="text-align: center; padding: 48px 24px;">
            <div style="width: 120px; height: 120px; border-radius: 50%; border: 2px dashed var(--hk-border); display: flex; align-items: center; justify-content: center; margin: 0 auto 24px; font-size: 48px; background: rgba(56, 189, 248, 0.1);">
                📷
            </div>
            <div style="font-size: 18px; font-weight: 700; color: var(--hk-text); margin-bottom: 8px;">Caméra Face ID</div>
            <div style="font-size: 14px; color: var(--hk-text-muted); margin-bottom: 32px;">Scannez le visage de l'employé pour pointer</div>
            <div style="display: flex; gap: 12px; justify-content: center;">
                <button class="hk-btn hk-btn-primary" style="padding: 12px 24px;">🕒 Pointer Entrée</button>
                <button class="hk-btn hk-btn-ghost" style="padding: 12px 24px;">🕒 Pointer Sortie</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Enregistrement Visage -->
<div id="faceModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.7); backdrop-filter: blur(8px); z-index: 1000; align-items: center; justify-content: center;">
    <div class="hk-content-card" style="max-width: 500px; width: 90%;">
        <div class="hk-card-header">
            <h2 class="hk-card-title">Enregistrer le Visage</h2>
            <button onclick="closeFaceModal()" style="background: none; border: none; color: var(--hk-text-muted); font-size: 24px; cursor: pointer;">&times;</button>
        </div>
        <div class="hk-card-body">
            <form id="faceRegisterForm">
                <input type="hidden" name="employee_id" id="faceEmployeeId">
                <div class="hk-form-group">
                    <label class="hk-form-label">Photo de Référence</label>
                    <input type="file" name="face_image" accept="image/*" required class="hk-form-input">
                </div>
                <div style="font-size: 12px; color: var(--hk-text-muted); margin-bottom: 16px;">
                    Le template faciale sera généré automatiquement depuis la photo.
                </div>
                <div style="display: flex; gap: 12px; justify-content: flex-end; margin-top: 24px;">
                    <button type="button" onclick="closeFaceModal()" class="hk-btn hk-btn-ghost">Annuler</button>
                    <button type="submit" class="hk-btn hk-btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openFaceModal(employeeId) {
        document.getElementById('faceEmployeeId').value = employeeId;
        document.getElementById('faceModal').style.display = 'flex';
    }
    function closeFaceModal() { document.getElementById('faceModal').style.display = 'none'; }
</script>

<style>
    .text-right { text-align: right; }
</style>
@endsection