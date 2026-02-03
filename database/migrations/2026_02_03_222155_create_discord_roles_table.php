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
        Schema::create('discord_roles', function (Blueprint $table) {
            $table->id();
            $table->string('discord_id')->unique();
            $table->string('name');
            $table->integer('color');
            $table->boolean('hoist');
            $table->integer('position');
            $table->string('permissions');
            $table->boolean('managed');
            $table->boolean('mentionable');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discord_roles');
    }
};
