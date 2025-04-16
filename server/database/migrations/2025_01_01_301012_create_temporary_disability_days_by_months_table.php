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
        Schema::create('temporary_disability_days_by_months', function (Blueprint $table) {
            $table->id();
            $table->string('year'); // Yıl
            $table->unsignedBigInteger('month_id'); // İlgili Ay
            $table->boolean('gender'); // Cinsiyet
            $table->boolean('is_outpatient'); // Ayakta
            $table->integer('one_day_unfit'); // 1 gün iş göremez
            $table->integer('two_days_unfit'); // 2 gün iş göremez
            $table->integer('three_days_unfit'); // 3 gün iş göremez
            $table->integer('four_days_unfit'); // 4 gün iş göremez
            $table->integer('five_or_more_days_unfit'); // 5 gün iş göremez
            $table->timestamps();

            $table->foreign('month_id')->references('id')->on('months')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('temporary_disability_days_by_months');
    }
};
