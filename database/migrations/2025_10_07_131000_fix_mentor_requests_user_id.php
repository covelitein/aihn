<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('mentor_requests')) {
            return;
        }

        $hasMentee = Schema::hasColumn('mentor_requests', 'mentee_id');
        $hasUser = Schema::hasColumn('mentor_requests', 'user_id');

        if ($hasMentee && ! $hasUser) {
            Schema::table('mentor_requests', function (Blueprint $table) {
                $table->foreignId('user_id')->nullable()->after('id')->constrained('users')->cascadeOnDelete();
            });

            // Copy data from mentee_id to user_id
            DB::statement('UPDATE mentor_requests SET user_id = mentee_id WHERE user_id IS NULL');

            // Drop mentee_id and make user_id required, ensure unique constraint on (user_id, mentor_id)
            Schema::table('mentor_requests', function (Blueprint $table) {
                // Best-effort: drop possible old unique index
                try { $table->dropUnique(['mentee_id', 'mentor_id']); } catch (\Throwable $e) {}
                try { $table->dropForeign(['mentee_id']); } catch (\Throwable $e) {}
                $table->dropColumn('mentee_id');
            });

            Schema::table('mentor_requests', function (Blueprint $table) {
                $table->unsignedBigInteger('user_id')->nullable(false)->change();
                $table->unique(['user_id', 'mentor_id']);
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('mentor_requests')) {
            return;
        }

        $hasUser = Schema::hasColumn('mentor_requests', 'user_id');
        $hasMentee = Schema::hasColumn('mentor_requests', 'mentee_id');

        if ($hasUser && ! $hasMentee) {
            Schema::table('mentor_requests', function (Blueprint $table) {
                $table->unsignedBigInteger('mentee_id')->nullable()->after('id');
                $table->foreign('mentee_id')->references('id')->on('users')->cascadeOnDelete();
            });

            DB::statement('UPDATE mentor_requests SET mentee_id = user_id WHERE mentee_id IS NULL');

            Schema::table('mentor_requests', function (Blueprint $table) {
                try { $table->dropUnique(['user_id', 'mentor_id']); } catch (\Throwable $e) {}
                try { $table->dropForeign(['user_id']); } catch (\Throwable $e) {}
                $table->dropColumn('user_id');
                $table->unique(['mentee_id', 'mentor_id']);
            });
        }
    }
};


