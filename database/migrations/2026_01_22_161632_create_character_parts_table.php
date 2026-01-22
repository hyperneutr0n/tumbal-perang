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
        Schema::create('character_parts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tribe_id')
                ->constrained('tribes')
                ->onDelete('cascade');
            $table->enum('part_type', ['head', 'body', 'arm', 'leg']);
            $table->string('name');
            $table->string('image_path')->nullable();
            $table->boolean('is_default')->default(false);
            $table->timestamps();

            $table->index(['tribe_id', 'part_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('character_parts');
    }
};
