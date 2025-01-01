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
        Schema::create('fatal_work_accidents_by_months', function (Blueprint $table) {
            $table->id();
            $table->string('year'); // Yıl
            $table->unsignedBigInteger('month_id'); // İlgili Ay
            $table->boolean('gender'); // Cinsiyet
            $table->integer('work_accident_fatalities'); // İş kazası sonucu ölenler
            $table->tinyInteger('occupational_disease_fatalities'); // Meslek hastalığı sonucu ölenler
            $table->timestamps();

            $table->foreign('month_id')->references('id')->on('months')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fatal_work_accidents_by_months');
    }
};
