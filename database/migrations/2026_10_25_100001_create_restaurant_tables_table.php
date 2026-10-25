<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('restaurant_tables', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->nullable();
            $table->string('name')->comment('Nom de la table (ex: Table 5, Terrasse A)');
            $table->string('status')->default('free')->comment('free, occupied, reserved, cleaning, maintenance');
            $table->integer('capacity')->default(4)->comment('Nombre de places');
            $table->string('section')->nullable()->comment('Salle, Terrasse, VIP, etc.');
            $table->text('qr_code_url')->nullable()->comment('URL QR code pour commande par table');
            $table->unsignedBigInteger('current_order_id')->nullable();
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->index(['tenant_id', 'status']);
        });

        Schema::create('restaurant_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->nullable();
            $table->unsignedBigInteger('table_id')->nullable();
            $table->string('order_number')->unique();
            $table->unsignedBigInteger('waiter_id')->nullable()->comment('Serveur');
            $table->string('status')->default('pending')->comment('pending, preparing, ready, served, paid, cancelled');
            $table->decimal('subtotal', 15, 4)->default(0);
            $table->decimal('tax', 15, 4)->default(0);
            $table->decimal('total', 15, 4)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('table_id')->references('id')->on('restaurant_tables')->onDelete('set null');
            $table->foreign('waiter_id')->references('id')->on('users')->onDelete('set null');
            $table->index(['tenant_id', 'status']);
            $table->index(['table_id']);
        });

        Schema::create('restaurant_order_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('restaurant_order_id');
            $table->unsignedBigInteger('product_id');
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price', 15, 4)->default(0);
            $table->decimal('total_price', 15, 4)->default(0);
            $table->string('kitchen_status')->default('pending')->comment('pending, in_progress, ready, served');
            $table->text('special_instructions')->nullable();
            $table->timestamps();

            $table->foreign('restaurant_order_id')->references('id')->on('restaurant_orders')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('restaurant_order_items');
        Schema::dropIfExists('restaurant_orders');
        Schema::dropIfExists('restaurant_tables');
    }
};
