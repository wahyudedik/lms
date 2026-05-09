<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('push_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('endpoint');
            $table->text('p256dh');
            $table->string('auth');
            $table->timestamps();
        });

        // Add unique key on (user_id, endpoint(500)) using raw statement
        // because Blueprint does not support prefix length on text columns directly.
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement('ALTER TABLE push_subscriptions ADD UNIQUE KEY unique_user_endpoint (user_id, endpoint(500))');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('push_subscriptions');
    }
};
