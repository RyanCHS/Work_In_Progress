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
        Schema::create('Inspection', function (Blueprint $table) {
            $table->comment('Form input ini dibuat dari formulir dari tabel FormContent');
            $table->char('InspectionId', 36)->unique('inspectionid')->comment('Form Input Id');
            $table->string('SWAPIC')->nullable()->comment('Pemegang SWA');
            $table->string('Location')->nullable()->comment('Lokasi Pekerjaan');
            $table->string('WorkType')->nullable()->comment('Tipe Pekerjaan');
            $table->json('InspectionForm')->nullable()->comment('Template Form');
            $table->boolean('IsDraft')->nullable()->comment('Status Draft atau Final');
            $table->char('CreatedBy', 36)->nullable()->index('createdby')->comment('Form ini dibuat oleh user siapa?');
            $table->date('CreatedAt')->nullable()->comment('Tanggal Pembuatan Data');

            $table->primary(['InspectionId']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Inspection');
    }
};
