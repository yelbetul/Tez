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
        Schema::create('fatal_work_accidents_by_provinces', function (Blueprint $table) {
            $table->id();
            $table->string('year'); // Yıl
            $table->unsignedBigInteger('province_id'); // İlgili Şehir
            $table->boolean('gender'); // Cinsiyet
            $table->integer('work_accident_fatalities'); // İş kazası sonucu ölenler
            $table->tinyInteger('occupational_disease_fatalities'); // Meslek hastalığı sonucu ölenler
            $table->timestamps();

            $table->foreign('province_id')->references('id')->on('province_codes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fatal_work_accidents_by_provinces');
    }
};
