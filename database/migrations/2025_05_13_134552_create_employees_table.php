<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('national_id')->unique();
            $table->enum('position',['Manager','Chef','Waiter','Security' , 'Delivery']);
            $table->bigInteger('salary');
            $table->integer('bonus')->nullable();
            $table->string('notes')->nullable();
            $table->date('hire_date');
            $table->date('birth_date');
            $table->timestamps();
            $table->unsignedBigInteger('user_id')->unique();
            $table->foreign('user_id')->references('id')->on('users')->constrained()->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
