<?php

use App\Models\Location;
use App\Models\Order;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->string('country',64);
            $table->string('region',64);
            $table->string('state', 64);
            $table->string('city',64);
            $table->time('delivery_time');
            $table->string('street',64)->nullable();
            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};
