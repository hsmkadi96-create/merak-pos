<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Ajouter tenant_id aux tables principales pour l'isolation multi-tenant
        $tables = [
            'products', 'sales', 'sale_details', 'purchases', 'purchase_details',
            'clients', 'providers', 'warehouses', 'settings', 'pos_settings',
            'payments_sales', 'payments_purchases', 'quotations',
            'transfers', 'adjustments', 'expenses', 'deposits',
            'employees', 'payrolls', 'service_jobs', 'bookings',
            'assets', 'damages', 'contracts', 'projects', 'tasks',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                Schema::table($table, function (Blueprint $table) use ($table) {
                    if (!Schema::hasColumn($table, 'tenant_id')) {
                        $table->unsignedBigInteger('tenant_id')->nullable()->after('id');
                        $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
                        $table->index('tenant_id');
                    }
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Réversible : on ne supprime pas les colonnes pour éviter la perte de données
    }
};
