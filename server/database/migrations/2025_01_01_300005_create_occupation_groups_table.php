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
        Schema::create('occupation_groups', function (Blueprint $table) {
            $table->id();
            $table->string('code',16);
            $table->string('occupation_code',16);
            $table->string('occupation_name',55);
            $table->string('group_code',3);
            $table->string('group_name',255);
            $table->string('sub_group_code',3);
            $table->string('sub_group_name',255);
            $table->string('pure_code',3);
            $table->string('pure_name',255);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('occupation_groups');
    }
};
