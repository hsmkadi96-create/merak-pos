<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Créer une notification pour un utilisateur spécifique.
     */
    public function create(array $data): Notification
    {
        $notification = Notification::create([
            'tenant_id' => $data['tenant_id'] ?? null,
            'user_id' => $data['user_id'],
            'type' => $data['type'],
            'title' => $data['title'],
            'message' => $data['message'],
            'url' => $data['url'] ?? null,
            'icon' => $data['icon'] ?? '🔔',
            'color' => $data['color'] ?? 'blue',
            'is_priority' => $data['is_priority'] ?? false,
            'data' => $data['data'] ?? null,
        ]);

        Log::info('Notification créée', [
            'id' => $notification->id,
            'type' => $notification->type,
            'user_id' => $notification->user_id,
        ]);

        return $notification;
    }

    /**
     * Notifier tous les utilisateurs d'un tenant.
     */
    public function notifyTenantUsers(int $tenantId, array $data): array
    {
        $users = User::whereHas('tenant', function ($q) use ($tenantId) {
            $q->where('tenant_id', $tenantId);
        })->get();

        $notifications = [];
        foreach ($users as $user) {
            $notifications[] = $this->create(array_merge($data, [
                'tenant_id' => $tenantId,
                'user_id' => $user->id,
            ]));
        }

        return $notifications;
    }

    /**
     * Alerte stock bas.
     */
    public function stockAlert(int $tenantId, string $productName, int $currentStock, int $minStock): Notification
    {
        return $this->notifyTenantUsers($tenantId, [
            'type' => 'stock_alert',
            'title' => '⚠️ Stock bas',
            'message' => "Le produit « {$productName} » est en dessous du seuil minimum ({$currentStock} / {$minStock}).",
            'url' => '/products',
            'icon' => '📦',
            'color' => 'yellow',
            'is_priority' => true,
            'data' => ['product' => $productName, 'stock' => $currentStock, 'min_stock' => $minStock],
        ]);
    }

    /**
     * Confirmation de paiement.
     */
    public function paymentConfirmed(int $tenantId, string $provider, string $orderId, float $amount): Notification
    {
        $icons = ['cib' => '🏦', 'dahabia' => '🏧', 'chifaa' => '🏥'];
        $names = ['cib' => 'CIB', 'dahabia' => 'DAHABIA', 'chifaa' => 'Carte Chifaa'];

        return $this->notifyTenantUsers($tenantId, [
            'type' => 'payment',
            'title' => '✅ Paiement confirmé',
            'message' => "Paiement {$names[$provider] ?? $provider} de {$amount} DZD pour la commande {$orderId} validé.",
            'url' => '/sales',
            'icon' => $icons[$provider] ?? '💳',
            'color' => 'green',
            'is_priority' => false,
            'data' => ['provider' => $provider, 'order_id' => $orderId, 'amount' => $amount],
        ]);
    }

    /**
     * Rappel Zakat dû.
     */
    public function zakatDue(int $tenantId, float $amount): Notification
    {
        return $this->notifyTenantUsers($tenantId, [
            'type' => 'zakat',
            'title' => '🕌 Zakat à verser',
            'message' => "Le montant de Zakat de " . number_format($amount, 2) . " DZD est calculé et prêt à être versé.",
            'url' => '/zakat',
            'icon' => '🕌',
            'color' => 'green',
            'is_priority' => true,
            'data' => ['amount' => $amount],
        ]);
    }

    /**
     * Rappel de rendez-vous clinique.
     */
    public function appointmentReminder(int $tenantId, string $patientName, string $doctorName, string $dateTime): Notification
    {
        return $this->notifyTenantUsers($tenantId, [
            'type' => 'appointment',
            'title' => '📅 Nouveau rendez-vous',
            'message' => "RDV avec {$patientName} chez Dr {$doctorName} le {$dateTime}.",
            'url' => '/clinic/appointments',
            'icon' => '👨‍⚕️',
            'color' => 'blue',
            'is_priority' => false,
            'data' => ['patient' => $patientName, 'doctor' => $doctorName, 'datetime' => $dateTime],
        ]);
    }

    /**
     * Pointage facial employé.
     */
    public function faceIdClock(int $tenantId, string $employeeName, string $type): Notification
    {
        $typeLabel = $type === 'clock_in' ? 'entrée' : 'sortie';
        $icon = $type === 'clock_in' ? '🟢' : '🔴';

        return $this->notifyTenantUsers($tenantId, [
            'type' => 'faceid',
            'title' => "Pointage {$typeLabel}",
            'message' => "{$employeeName} a pointé {$typeLabel} par reconnaissance faciale.",
            'url' => '/faceid/logs',
            'icon' => $icon,
            'color' => 'blue',
            'is_priority' => false,
            'data' => ['employee' => $employeeName, 'type' => $type],
        ]);
    }

    /**
     * Alerte abonnement SAAS expirant bientôt.
     */
    public function saasExpiring(int $tenantId, string $tenantName, string $expiryDate): Notification
    {
        return $this->notifyTenantUsers($tenantId, [
            'type' => 'saas',
            'title' => '⏰ Abonnement expirant',
            'message' => "L'abonnement de {$tenantName} expire le {$expiryDate}. Renouvelez pour éviter l'interruption.",
            'url' => '/saas/dashboard',
            'icon' => '💎',
            'color' => 'red',
            'is_priority' => true,
            'data' => ['tenant' => $tenantName, 'expiry' => $expiryDate],
        ]);
    }

    /**
     * Notification système générale.
     */
    public function systemAlert(int $tenantId, string $title, string $message, string $color = 'blue'): Notification
    {
        return $this->notifyTenantUsers($tenantId, [
            'type' => 'system',
            'title' => $title,
            'message' => $message,
            'url' => null,
            'icon' => 'ℹ️',
            'color' => $color,
            'is_priority' => false,
        ]);
    }

    /**
     * Nombre de notifications non lues pour un utilisateur.
     */
    public function unreadCount(int $userId): int
    {
        return Notification::where('user_id', $userId)->where('is_read', false)->count();
    }

    /**
     * Marquer toutes les notifications d'un utilisateur comme lues.
     */
    public function markAllAsRead(int $userId): int
    {
        return Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);
    }
}
