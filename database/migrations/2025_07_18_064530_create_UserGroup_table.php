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
        Schema::create('UserGroup', function (Blueprint $table) {
            $table->comment('Tabel User Group ini merupakan nama tabel dari Group seperti Safety Advisor dan Pengawas K3');
            $table->string('Name', 50)->primary()->comment('Nama Group');

            $table->unique(['Name'], 'user_group_name_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('UserGroup');
    }
};
