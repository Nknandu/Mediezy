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
        Schema::create('docter', function (Blueprint $table) {
            $table->id();
            $table->string('firstname');
            $table->string('lastname');
            $table->string('docter_image', 100)->nullable();
            $table->string('mobileNo', 50)->nullable();
            $table->string('gender')->default('0');
            $table->string('location', 50)->nullable();
            $table->string('email', 50)->nullable();
            $table->string('specialization_id')->nullable()->default(0);
            $table->string('specification_id')->nullable()->default(0);
            $table->string('subspecification_id')->nullable()->default(0);
            $table->string('about', 200)->nullable();
            $table->string('Services_at', 200)->nullable();
            $table->integer('UserId')->length(11)->default("0");

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('docter');
    }
};
