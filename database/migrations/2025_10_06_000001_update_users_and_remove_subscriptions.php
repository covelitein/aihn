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
        // Update users table: add mentor and renewal fields; remove subscription-related columns/constraints
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'is_mentor')) {
                $table->boolean('is_mentor')->default(false)->after('is_admin');
            }
            if (!Schema::hasColumn('users', 'renewal_date')) {
                $table->timestamp('renewal_date')->nullable()->after('email_verified_at');
            }

            if (Schema::hasColumn('users', 'current_subscription_id')) {
                $table->dropForeign(['current_subscription_id']);
            }

            $columnsToDrop = [
                'subscription_expires_at',
                'is_subscribed',
                'subscription_status',
                'current_subscription_id',
                'last_subscription_at',
                'total_subscriptions'
            ];

            foreach ($columnsToDrop as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        // Drop subscription-related tables if they exist
        if (Schema::hasTable('subscription_applications')) {
            Schema::drop('subscription_applications');
        }
        if (Schema::hasTable('subscription_plans')) {
            Schema::drop('subscription_plans');
        }

        // Optional: content table cleanup for accessible_plans
        if (Schema::hasColumn('content', 'accessible_plans')) {
            Schema::table('content', function (Blueprint $table) {
                $table->dropColumn('accessible_plans');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'is_mentor')) {
                $table->dropColumn('is_mentor');
            }
            if (Schema::hasColumn('users', 'renewal_date')) {
                $table->dropColumn('renewal_date');
            }

            // We won't recreate subscription fields/tables in down for simplicity
        });

        // We won't recreate dropped tables/columns here to avoid data inconsistency on rollback.
    }
};


