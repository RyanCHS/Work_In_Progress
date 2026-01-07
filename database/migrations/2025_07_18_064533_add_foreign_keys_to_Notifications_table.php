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
        Schema::table('Notifications', function (Blueprint $table) {
            $table->foreign(['UserId'], 'Notifications_ibfk_1')->references(['UserId'])->on('UserDetail')->onUpdate('restrict')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('Notifications', function (Blueprint $table) {
            $table->dropForeign('Notifications_ibfk_1');
        });
    }
};
