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
        Schema::create('patient_profiles', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();

            $table->enum('gender', ['male','female','other'])->nullable();
            $table->date('dob')->nullable();

            $table->string('blood_type')->nullable(); // A+, O-, ...
            $table->integer('height_cm')->nullable();
            $table->integer('weight_kg')->nullable();

            $table->text('allergies')->nullable();
            $table->text('chronic_diseases')->nullable();

            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient_profiles');
    }
};
