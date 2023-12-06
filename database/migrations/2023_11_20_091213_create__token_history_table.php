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
        Schema::create('token_history', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('docter_id')->default(0);
            $table->string('session_title')->nullable();
            $table->date('TokenUpdateddate')->nullable();
            $table->bigInteger('hospital_Id')->default(0);
            $table->string('startingTime')->nullable();
            $table->string('endingTime')->nullable();
            $table->string('TokenCount')->nullable();
            $table->string('timeduration')->nullable();
            $table->string('format')->nullable();
            $table->longText('tokens')->nullable();
            $table->string('scheduleupto')->nullable();
            $table->text('selecteddays')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('token_history');
    }
};
