@extends('layouts.master')

@section('content')
<div class="hk-page-header">
    <h1 class="hk-page-title">Notifications</h1>
    <p class="hk-page-subtitle">Centre de notifications MERAK POS</p>
</div>

@if(session('success'))
<div style="background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.3); color: var(--hk-success); padding: 12px 16px; border-radius: 10px; margin-bottom: 24px; font-size: 14px;">
    {{ session('success') }}
</div>
@endif

<div class="hk-stats-grid">
    <div class="hk-stat-card">
        <div class="hk-stat-header">
            <span class="hk-stat-label">Total</span>
            <div class="hk-stat-icon">📬</div>
        </div>
        <div class="hk-stat-value">{{ $notifications->total() }}</div>
    </div>
    <div class="hk-stat-card">
        <div class="hk-stat-header">
            <span class="hk-stat-label">Non lues</span>
            <div class="hk-stat-icon">🔴</div>
        </div>
        <div class="hk-stat-value" style="color: var(--hk-danger)">{{ $unreadCount }}</div>
    </div>
    <div class="hk-stat-card">
        <div class="hk-stat-header">
            <span class="hk-stat-label">Prioritaires</span>
            <div class="hk-stat-icon">⚠️</div>
        </div>
        <div class="hk-stat-value">{{ $notifications->where('is_priority', true)->count() }}</div>
    </div>
</div>

<div class="hk-grid">
    <div class="hk-content-card" style="grid-column: span 2;">
        <div class="hk-card-header">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <h2 class="hk-card-title">Liste des Notifications</h2>
                <div style="display: flex; gap: 8px;">
                    <a href="{{ route('notifications.read-all') }}" onclick="return confirm('Marquer toutes comme lues ?')" class="hk-btn hk-btn-ghost hk-btn-sm">Tout marquer lu</a>
                    @if($unreadCount > 0)
                    <form method="POST" action="{{ route('notifications.read-all') }}" style="display: inline;">
                        @csrf
                        <button type="submit" class="hk-btn hk-btn-primary hk-btn-sm">Marquer tout lu</button>
                    </form>
                    @endif
                </div>
            </div>
        </div>

        <div class="hk-card-body">
            <div class="hk-table-wrap">
                <table class="hk-table">
                    <thead>
                        <tr>
                            <th>Icône</th>
                            <th>Titre</th>
                            <th>Message</th>
                            <th>Type</th>
                            <th>Statut</th>
                            <th>Date</th>
                            <th class="text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($notifications as $notification)
                        <tr @if(!$notification->is_read) style="background: rgba(56, 189, 248, 0.08);" @endif>
                            <td style="font-size: 20px;">{{ $notification->icon }}</td>
                            <td>
                                <div style="font-weight: 600;">{{ $notification->title }}</div>
                                @if($notification->is_priority)
                                <span class="hk-badge hk-badge-danger" style="font-size: 10px; margin-top: 2px;">Prioritaire</span>
                                @endif
                            </td>
                            <td>{{ $notification->message }}</td>
                            <td>
                                <span class="hk-badge hk-badge-{{ $notification->color }}">
                                    {{ ucfirst($notification->type) }}
                                </span>
                            </td>
                            <td>
                                <span class="hk-badge {{ $notification->is_read ? 'hk-badge-success' : 'hk-badge-warning' }}">
                                    {{ $notification->is_read ? 'Lu' : 'Non lu' }}
                                </span>
                            </td>
                            <td>{{ $notification->created_at->format('d/m/Y H:i') }}</td>
                            <td class="text-right">
                                @if(!$notification->is_read)
                                <form method="POST" action="{{ route('notifications.read', $notification) }}" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="hk-btn hk-btn-ghost hk-btn-sm" style="padding: 4px 10px;">Marquer lu</button>
                                </form>
                                @endif
                                <form method="POST" action="{{ route('notifications.destroy', $notification) }}" style="display: inline;" onsubmit="return confirm('Supprimer cette notification ?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="hk-btn hk-btn-ghost hk-btn-sm" style="padding: 4px 10px; color: var(--hk-danger);">🗑️</button>
                                </form>
                                @if($notification->url)
                                <a href="{{ $notification->url }}" class="hk-btn hk-btn-ghost hk-btn-sm" style="padding: 4px 10px;">Voir</a>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{ $notifications->links() }}
        </div>
    </div>
</div>

<style>
    .text-right { text-align: right; }
</style>
@endsection