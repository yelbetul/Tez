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
        Schema::create('accidents_and_fatalities_by_occupations', function (Blueprint $table) {
            $table->id();
            $table->string('year'); // Yıl
            $table->unsignedBigInteger('group_id'); // İlgili Meslek Grubu
            $table->boolean('gender'); // Cinsiyet
            $table->integer('works_on_accident_day'); // Kaza günü çalışır
            $table->integer('unfit_on_accident_day'); // Kaza günü iş göremez
            $table->integer('two_days_unfit'); // 2 gün iş göremez
            $table->integer('three_days_unfit'); // 3 gün iş göremez
            $table->integer('four_days_unfit'); // 4 gün iş göremez
            $table->integer('five_or_more_days_unfit'); // 5 gün iş göremez
            $table->integer('fatalities'); // İş Kazası Sonucu Ölen
            $table->timestamps();

            $table->foreign('group_id')->references('id')->on('occupation_groups')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accidents_fatalities_by_occupations');
    }
};
