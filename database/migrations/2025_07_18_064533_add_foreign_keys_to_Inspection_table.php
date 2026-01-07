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
        Schema::table('Inspection', function (Blueprint $table) {
            $table->foreign(['CreatedBy'], 'Inspection_ibfk_1')->references(['UserId'])->on('UserDetail')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('Inspection', function (Blueprint $table) {
            $table->dropForeign('Inspection_ibfk_1');
        });
    }
};
