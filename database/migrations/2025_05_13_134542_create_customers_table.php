<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->
            enum('allergies',[
                'Cows Milk Allergy','Egg Allergy','Peanut Allergy','Tree Nut Allergy','Fish Allergy',
                'Shellfish Allergy','Wheat Allergy','Soy Allergy','Seed Allergies','Red Meat Allergy',
                'Fruit Allergies','Vegetable Allergies','Spice Allergies'
                ])->nullable();
            $table->string('favorite_categories',32)->nullable();
            $table->boolean('ban')->default(false);            
            $table->date('ban_date')->nullable();            
            $table->timestamps();
            $table->unsignedBigInteger('user_id')->unique();
            $table->foreign('user_id')->references('id')->on('users')->constrained()->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
