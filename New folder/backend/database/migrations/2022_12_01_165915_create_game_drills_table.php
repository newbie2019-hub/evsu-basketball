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
        Schema::create('game_drills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('drill_category_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('drill');
            $table->string('description');
            $table->text('instructions')->nullable();
            $table->string('hours');
            $table->string('minutes');
            $table->string('seconds');
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
        Schema::dropIfExists('game_drills');
    }
};
