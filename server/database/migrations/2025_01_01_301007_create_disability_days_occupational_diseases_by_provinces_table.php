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
        Schema::create('disability_days_occupational_diseases_by_provinces', function (Blueprint $table) {
            $table->id();
            $table->string('year'); // Yıl
            $table->unsignedBigInteger('province_id'); // İlgili Şehir
            $table->boolean('gender'); // Cinsiyet
            $table->integer('outpatient'); // Ayakta
            $table->integer('inpatient'); // Yatarak
            $table->timestamps();

            $table->foreign('province_id', 'province_id_fk')->references('id')->on('province_codes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('disability_days_occupational_diseases_by_provinces');
    }
};
