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
        Schema::table('medicalprescription', function (Blueprint $table) {
            $table->dropColumn('MorningBF');
            $table->dropColumn('MorningAF');
            $table->integer('token_id');
            $table->integer('morning')->default(0);
            $table->integer('type')->comment('1 - before , 2 - after');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('medicalprescription', function (Blueprint $table) {
            $table->string('MorningBF');
            $table->string('MorningAF');
            $table->dropColumn('token_id');
            $table->dropColumn('morning');
            $table->dropColumn('type');
        });
    }
};
