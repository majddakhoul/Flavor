<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('offers', function (Blueprint $table) {
            $table->unsignedBigInteger('picture_id')->nullable()->unique()->after('end_date');
            $table->foreign('picture_id')->references('id')->on('pictures');
        });
    }

    public function down(): void
    {
        Schema::table('offers', function (Blueprint $table) {
            $table->dropForeign(['picture_id']);
            $table->dropColumn('picture_id');
        });
    }
};
