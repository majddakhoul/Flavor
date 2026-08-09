<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('first_name',32);
            $table->string('last_name',32);
            $table->string('email',64)->unique();
            $table->string('password');
            $table->string('phone',15)->unique()->nullable();
            $table->enum('gender',['Male','Female']);
            $table->boolean('status')->default(true);
            $table->rememberToken();
            $table->enum('user_type',['Manager','Customer','Employee'])->default('Customer');
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamps();
            $table->unsignedBigInteger('location_id')->nullable();
            $table->foreign('location_id')->references('id')->on('locations');

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
