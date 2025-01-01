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
        Schema::create('occ_disease_fatalities_by_occupations', function (Blueprint $table) {
            $table->id();
            $table->string('year'); // Yıl
            $table->unsignedBigInteger('group_id'); // İlgili Meslek Grubu
            $table->boolean('gender'); // Cinsiyet
            $table->integer('occ_disease_cases'); // Meslek Hastalığına Yakalanan
            $table->integer('occ_disease_fatalities'); // Meslek Hastalığı Sonucu Ölen
            $table->timestamps();

            $table->foreign('group_id')->references('id')->on('occupation_groups')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('occ_disease_fatalities_by_occupations');
    }
};
