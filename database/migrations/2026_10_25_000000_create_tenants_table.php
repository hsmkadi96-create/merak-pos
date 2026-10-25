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
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Nom du client SAAS');
            $table->string('email')->unique()->comment('Email du client');
            $table->string('subdomain')->unique()->nullable()->comment('Sous-domaine dédié');
            $table->string('domain')->nullable()->comment('Domaine personnalisé');
            $table->string('plan')->default('free')->comment('free, pro, enterprise');
            $table->date('trial_ends_at')->nullable();
            $table->date('subscription_ends_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('settings')->nullable()->comment('Config JSON spécifique au tenant');
            $table->unsignedBigInteger('created_by')->nullable()->comment('Admin qui a créé ce tenant');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};
