<?php

namespace App\Http\Controllers\Saas;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\TenantUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class TenantController extends Controller
{
    /**
     * Liste des tenants SAAS (admin seulement).
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Tenant::class);

        $query = Tenant::query()->withTrashed()->with('creator');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('subdomain', 'like', "%{$search}%");
            });
        }

        if ($request->filled('plan')) {
            $query->where('plan', $request->plan);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $tenants = $query->latest()->paginate(15)->withQueryString();

        return view('saas.tenants.index', compact('tenants'));
    }

    /**
     * Formulaire de création d'un nouveau tenant.
     */
    public function create()
    {
        $this->authorize('create', Tenant::class);

        return view('saas.tenants.create');
    }

    /**
     * Enregistrement d'un nouveau tenant SAAS.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Tenant::class);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:tenants,email',
            'subdomain' => 'nullable|string|unique:tenants,subdomain|alpha_dash|max:63',
            'plan' => 'required|in:free,pro,enterprise',
            'trial_days' => 'nullable|integer|min:1|max:365',
            'admin_name' => 'required|string|max:255',
            'admin_email' => 'required|email|unique:users,email',
            'admin_password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        DB::transaction(function () use ($request) {
            // Création du tenant
            $tenant = Tenant::create([
                'name' => $request->name,
                'email' => $request->email,
                'subdomain' => $request->subdomain ?: str($request->name)->slug(),
                'plan' => $request->plan,
                'trial_ends_at' => $request->filled('trial_days')
                    ? now()->addDays($request->trial_days)
                    : null,
                'subscription_ends_at' => $request->plan !== 'free' && !$request->filled('trial_days')
                    ? now()->addMonth()
                    : null,
                'is_active' => true,
                'created_by' => auth()->id(),
                'settings' => [
                    'currency' => 'DZD',
                    'timezone' => 'Africa/Algiers',
                    'language' => 'fr',
                    'payment_methods' => ['cash', 'cib', 'dahabia', 'chifaa'],
                ],
            ]);

            // Création de l'utilisateur admin du tenant
            $adminUser = User::create([
                'firstname' => $request->admin_name,
                'lastname' => 'Admin',
                'username' => str($request->admin_email)->before('@'),
                'email' => $request->admin_email,
                'password' => Hash::make($request->admin_password),
                'role_id' => null, // Sera assigné via RBAC existant
                'is_all_warehouses' => true,
            ]);

            // Lien tenant-utilisateur
            TenantUser::create([
                'tenant_id' => $tenant->id,
                'user_id' => $adminUser->id,
                'role' => 'admin',
                'is_owner' => true,
            ]);
        });

        return redirect()
            ->route('saas.tenants.index')
            ->with('success', 'Le tenant a été créé avec succès.');
    }

    /**
     * Détail d'un tenant.
     */
    public function show(Tenant $tenant)
    {
        $this->authorize('view', $tenant);

        $tenant->load(['creator', 'users.user']);

        return view('saas.tenants.show', compact('tenant'));
    }

    /**
     * Formulaire d'édition.
     */
    public function edit(Tenant $tenant)
    {
        $this->authorize('update', $tenant);

        return view('saas.tenants.edit', compact('tenant'));
    }

    /**
     * Mise à jour d'un tenant.
     */
    public function update(Request $request, Tenant $tenant)
    {
        $this->authorize('update', $tenant);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:tenants,email,' . $tenant->id,
            'subdomain' => 'nullable|string|unique:tenants,subdomain,' . $tenant->id . '|alpha_dash|max:63',
            'plan' => 'required|in:free,pro,enterprise',
            'is_active' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $tenant->update([
            'name' => $request->name,
            'email' => $request->email,
            'subdomain' => $request->subdomain ?: $tenant->subdomain,
            'plan' => $request->plan,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()
            ->route('saas.tenants.index')
            ->with('success', 'Le tenant a été mis à jour.');
    }

    /**
     * Suppression (soft delete) d'un tenant.
     */
    public function destroy(Tenant $tenant)
    {
        $this->authorize('delete', $tenant);

        $tenant->delete();

        return redirect()
            ->route('saas.tenants.index')
            ->with('success', 'Le tenant a été désactivé.');
    }

    /**
     * Dashboard SAAS pour un tenant connecté.
     */
    public function dashboard(Request $request)
    {
        $tenant = $request->user()->tenant()->first();

        if (!$tenant) {
            abort(403, 'Aucun tenant associé à votre compte.');
        }

        $stats = [
            'total_products' => 0, // À remplir avec les vraies requêtes filtrées par tenant_id
            'total_sales' => 0,
            'total_clients' => 0,
            'total_stock_value' => 0,
        ];

        return view('saas.dashboard', compact('tenant', 'stats'));
    }
}
