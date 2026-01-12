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
        Schema::create('doctor_slots', function (Blueprint $table) {
            $table->id();

            $table->foreignId('doctor_profile_id')->constrained('doctor_profiles')->cascadeOnDelete();
            $table->foreignId('clinic_branch_id')->nullable()->constrained('clinic_branches')->nullOnDelete();

            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');

            $table->enum('status', ['available','booked','locked'])->default('available');

            $table->foreignId('generated_from_working_hour_id')
                ->nullable()
                ->constrained('doctor_working_hours')
                ->nullOnDelete();

            $table->timestamps();

            $table->unique(['doctor_profile_id','date','start_time']);
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctor_slots');
    }
};
