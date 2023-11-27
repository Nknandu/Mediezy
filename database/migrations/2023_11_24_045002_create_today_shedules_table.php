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
        Schema::create('today_shedules', function (Blueprint $table) {
            $table->id();
            $table->integer('docter_id');
            $table->integer('hospital_id');
            $table->date('date')->nullable();
            $table->string('delay_time')->nullable();
            $table->bigInteger('delay_type')->nullable();
            $table->longText('tokens')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('today_shedules');
    }
};
