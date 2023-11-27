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
        Schema::create('token_booking', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('BookedPerson_id')->nullable();
            $table->bigInteger('doctor_id')->default('0');
            $table->string('PatientName')->nullable();
            $table->string('gender')->nullable();
            $table->string('age')->nullable();
            $table->string('MobileNo')->nullable();
            $table->string('Appoinmentfor_id')->default('0');
            $table->date('date')->nullable();
            $table->string('TokenNumber')->nullable();
            $table->string('TokenTime')->nullable();
            $table->string('Bookingtime')->nullable();
            $table->bigInteger('Is_checkIn')->default('0');
            $table->bigInteger('Is_completed')->default('0');
            $table->bigInteger('Is_canceled')->default('0');
            $table->string('whenitstart')->nullable();
            $table->string('whenitcomes')->nullable();
            $table->string('regularmedicine')->nullable();
            $table->string('amount')->nullable();
            $table->string('paymentmethod')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('token_booking');
    }
};
