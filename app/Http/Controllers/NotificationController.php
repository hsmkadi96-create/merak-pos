<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Liste des notifications de l'utilisateur connecté.
     */
    public function index(Request $request)
    {
        $userId = auth()->id();
        $tenantId = session('current_tenant_id');

        $query = Notification::where('user_id', $userId);

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('read')) {
            $query->where('is_read', $request->read === 'true');
        }

        $notifications = $query->orderBy('created_at', 'desc')->paginate(20);
        $unreadCount = $this->notificationService->unreadCount($userId);

        return view('notifications.index', compact('notifications', 'unreadCount'));
    }

    /**
     * Marquer une notification comme lue.
     */
    public function markAsRead(Notification $notification)
    {
        // Vérifier que l'utilisateur est le propriétaire
        if ($notification->user_id !== auth()->id()) {
            abort(403);
        }

        $notification->markAsRead();

        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'unread_count' => $this->notificationService->unreadCount(auth()->id())]);
        }

        return back()->with('success', 'Notification marquée comme lue.');
    }

    /**
     * Marquer toutes comme lues.
     */
    public function markAllAsRead(Request $request)
    {
        $count = $this->notificationService->markAllAsRead(auth()->id());

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'unread_count' => 0]);
        }

        return back()->with('success', "{$count} notification(s) marquée(s) comme lue(s).");
    }

    /**
     * Supprimer une notification.
     */
    public function destroy(Notification $notification)
    {
        if ($notification->user_id !== auth()->id()) {
            abort(403);
        }

        $notification->delete();

        return back()->with('success', 'Notification supprimée.');
    }

    /**
     * API : nombre de notifications non lues.
     */
    public function unreadCountApi(Request $request)
    {
        $count = $this->notificationService->unreadCount(auth()->id());

        return response()->json([
            'unread_count' => $count,
            'unread' => $count > 0,
        ]);
    }

    /**
     * API : notifications récentes pour le widget header.
     */
    public function recentApi(Request $request)
    {
        $notifications = Notification::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return response()->json($notifications);
    }
}
