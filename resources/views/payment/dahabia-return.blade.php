<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paiement DAHABIA - MERAK POS</title>
    <link rel="stylesheet" href="/css/hkadi-modern.css">
    <style>
        .payment-return {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--hk-bg);
        }
        .payment-card {
            background: var(--hk-card);
            backdrop-filter: blur(12px);
            border: 1px solid var(--hk-card-border);
            border-radius: var(--hk-radius);
            padding: 48px;
            box-shadow: var(--hk-shadow);
            text-align: center;
            max-width: 480px;
            width: 100%;
        }
        .payment-icon {
            width: 80px;
            height: 80px;
            border-radius: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
            font-size: 36px;
        }
        .success .payment-icon { background: rgba(16, 185, 129, 0.15); color: var(--hk-success); }
        .pending .payment-icon { background: rgba(245, 158, 11, 0.15); color: var(--hk-warning); }
        .error .payment-icon { background: rgba(239, 68, 68, 0.15); color: var(--hk-danger); }
        .payment-title { font-size: 24px; font-weight: 800; color: var(--hk-text); margin-bottom: 8px; }
        .payment-subtitle { font-size: 15px; color: var(--hk-text-muted); margin-bottom: 32px; }
        .payment-tx { font-family: monospace; font-size: 13px; color: var(--hk-text-muted); background: rgba(0,0,0,0.3); padding: 8px 12px; border-radius: 8px; margin-bottom: 32px; display: inline-block; }
        .hk-btn { margin: 0 6px; }
    </style>
</head>
<body>
    <div class="payment-return">
        <div class="payment-card {{ $status ?? 'pending' }}">
            <div class="payment-icon {{ $status ?? 'pending' }}">
                @if($status === 'success') ✅
                @elseif($status === 'error') ❌
                @else ⏳
                @endif
            </div>
            <h1 class="payment-title">
                @if($status === 'success') Paiement Réussi
                @elseif($status === 'error') Paiement Échoué
                @else Paiement en cours de traitement
                @endif
            </h1>
            <p class="payment-subtitle">
                @if($status === 'success') Votre paiement DAHABIA a été confirmé avec succès.
                @elseif($status === 'error') Votre paiement DAHABIA n'a pas pu être complété.
                @else Votre paiement est en cours de vérification. Veuillez patienter.
                @endif
            </p>
            <div class="payment-tx">Transaction: {{ $transactionId ?? 'N/A' }}</div>
            <div style="display: flex; gap: 12px; justify-content: center;">
                <a href="/" class="hk-btn hk-btn-primary">Retour au Dashboard</a>
                <a href="/sales" class="hk-btn hk-btn-ghost">Voir les Ventes</a>
            </div>
        </div>
    </div>
</body>
</html>