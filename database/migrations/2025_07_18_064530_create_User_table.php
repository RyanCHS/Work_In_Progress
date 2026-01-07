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
        Schema::create('User', function (Blueprint $table) {
            $table->comment('Tabel User ini untuk login');
            $table->char('UserId', 36)->index('user_userid_fkey')->comment('User Id dari tabel UserDetail');
            $table->string('Username', 50)->primary()->comment('Username untuk login');
            $table->string('Password')->nullable()->comment('Password');
            $table->string('UserGroup')->nullable()->index('user_usergroup_fkey')->comment('UserGroup diambil dari tabel UserGroup');
            $table->boolean('IsActivated')->nullable()->comment('Mengaktifkan User');

            $table->unique(['Username'], 'user_username_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('User');
    }
};
