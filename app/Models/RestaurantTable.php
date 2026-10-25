<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RestaurantTable extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id', 'name', 'status', 'capacity', 'section', 'qr_code_url', 'current_order_id',
    ];

    protected $casts = [
        'capacity' => 'integer',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(RestaurantOrder::class);
    }

    public function currentOrder(): BelongsTo
    {
        return $this->belongsTo(RestaurantOrder::class, 'current_order_id');
    }

    public function scopeFree($query)
    {
        return $query->where('status', 'free');
    }

    public function scopeOccupied($query)
    {
        return $query->where('status', 'occupied');
    }

    public function scopeReserved($query)
    {
        return $query->where('status', 'reserved');
    }
}
