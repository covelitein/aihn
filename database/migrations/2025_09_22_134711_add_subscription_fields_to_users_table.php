<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('subscription_expires_at')->nullable();
            $table->boolean('is_subscribed')->default(false);
            $table->enum('subscription_status', ['active', 'expired', 'cancelled', 'pending'])->default('pending');
            $table->unsignedBigInteger('current_subscription_id')->nullable();
            $table->timestamp('last_subscription_at')->nullable();
            $table->integer('total_subscriptions')->default(0);

            $table->foreign('current_subscription_id')
                ->references('id')
                ->on('subscription_applications')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'subscription_expires_at',
                'is_subscribed',
                'subscription_status',
                'current_subscription_id',
                'last_subscription_at',
                'total_subscriptions'
            ]);
        });
    }
};
