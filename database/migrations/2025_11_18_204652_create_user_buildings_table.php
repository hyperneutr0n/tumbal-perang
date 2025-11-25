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
        Schema::create('user_buildings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');

            $table->foreignId('building_id')
                ->constrained('buildings')
                ->onDelete('restrict');

            $table->timestamp('built_at');
            $table->integer('level')->default(1);
            $table->timestamp('built_at');
            $table->timestamps();

            $table->index(['user_id', 'building_type_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_buildings');
    }
};
