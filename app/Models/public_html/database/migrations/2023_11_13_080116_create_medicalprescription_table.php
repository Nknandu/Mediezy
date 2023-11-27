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
        Schema::create('medicalprescription', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->nullable()->default(0);
            $table->bigInteger('docter_id')->nullable()->default(0);
            $table->string('medicineName')->nullable();
            $table->string('Dosage')->nullable();
            $table->string('NoOfDays')->nullable();
            $table->bigInteger('MorningBF')->nullable()->default(0);
            $table->bigInteger('MorningAF')->nullable()->default(0);
            $table->bigInteger('Noon')->nullable()->default(0);
            $table->bigInteger('night')->nullable()->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medicalprescription');
    }
};
