<?php

use App\Models\Order;
use App\Models\Reservation;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->string('reservation_code', 8)->unique();
            $table->enum('status', ['Pending', 'Confirmed', 'Cancelled', 'Completed'])->default('Pending');
            $table->tinyInteger('party_size')->default(1);
            $table->enum('type', ['Locally', 'Application']);
            $table->text('special_requests')->nullable();
            $table->date('date')->nullable();
            $table->timestamps();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->foreign('customer_id')->references('id')->on('customers')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('employee_id')->nullable();
            $table->foreign('employee_id')->references('id')->on('employees');
            $table->foreignIdFor(Order::class)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
