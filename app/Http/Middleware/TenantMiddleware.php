<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class TenantMiddleware
{
    /**
     * Middleware d'isolation multi-tenant.
     * Filtre automatiquement les requêtes par tenant_id de l'utilisateur connecté.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if ($user) {
            $tenant = $user->tenant()->first();

            if ($tenant) {
                // Stocke le tenant actuel dans la session pour utilisation globale
                session(['current_tenant_id' => $tenant->id]);
                session(['current_tenant_plan' => $tenant->plan]);
                session(['current_tenant_name' => $tenant->name]);

                // Ajoute automatiquement tenant_id aux requêtes de création
                if ($request->isMethod('post') || $request->isMethod('put') || $request->isMethod('patch')) {
                    $request->merge(['tenant_id' => $tenant->id]);
                }
            }
        }

        return $next($request);
    }
}
