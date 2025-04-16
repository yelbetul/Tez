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
            $table->boolean('is_outpatient'); // Ayakta
            $table->integer('one_day_unfit'); // 1 gün iş göremez
            $table->integer('two_days_unfit'); // 2 gün iş göremez
            $table->integer('three_days_unfit'); // 3 gün iş göremez
            $table->integer('four_days_unfit'); // 4 gün iş göremez
            $table->integer('five_or_more_days_unfit'); // 5 gün iş göremez
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
