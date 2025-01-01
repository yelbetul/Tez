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
        Schema::create('work_environments', function (Blueprint $table) {
            $table->id();
            $table->string('environment_code');
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
        Schema::dropIfExists('work_environments');
    }
};
