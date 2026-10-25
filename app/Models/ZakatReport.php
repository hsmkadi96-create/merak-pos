<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ZakatReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'period',
        'nisab_threshold',
        'total_stock_value',
        'total_cash',
        'total_receivables',
        'zakat_base',
        'zakat_amount',
        'notes',
        'is_paid',
        'paid_at',
    ];

    protected $casts = [
        'nisab_threshold' => 'decimal:4',
        'total_stock_value' => 'decimal:4',
        'total_cash' => 'decimal:4',
        'total_receivables' => 'decimal:4',
        'zakat_base' => 'decimal:4',
        'zakat_amount' => 'decimal:4',
        'is_paid' => 'boolean',
        'paid_at' => 'datetime',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Calcule le zakat (2.5% de la base éligible).
     */
    public static function calculate(array $data): array
    {
        $nisab = $data['nisab_threshold'] ?? 0;
        $base = ($data['total_stock_value'] ?? 0) + ($data['total_cash'] ?? 0) + ($data['total_receivables'] ?? 0);

        $zakatAmount = 0;
        if ($base >= $nisab && $nisab > 0) {
            $zakatAmount = $base * 0.025; // 2.5%
        }

        return [
            'zakat_base' => $base,
            'zakat_amount' => round($zakatAmount, 4),
            'nisab_threshold' => $nisab,
            'eligible' => $base >= $nisab,
        ];
    }
}
