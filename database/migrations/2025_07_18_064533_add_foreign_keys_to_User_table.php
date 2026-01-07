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
        Schema::table('User', function (Blueprint $table) {
            $table->foreign(['UserGroup'], 'User_ibfk_1')->references(['Name'])->on('UserGroup')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['UserId'], 'User_ibfk_2')->references(['UserId'])->on('UserDetail')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('User', function (Blueprint $table) {
            $table->dropForeign('User_ibfk_1');
            $table->dropForeign('User_ibfk_2');
        });
    }
};
