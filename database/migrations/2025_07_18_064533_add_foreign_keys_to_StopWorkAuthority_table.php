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
        Schema::table('StopWorkAuthority', function (Blueprint $table) {
            $table->foreign(['WIPId'], 'StopWorkAuthority_ibfk_1')->references(['WIPId'])->on('WorkInProgress')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['ValidatedBy'], 'StopWorkAuthority_ibfk_2')->references(['UserId'])->on('UserDetail')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['CreatedBy'], 'StopWorkAuthority_ibfk_3')->references(['UserId'])->on('UserDetail')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('StopWorkAuthority', function (Blueprint $table) {
            $table->dropForeign('StopWorkAuthority_ibfk_1');
            $table->dropForeign('StopWorkAuthority_ibfk_2');
            $table->dropForeign('StopWorkAuthority_ibfk_3');
        });
    }
};
