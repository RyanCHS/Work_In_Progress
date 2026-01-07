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
        Schema::create('WorkingPermit', function (Blueprint $table) {
            $table->comment('Untuk membuat Working Permit');
            $table->char('WPId', 36)->primary()->comment('Working Permit Id');
            $table->char('InspectionId', 36)->nullable()->index('workingpermit_ibfk_3')->comment('Relasi Inspection');
            $table->dateTime('WPDate')->nullable()->comment('Tanggal input data');
            $table->boolean('Status')->nullable()->comment('Status upload');
            $table->char('SendTo', 36)->nullable()->comment('Send To');
            $table->char('VerificationBy', 36)->nullable()->index('verificationby')->comment('Form ini di verifikasi oleh user siapa?');
            $table->char('RequestBy', 36)->nullable()->index('requestby')->comment('Form ini di request oleh siap?');

            $table->unique(['WPId'], 'wpid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('WorkingPermit');
    }
};
