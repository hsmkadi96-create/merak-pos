<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('type')->comment('stock_alert, payment, zakat, appointment, faceid, saas, system');
            $table->string('title');
            $table->text('message');
            $table->string('url')->nullable()->comment('Lien vers la page concernée');
            $table->string('icon')->default('🔔')->comment('Emoji ou icône');
            $table->string('color')->default('blue')->comment('blue, green, yellow, red, purple');
            $table->boolean('is_read')->default(false);
            $table->boolean('is_priority')->default(false)->comment('Notification urgente');
            $table->timestamp('read_at')->nullable();
            $table->json('data')->nullable()->comment('Données supplémentaires');
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['tenant_id', 'is_read']);
            $table->index(['user_id', 'is_read']);
            $table->index(['type', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
