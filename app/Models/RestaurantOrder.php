<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RestaurantOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id', 'table_id', 'order_number', 'waiter_id', 'status',
        'subtotal', 'tax', 'total', 'notes',
    ];

    protected $casts = [
        'subtotal' => 'decimal:4',
        'tax' => 'decimal:4',
        'total' => 'decimal:4',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function table(): BelongsTo
    {
        return $this->belongsTo(RestaurantTable::class);
    }

    public function waiter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'waiter_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(RestaurantOrderItem::class);
    }
}
