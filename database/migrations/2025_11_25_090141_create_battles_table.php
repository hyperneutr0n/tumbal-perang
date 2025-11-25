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
        Schema::create('battles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attacker_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('defender_id')->constrained('users')->onDelete('cascade');
            $table->bigInteger('attacker_troops')->default(0);
            $table->bigInteger('defender_troops')->default(0);
            $table->bigInteger('attacker_power')->default(0);
            $table->bigInteger('defender_power')->default(0);
            $table->enum('result', ['attacker_win', 'defender_win']);
            $table->bigInteger('gold_stolen')->default(0);
            $table->bigInteger('attacker_troops_lost')->default(0);
            $table->bigInteger('defender_troops_lost')->default(0);
            $table->timestamps();

            $table->index('attacker_id');
            $table->index('defender_id');
            $table->index('battle_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('battles');
    }
};
