<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('meal_order', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('quantity')->default(1);
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('meal_id');
            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders_meals');
    }
};
