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
        Schema::table('WorkInProgress', function (Blueprint $table) {
            $table->foreign(['InspectionId'], 'WorkInProgress_ibfk_1')->references(['InspectionId'])->on('Inspection')->onUpdate('no action')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('WorkInProgress', function (Blueprint $table) {
            $table->dropForeign('WorkInProgress_ibfk_1');
        });
    }
};
