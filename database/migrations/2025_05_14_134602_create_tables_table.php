<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tables', function (Blueprint $table) {
            $table->id();
            $table->string('table_number',3)->unique();
            $table->tinyInteger('capacity');
            $table->enum('location',['Indoor','Outdoor','VIP','Roof'])->default('Indoor');
            $table->boolean('is_active')->default(true);
            $table->integer('price_per_hour')->default('0');
            $table->string('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tables');
    }
};
