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
        Schema::create('WorkInProgress', function (Blueprint $table) {
            $table->comment('Form input ini dibuat dari formulir dari tabel FormContent');
            $table->char('WIPId', 36)->primary()->comment('Form Input Id');
            $table->char('InspectionId', 36)->nullable()->index('inspectionid')->comment('Relasi Inspection');
            $table->boolean('Status')->nullable()->comment('Status Comply dan Safe');

            $table->unique(['WIPId'], 'wipid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('WorkInProgress');
    }
};
