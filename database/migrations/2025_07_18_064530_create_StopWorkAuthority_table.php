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
        Schema::create('StopWorkAuthority', function (Blueprint $table) {
            $table->comment('Form input ini dibuat dari formulir dari tabel FormContent');
            $table->char('SWAId', 36)->primary()->comment('SWA Id');
            $table->char('WIPId', 36)->nullable()->index('wipid')->comment('WIP Id');
            $table->dateTime('SWADate')->nullable()->comment('Tanggal input data');
            $table->json('InputInspection')->nullable()->comment('Isi dari form inspeksi');
            $table->boolean('IsDraft')->default(false);
            $table->boolean('Status')->nullable()->comment('Status Comply dan Safe');
            $table->boolean('IsWIP')->default(false);
            $table->boolean('RepairSWA')->default(false)->comment('Perbaikan SWA Status');
            $table->boolean('DoRepairSWA')->nullable()->comment('Repair SWA dari Pengawas K3');
            $table->char('ValidatedBy', 36)->nullable()->index('verificationby')->comment('Form ini di validasi oleh user siapa?');
            $table->dateTime('ValidatedAt')->nullable();
            $table->char('SendTo', 36)->nullable()->comment('Perbaikan SWA di kirim ke?');
            $table->char('CreatedBy', 36)->nullable()->index('createdby')->comment('Form ini dibuat oleh siapa');

            $table->unique(['SWAId'], 'swaid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('StopWorkAuthority');
    }
};
