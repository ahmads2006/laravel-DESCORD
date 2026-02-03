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
        Schema::create('discord_members', function (Blueprint $table) {
            $table->id();
            $table->string('discord_id')->unique();
            $table->string('username');
            $table->string('global_name')->nullable();
            $table->json('roles')->nullable(); // Stores array of role IDs
            $table->string('avatar')->nullable();
            $table->timestamp('joined_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discord_members');
    }
};
