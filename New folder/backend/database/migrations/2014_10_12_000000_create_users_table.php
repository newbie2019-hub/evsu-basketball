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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('photo')->nullable();
            $table->string('first_name', 120);
            $table->string('middle_name', 120)->nullable();
            $table->string('last_name', 120);
            $table->string('year', 20)->nullable();
            $table->string('gender', 15);
            $table->string('height', 80)->nullable();
            $table->string('weight', 80)->nullable();
            $table->string('date_of_birth', 80)->nullable();
            $table->string('course', 150)->nullable();
            $table->string('contact', 150)->nullable();
            $table->string('address')->nullable();
            $table->string('position', 150)->nullable();
            $table->string('email')->unique();
            $table->string('user_type', 25)->default('user');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
};
