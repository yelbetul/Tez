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
        Schema::create('general_activities', function (Blueprint $table) {
            $table->id();
            $table->string('general_activity_code');
            $table->string('group_code');
            $table->string('group_name');
            $table->string('sub_group_code');
            $table->string('sub_group_name');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('general_activities');
    }
};
