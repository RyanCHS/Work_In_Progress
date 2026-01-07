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
        Schema::create('Notifications', function (Blueprint $table) {
            $table->char('NotifId', 36)->unique('notifid');
            $table->char('UserId', 36)->nullable()->index('notifications_ibfk_1');
            $table->string('Title')->nullable();
            $table->string('Message', 500)->nullable();
            $table->string('Category', 20)->nullable();
            $table->dateTime('CreatedAt')->nullable();
            $table->char('CreatedBy', 36);

            $table->primary(['NotifId']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Notifications');
    }
};
