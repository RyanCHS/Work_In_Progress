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
        Schema::create('FileInput', function (Blueprint $table) {
            $table->comment('Tabel Ini nanti akan berhubungan dengan Form Input');
            $table->char('FileId', 36)->unique('fileid')->comment('File Id');
            $table->char('WPId', 36)->nullable()->index('relationid')->comment('Relasi WP');
            $table->char('SWAId', 36)->nullable()->index('fileinput_ibfk_3')->comment('Relasi SWA');
            $table->string('Keytag')->nullable()->comment('tag key dari json');
            $table->boolean('Status')->nullable()->comment('Status Upload');
            $table->date('UploadAt')->nullable()->comment('Tanggal Upload');
            $table->char('UploadedBy', 36)->nullable()->index('uploadedby')->comment('Di Upload Oleh User siapa');

            $table->primary(['FileId']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('FileInput');
    }
};
