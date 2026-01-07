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
        Schema::table('FileInput', function (Blueprint $table) {
            $table->foreign(['WPId'], 'FileInput_ibfk_1')->references(['WPId'])->on('WorkingPermit')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['SWAId'], 'FileInput_ibfk_2')->references(['SWAId'])->on('StopWorkAuthority')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['UploadedBy'], 'FileInput_ibfk_3')->references(['UserId'])->on('UserDetail')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('FileInput', function (Blueprint $table) {
            $table->dropForeign('FileInput_ibfk_1');
            $table->dropForeign('FileInput_ibfk_2');
            $table->dropForeign('FileInput_ibfk_3');
        });
    }
};
