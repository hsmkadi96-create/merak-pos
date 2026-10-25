@php
$unreadCount = $unread_notifications_count ?? 0;
$recentNotifications = \App\Models\Notification::where('user_id', auth()->id())
    ->orderBy('created_at', 'desc')
    ->limit(5)
    ->get();
@endphp

<!-- Bouton notification dans le header -->
<div class="hk-notification-widget" style="position: relative; display: inline-block;">
    <button class="hk-menu-toggle" id="notifBtn" title="Notifications">
        🔔
        @if($unreadCount > 0)
        <span style="position: absolute; top: 6px; right: 6px; width: 10px; height: 10px; background: var(--hk-danger); border-radius: 50%; display: block;"></span>
        @endif
    </button>

    <!-- Dropdown notifications -->
    <div id="notifDropdown" style="display: none; position: absolute; top: 52px; right: 0; width: 320px; background: var(--hk-card); backdrop-filter: blur(16px); border: 1px solid var(--hk-card-border); border-radius: var(--hk-radius); box-shadow: var(--hk-shadow); z-index: 1000; animation: hkFadeInUp 0.3s ease-out;">
        <div style="padding: 16px; border-bottom: 1px solid var(--hk-card-border);">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <h3 style="margin: 0; font-size: 16px; font-weight: 700; color: var(--hk-text);">Notifications</h3>
                @if($unreadCount > 0)
                <a href="{{ route('notifications.index') }}" style="font-size: 12px; color: var(--hk-primary); font-weight: 600;">Tout voir</a>
                @endif
            </div>
        </div>

        <div style="max-height: 400px; overflow-y: auto; padding: 0;">
            @if($recentNotifications->isEmpty())
            <div style="padding: 24px; text-align: center; color: var(--hk-text-muted);">
                Aucune notification
            </div>
            @else
            @foreach ($recentNotifications as $notif)
            <div style="padding: 12px 16px; border-bottom: 1px solid var(--hk-card-border); cursor: pointer; transition: var(--hk-transition); @if(!$notif->is_read) background: rgba(56, 189, 248, 0.05); @endif" onclick="window.location.href='{{ $notif->url ?? route('notifications.index') }}'">
                <div style="display: flex; gap: 10px; align-items: flex-start;">
                    <span style="font-size: 20px;">{{ $notif->icon }}</span>
                    <div>
                        <div style="font-size: 13px; font-weight: 600; color: var(--hk-text); margin-bottom: 2px;">{{ $notif->title }}</div>
                        <div style="font-size: 12px; color: var(--hk-text-muted); line-height: 1.4;">{{ $notif->message }}</div>
                        <div style="font-size: 10px; color: var(--hk-text-muted); margin-top: 4px;">{{ $notif->created_at->diffForHumans() }}</div>
                        @if(!$notif->is_read)
                        <div style="font-size: 10px; color: var(--hk-primary); margin-top: 2px; font-weight: 700;">Nouveau</div>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
            @endif
        </div>

        <div style="padding: 12px 16px; border-top: 1px solid var(--hk-card-border);">
            <a href="{{ route('notifications.index') }}" class="hk-btn hk-btn-ghost" style="width: 100%; padding: 8px; font-size: 13px;">Voir toutes les notifications</a>
        </div>
    </div>
</div>

<style>
    @keyframes hkFadeInUp {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

<script>
    document.getElementById('notifBtn')?.addEventListener('click', function(e) {
        e.stopPropagation();
        const dropdown = document.getElementById('notifDropdown');
        dropdown.style.display = dropdown.style.display === 'none' ? 'block' : 'none';
        if (dropdown.style.display === 'block') {
            fetchNotifCount();
        }
    });

    document.addEventListener('click', function(e) {
        const btn = document.getElementById('notifBtn');
        const dropdown = document.getElementById('notifDropdown');
        if (btn && dropdown && !btn.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.style.display = 'none';
        }
    });

    // Rafraîchir le compteur toutes les 30 secondes
    setInterval(fetchNotifCount, 30000);

    function fetchNotifCount() {
        fetch('{{ route('notifications.api.unread-count') }}')
            .then(r => r.json())
            .then(data => {
                const dot = document.querySelector('#notifBtn span');
                if (data.unread_count > 0) {
                    if (!dot) {
                        const d = document.createElement('span');
                        d.style.cssText = 'position: absolute; top: 6px; right: 6px; width: 10px; height: 10px; background: var(--hk-danger); border-radius: 50%; display: block;';
                        document.getElementById('notifBtn').appendChild(d);
                    }
                } else if (dot) {
                    dot.remove();
                }
            });
    }
</script>