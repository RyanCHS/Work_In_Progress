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
        Schema::create('InspectionTemplate', function (Blueprint $table) {
            $table->comment('Form Content ini merupakan semua Formulir untuk diinputkan');
            $table->char('TemplateId', 36)->index('formcontent_contentid_idx')->comment('Template Id
');
            $table->dateTime('TemplateDate')->nullable()->comment('Tanggal pembuatan template');
            $table->json('Content')->nullable()->comment('Ini semua isi kontent berupa json');
            $table->string('Description')->nullable()->comment('Deskripsi');
            $table->boolean('IsActivated')->nullable()->comment('Mengaktifkan status kontent');
            $table->char('CreatedBy', 36)->nullable()->index('createdby')->comment('Siapa yang buat?');

            $table->primary(['TemplateId']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('InspectionTemplate');
    }
};
