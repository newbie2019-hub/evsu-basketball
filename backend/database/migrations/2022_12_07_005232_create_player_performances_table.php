<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('player_performances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('free_throws_attempted');
            $table->string('free_throws_made');
            $table->string('field_goals_attempted');
            $table->string('field_goals_made');
            $table->string('three_pointers_attempted');
            $table->string('three_pointers_made');
            $table->string('games_won');
            $table->string('games_lost');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('player_performances');
    }
};
