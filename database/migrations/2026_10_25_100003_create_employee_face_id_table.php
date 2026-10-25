<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_face_ids', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->nullable();
            $table->unsignedBigInteger('employee_id');
            $table->string('face_template')->nullable()->comment('Template faciale encodé (base64 ou hash)');
            $table->string('face_image_path')->nullable()->comment('Chemin vers la photo de référence');
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_clock_in')->nullable();
            $table->timestamp('last_clock_out')->nullable();
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->unique(['tenant_id', 'employee_id']);
            $table->index(['employee_id']);
        });

        Schema::create('face_attendance_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->nullable();
            $table->unsignedBigInteger('employee_id');
            $table->timestamp('clocked_at');
            $table->string('type')->comment('clock_in, clock_out');
            $table->string('method')->default('face_id')->comment('face_id, manual, badge');
            $table->string('device_info')->nullable();
            $table->string('ip_address')->nullable();
            $table->decimal('confidence_score', 5, 2)->nullable()->comment('Score de confiance reconnaissance faciale');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->index(['employee_id', 'clocked_at']);
            $table->index(['tenant_id', 'clocked_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('face_attendance_logs');
        Schema::dropIfExists('employee_face_ids');
    }
};
