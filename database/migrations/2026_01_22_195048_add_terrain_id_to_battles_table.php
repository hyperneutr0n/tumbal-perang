<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('battles', function (Blueprint $table) {
            $table->foreignId('terrain_id')->nullable()->constrained('terrain')->onDelete('set null');
            $table->index('terrain_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('battles', function (Blueprint $table) {
            $table->dropForeign(['terrain_id']);
            $table->dropIndex(['terrain_id']);
            $table->dropColumn('terrain_id');
        });
    }
};
