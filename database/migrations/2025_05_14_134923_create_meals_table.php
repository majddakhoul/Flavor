<?php

use App\Models\Category;
use App\Models\Offer;
use App\Models\Table;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meals', function (Blueprint $table) {
            $table->id();
            $table->string('name',35);
            $table->time('prep_time');
            $table->boolean('is_vegetarian')->default(false);
            $table->integer('percentage');
            $table->string('description')->nullable();
            $table->enum('availability', ['available', 'unavailable'])->default('available');
            $table->unsignedBigInteger('picture_id')->nullable()->unique();
            $table->foreign('picture_id')->references('id')->on('pictures');
            $table->foreignIdFor(Category::class)->nullable()->constrained()->cascadeOnDelete();
            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meals');
    }
};
