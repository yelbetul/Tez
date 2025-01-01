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
        Schema::create('work_accident_frequency_and_weight_rates', function (Blueprint $table) {
            $table->id();
            $table->string('period',100);
            $table->integer('accidents_incurred');
            $table->integer('total_contribution_days');
            $table->integer('accident_frequency_rate_one_million_hours');
            $table->integer('accident_frequency_rate_one_hundred_person');
            $table->integer('temporary_unfit_for_work');
            $table->integer('permanent_unfit_for_work');
            $table->integer('fatal_case_count');
            $table->integer('accident_severity_rate_day');
            $table->integer('accident_severity_rate_hour');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_accident_frequency_and_weight_rates');
    }
};
