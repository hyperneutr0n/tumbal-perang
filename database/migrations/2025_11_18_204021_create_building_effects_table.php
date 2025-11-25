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
        Schema::create('building_effects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('building_id')
                ->constrained('buildings')
                ->onDelete('cascade');
            $table->string('key', 100);
            $table->string('value');
            $table->enum('data_type', ['integer', 'float', 'string', 'boolean'])
                ->default('integer');
            $table->text('description')->nullable();
            $table->timestamps();

            $table->unique(['building_type_id', 'key']);
            $table->index('building_type_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('building_effects');
    }
};
