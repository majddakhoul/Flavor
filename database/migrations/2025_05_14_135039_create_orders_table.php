<?php

use App\Models\Location;
use App\Models\Reservation;
use App\Models\Table;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('notes')->nullable();
            $table->enum('order_type' ,['Delivery' , 'Reservation' , 'Takeaway']);
            $table->enum('status', ['Pending', 'Completed', 'Cancelled' , 'Confirmed'])->default('Pending');
            $table->timestamps();
            $table->date('dated_at');
            $table->softDeletes();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->foreign('customer_id')->references('id')->on('customers')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('employee_id')->nullable();
            $table->foreign('employee_id')->references('id')->on('employees');
            $table->foreignIdFor(Reservation::class)->nullable();
            $table->foreignIdFor(Location::class)->nullable();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
