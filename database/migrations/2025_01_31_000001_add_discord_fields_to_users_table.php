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
        Schema::table('users', function (Blueprint $table) {
            $table->string('discord_id')->nullable()->unique()->after('id');
            $table->string('discord_username')->nullable()->after('discord_id');
            $table->string('discord_avatar')->nullable()->after('discord_username');
            $table->string('discord_email')->nullable()->after('discord_avatar');
            $table->string('language')->default('en')->after('discord_email');
            $table->string('specialization_role')->nullable()->after('language');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'discord_id',
                'discord_username',
                'discord_avatar',
                'discord_email',
                'language',
                'specialization_role',
            ]);
        });
    }
};
