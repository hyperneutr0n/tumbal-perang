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
        Schema::create('tribe_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tribe_id')
                ->constrained('tribes')
                ->onDelete('cascade');

            $table->foreignId('stat_type_id')
                ->constrained('stat_types')
                ->onDelete('cascade');

            $table->integer('value')->default(0);
            $table->timestamps();
            
            $table->unique(['tribe_id', 'stat_type_id']);
            $table->index('tribe_id');
            $table->index('stat_type_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tribe_stats');
    }
};
