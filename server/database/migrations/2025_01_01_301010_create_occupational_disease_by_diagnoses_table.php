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
        Schema::create('occupational_disease_by_diagnoses', function (Blueprint $table) {
            $table->id();
            $table->string('year');
            $table->unsignedBigInteger('diagnosis_code');
            $table->boolean('gender');
            $table->integer('occupational_disease_cases'); 
            $table->timestamps();

            $table->foreign('diagnosis_code')->references('id')->on('diagnosis_groups')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('occupational_disease_by_diagnoses');
    }
};
