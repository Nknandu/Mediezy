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
        Schema::create('docteravaliblity', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('docter_id')->nullable()->default(0);
            $table->string('hospital_Name');
            $table->string('startingTime');
            $table->string('endingTime');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('docteravaliblity');
    }
};
