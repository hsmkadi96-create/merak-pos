<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tenant extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'subdomain',
        'domain',
        'plan',
        'trial_ends_at',
        'subscription_ends_at',
        'is_active',
        'settings',
        'created_by',
        'deleted_at',
    ];

    protected $casts = [
        'trial_ends_at' => 'datetime',
        'subscription_ends_at' => 'datetime',
        'is_active' => 'boolean',
        'settings' => 'array',
    ];

    /**
     * L'utilisateur qui a créé ce tenant.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Les utilisateurs liés à ce tenant.
     */
    public function users(): HasMany
    {
        return $this->hasMany(TenantUser::class);
    }

    /**
     * Vérifie si l'abonnement est actif.
     */
    public function isSubscribed(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->plan === 'free') {
            return true;
        }

        if ($this->subscription_ends_at && $this->subscription_ends_at->isPast()) {
            return false;
        }

        return true;
    }

    /**
     * Vérifie si le tenant est en période d'essai.
     */
    public function isOnTrial(): bool
    {
        return $this->trial_ends_at ? $this->trial_ends_at->isFuture() : false;
    }

    /**
     * Scope pour les tenants actifs uniquement.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
