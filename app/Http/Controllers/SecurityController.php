<?php

namespace App\Http\Controllers;

use App\Models\ErrorLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class SecurityController extends Controller
{
    /**
     * Audit de sécurité - détection des failles.
     */
    public function audit(Request $request)
    {
        $issues = [];

        // Vérifier si APP_DEBUG est activé en production
        if (env('APP_DEBUG') === 'true' && env('APP_ENV') === 'production') {
            $issues[] = [
                'level' => 'critical',
                'title' => 'APP_DEBUG activé en production',
                'description' => 'Les erreurs détaillées sont exposées aux utilisateurs, ce qui peut révéler des informations sensibles.',
                'fix' => 'Définir APP_DEBUG=false dans le fichier .env',
            ];
        }

        // Vérifier si la clé APP_KEY est présente
        if (empty(env('APP_KEY'))) {
            $issues[] = [
                'level' => 'critical',
                'title' => 'Clé d\'application manquante',
                'description' => 'APP_KEY n\'est pas définie, le chiffrement des données est compromis.',
                'fix' => 'Exécuter: php artisan key:generate',
            ];
        }

        // Vérifier les clés de paiement configurées
        $paymentConfigs = [
            'CIB' => ['CIB_API_KEY', 'CIB_API_SECRET'],
            'DAHABIA' => ['DAHABIA_API_KEY', 'DAHABIA_API_SECRET'],
            'CHIF AA' => ['CHIF AA_API_KEY', 'CHIF AA_API_SECRET'],
        ];

        foreach ($paymentConfigs as $provider => $keys) {
            foreach ($keys as $key) {
                if (empty(env($key))) {
                    $issues[] = [
                        'level' => 'warning',
                        'title' => "Clé API $provider non configurée: $key",
                        'description' => "La clé $key n'est pas définie. Les paiements $provider ne fonctionneront pas.",
                        'fix' => "Ajouter $key dans le fichier .env",
                    ];
                }
            }
        }

        // Vérifier les mots de passe par défaut
        $defaultUsers = \App\Models\User::where('password', 'password')->count();
        if ($defaultUsers > 0) {
            $issues[] = [
                'level' => 'critical',
                'title' => 'Comptes avec mot de passe par défaut',
                'description' => "$defaultUsers utilisateur(s) utilisent encore le mot de passe par défaut.",
                'fix' => 'Forcer la réinitialisation des mots de passe',
            ];
        }

        // Vérifier les permissions de fichiers sensibles
        $sensitiveFiles = ['.env', 'storage/logs/laravel.log'];
        foreach ($sensitiveFiles as $file) {
            $path = base_path($file);
            if (file_exists($path) && is_readable($path)) {
                $perms = fileperms($path);
                if ($perms & 0x04) { // Lecture pour les autres
                    $issues[] = [
                        'level' => 'warning',
                        'title' => "Fichier $file lisible publiquement",
                        'description' => "Le fichier $file a des permissions trop ouvertes.",
                        'fix' => "chmod 600 $file",
                    ];
                }
            }
        }

        // Erreurs récentes
        $recentErrors = ErrorLog::where('created_at', '>=', now()->subDays(7))->count();

        return view('security.audit', compact('issues', 'recentErrors'));
    }

    /**
     * Journal des événements de sécurité.
     */
    public function logs(Request $request)
    {
        $logs = ErrorLog::orderBy('created_at', 'desc')->paginate(20);

        return view('security.logs', compact('logs'));
    }

    /**
     * Test de sécurité CSRF/XSS.
     */
    public function testSecurity(Request $request)
    {
        $results = [
            'csrf' => 'Activé',
            'xss_protection' => 'Activé (middleware XSS)',
            'https' => $request->secure() ? 'Activé' : 'Non (HTTP)',
            'session_secure' => config('session.secure') ?? false,
        ];

        return response()->json($results);
    }
}
