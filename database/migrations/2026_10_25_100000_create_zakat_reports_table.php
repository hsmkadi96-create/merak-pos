<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('zakat_reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->nullable();
            $table->string('period')->comment('Année ou période du zakat');
            $table->decimal('nisab_threshold', 15, 4)->default(0)->comment('Seuil nisab en DZD');
            $table->decimal('total_stock_value', 15, 4)->default(0)->comment('Valeur totale du stock');
            $table->decimal('total_cash', 15, 4)->default(0)->comment('Liquidités');
            $table->decimal('total_receivables', 15, 4)->default(0)->comment('Créances');
            $table->decimal('zakat_base', 15, 4)->default(0)->comment('Base de calcul du zakat');
            $table->decimal('zakat_amount', 15, 4)->default(0)->comment('Montant du zakat (2.5%)');
            $table->text('notes')->nullable();
            $table->boolean('is_paid')->default(false);
            $table->date('paid_at')->nullable();
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->index(['tenant_id', 'period']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('zakat_reports');
    }
};
