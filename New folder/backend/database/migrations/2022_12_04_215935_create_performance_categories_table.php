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
        Schema::create('performance_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('per_eval_id')->constrained('performance_evaluations')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('evaluation_categories_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('score')->default(0);
            $table->foreignId('max_score')->default(5);
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
        Schema::dropIfExists('performance_categories');
    }
};
