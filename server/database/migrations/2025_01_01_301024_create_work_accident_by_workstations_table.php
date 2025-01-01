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
        Schema::create('work_accident_by_workstations', function (Blueprint $table) {
            $table->id();
            $table->string('year'); // Yıl
            $table->unsignedBigInteger('group_id'); // Çalışılan Ortam
            $table->boolean('gender'); // Cinsiyet
            $table->integer('works_on_accident_day'); // Kaza günü çalışır
            $table->integer('unfit_on_accident_day'); // Kaza günü iş göremez
            $table->integer('two_days_unfit'); // 2 gün iş göremez
            $table->integer('three_days_unfit'); // 3 gün iş göremez
            $table->integer('four_days_unfit'); // 4 gün iş göremez
            $table->integer('five_or_more_days_unfit'); // 5 gün iş göremez
            $table->timestamps();

            $table->foreign('group_id')->references('id')->on('workstation_types')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_accident_by_workstations');
    }
};
