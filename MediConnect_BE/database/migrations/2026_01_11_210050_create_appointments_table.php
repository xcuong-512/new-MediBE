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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();

            $table->string('appointment_code')->unique();
            $table->foreignId('patient_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('doctor_profile_id')->constrained('doctor_profiles')->cascadeOnDelete();
            $table->foreignId('clinic_branch_id')->nullable()->constrained('clinic_branches')->nullOnDelete();
            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');
            $table->enum('type', ['online','offline'])->default('offline');
            $table->enum('status', [
                'pending',
                'confirmed',
                'checkin',
                'completed',
                'cancelled',
                'no_show'
            ])->default('pending');

            $table->text('symptom_note')->nullable();
            $table->text('doctor_note')->nullable();

            $table->timestamps();

            $table->unique(['doctor_profile_id','date','start_time']);
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
