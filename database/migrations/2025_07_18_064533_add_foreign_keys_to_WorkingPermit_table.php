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
        Schema::table('WorkingPermit', function (Blueprint $table) {
            $table->foreign(['VerificationBy'], 'WorkingPermit_ibfk_1')->references(['UserId'])->on('UserDetail')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['RequestBy'], 'WorkingPermit_ibfk_2')->references(['UserId'])->on('UserDetail')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['InspectionId'], 'WorkingPermit_ibfk_3')->references(['InspectionId'])->on('Inspection')->onUpdate('restrict')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('WorkingPermit', function (Blueprint $table) {
            $table->dropForeign('WorkingPermit_ibfk_1');
            $table->dropForeign('WorkingPermit_ibfk_2');
            $table->dropForeign('WorkingPermit_ibfk_3');
        });
    }
};
