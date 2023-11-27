<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('daily_token_deatils', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('docter_id')->nullable();
            $table->date('date')->nullable();
            $table->string('DelayTime')->nullable();
            $table->bigInteger('delayStatus')->nullable();
            $table->longText('tokens')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_token_deatils');
    }
};
