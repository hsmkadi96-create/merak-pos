<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Rapport Zakat - MERAK POS</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #333; padding: 40px; }
        .header { text-align: center; border-bottom: 3px solid #0ea5e9; padding-bottom: 20px; margin-bottom: 30px; }
        .header h1 { color: #0f172a; margin: 0; font-size: 28px; }
        .header .subtitle { color: #64748b; font-size: 14px; }
        .company { font-weight: 700; color: #0ea5e9; font-size: 18px; margin-bottom: 5px; }
        .stats { display: flex; gap: 20px; margin-bottom: 30px; }
        .stat-card { flex: 1; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; text-align: center; }
        .stat-value { font-size: 24px; font-weight: 800; color: #0f172a; }
        .stat-label { font-size: 12px; color: #64748b; text-transform: uppercase; margin-top: 4px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        th, td { padding: 12px; border: 1px solid #e2e8f0; text-align: left; }
        th { background: #0f172a; color: #fff; font-size: 12px; text-transform: uppercase; }
        .total { font-weight: 700; font-size: 16px; color: #0ea5e9; }
        .footer { margin-top: 40px; border-top: 1px solid #e2e8f0; padding-top: 20px; font-size: 12px; color: #64748b; text-align: center; }
        .dev { color: #0ea5e9; font-weight: 700; }
    </style>
</head>
<body>
    <div class="header">
        <div class="company">MERAK POS</div>
        <h1>Rapport Zakat</h1>
        <div class="subtitle">Période: {{ $zakatReport->period }} | Développé par <span class="dev">H-KADI</span></div>
    </div>

    <div class="stats">
        <div class="stat-card">
            <div class="stat-value">{{ number_format($zakatReport->zakat_base, 2) }} DZD</div>
            <div class="stat-label">Base Zakat</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ number_format($zakatReport->zakat_amount, 2) }} DZD</div>
            <div class="stat-label">Montant Zakat (2.5%)</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ number_format($zakatReport->nisab_threshold, 2) }} DZD</div>
            <div class="stat-label">Seuil Nisab</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ $zakatReport->is_paid ? 'Payé' : 'En attente' }}</div>
            <div class="stat-label">Statut</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Désignation</th>
                <th>Montant (DZD)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Valeur du Stock</td>
                <td>{{ number_format($zakatReport->total_stock_value, 2) }}</td>
            </tr>
            <tr>
                <td>Liquidités</td>
                <td>{{ number_format($zakatReport->total_cash, 2) }}</td>
            </tr>
            <tr>
                <td>Créances</td>
                <td>{{ number_format($zakatReport->total_receivables, 2) }}</td>
            </tr>
            <tr class="total">
                <td>Base Zakat (Somme)</td>
                <td>{{ number_format($zakatReport->zakat_base, 2) }}</td>
            </tr>
            <tr class="total">
                <td>Zakat à verser (2.5%)</td>
                <td>{{ number_format($zakatReport->zakat_amount, 2) }}</td>
            </tr>
        </tbody>
    </table>

    @if($zakatReport->notes)
    <div style="background: #f1f5f9; border-radius: 8px; padding: 16px; margin-bottom: 30px;">
        <strong>Notes:</strong> {{ $zakatReport->notes }}
    </div>
    @endif

    <div class="footer">
        Généré le {{ now()->format('d/m/Y H:i') }} par MERAK POS<br>
        Développé par <strong class="dev">H-KADI</strong> — Champ non modifiable
    </div>
</body>
</html>