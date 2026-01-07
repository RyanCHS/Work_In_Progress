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
        Schema::create('UserDetail', function (Blueprint $table) {
            $table->comment('Tabel UserDetail ini berisi semua data user lebih detail');
            $table->char('UserId', 36)->primary()->comment('User Id');
            $table->string('Name', 100)->nullable()->comment('Nama User');
            $table->string('Email', 100)->nullable()->comment('Email User');
            $table->string('Address', 100)->nullable()->comment('Alamat User');
            $table->string('City', 50)->nullable()->comment('Kota User');
            $table->string('Phone', 20)->nullable()->comment('Phone User');

            $table->unique(['UserId'], 'userdetail_userid_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('UserDetail');
    }
};
