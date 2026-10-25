<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clinic_doctors', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('name');
            $table->string('specialty');
            $table->string('license_number')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('bio')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });

        Schema::create('clinic_patients', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->nullable();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('gender')->nullable()->comment('M, F, Other');
            $table->string('address')->nullable();
            $table->string('emergency_contact')->nullable();
            $table->string('emergency_phone')->nullable();
            $table->text('medical_history')->nullable();
            $table->string('insurance_number')->nullable()->comment('Numéro carte Chifaa / assurance');
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->index(['tenant_id', 'phone']);
        });

        Schema::create('clinic_appointments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->nullable();
            $table->unsignedBigInteger('doctor_id');
            $table->unsignedBigInteger('patient_id');
            $table->datetime('appointment_date');
            $table->integer('duration')->default(30)->comment('Durée en minutes');
            $table->string('status')->default('scheduled')->comment('scheduled, confirmed, in_progress, completed, cancelled, no_show');
            $table->string('type')->default('consultation')->comment('consultation, follow_up, emergency, procedure');
            $table->decimal('consultation_fee', 15, 4)->default(0);
            $table->text('symptoms')->nullable();
            $table->text('diagnosis')->nullable();
            $table->text('prescription')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('doctor_id')->references('id')->on('clinic_doctors')->onDelete('cascade');
            $table->foreign('patient_id')->references('id')->on('clinic_patients')->onDelete('cascade');
            $table->index(['tenant_id', 'appointment_date']);
            $table->index(['doctor_id']);
            $table->index(['patient_id']);
        });

        Schema::create('clinic_medical_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->nullable();
            $table->unsignedBigInteger('patient_id');
            $table->unsignedBigInteger('doctor_id');
            $table->unsignedBigInteger('appointment_id')->nullable();
            $table->date('record_date');
            $table->text('symptoms');
            $table->text('diagnosis');
            $table->text('treatment');
            $table->text('prescription')->nullable();
            $table->text('lab_results')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('patient_id')->references('id')->on('clinic_patients')->onDelete('cascade');
            $table->foreign('doctor_id')->references('id')->on('clinic_doctors')->onDelete('cascade');
            $table->foreign('appointment_id')->references('id')->on('clinic_appointments')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clinic_medical_records');
        Schema::dropIfExists('clinic_appointments');
        Schema::dropIfExists('clinic_patients');
        Schema::dropIfExists('clinic_doctors');
    }
};
