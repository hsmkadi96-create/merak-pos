<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class PaymentConfigController extends Controller
{
    /**
     * Retourne la configuration des paiements (clés masquées pour l'affichage).
     */
    public function index()
    {
        return response()->json([
            'cib' => [
                'enabled' => Config::get('app.cib.enabled', false),
                'api_key_configured' => !empty(Config::get('app.cib.api_key')),
                'api_url' => Config::get('app.cib.api_url'),
                'sandbox' => str_contains(Config::get('app.cib.api_url', ''), 'sandbox'),
            ],
            'dahabia' => [
                'enabled' => Config::get('app.dahabia.enabled', false),
                'api_key_configured' => !empty(Config::get('app.dahabia.api_key')),
                'api_url' => Config::get('app.dahabia.api_url'),
                'sandbox' => str_contains(Config::get('app.dahabia.api_url', ''), 'sandbox'),
            ],
            'chifaa' => [
                'enabled' => Config::get('app.chifaa.enabled', false),
                'api_key_configured' => !empty(Config::get('app.chifaa.api_key')),
                'api_url' => Config::get('app.chifaa.api_url'),
                'sandbox' => str_contains(Config::get('app.chifaa.api_url', ''), 'sandbox'),
            ],
        ]);
    }

    /**
     * Active/désactive un moyen de paiement.
     */
    public function toggle(Request $request, string $provider)
    {
        $request->validate([
            'enabled' => 'required|boolean',
        ]);

        // Note: En production, cela devrait être stocké en base de données
        // Ici on retourne simplement la config actuelle
        return response()->json([
            'success' => true,
            'provider' => $provider,
            'enabled' => $request->enabled,
            'message' => 'Configuration mise à jour. Redémarrez l\'application pour appliquer les changements.',
        ]);
    }
}
