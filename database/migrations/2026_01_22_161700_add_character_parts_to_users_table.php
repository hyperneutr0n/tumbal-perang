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
            $table->foreignId('head_id')
                ->nullable()
                ->after('tribe_id')
                ->constrained('character_parts')
                ->onDelete('set null');
            
            $table->foreignId('body_id')
                ->nullable()
                ->after('head_id')
                ->constrained('character_parts')
                ->onDelete('set null');
            
            $table->foreignId('arm_id')
                ->nullable()
                ->after('body_id')
                ->constrained('character_parts')
                ->onDelete('set null');
            
            $table->foreignId('leg_id')
                ->nullable()
                ->after('arm_id')
                ->constrained('character_parts')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['head_id']);
            $table->dropForeign(['body_id']);
            $table->dropForeign(['arm_id']);
            $table->dropForeign(['leg_id']);
            
            $table->dropColumn(['head_id', 'body_id', 'arm_id', 'leg_id']);
        });
    }
};
