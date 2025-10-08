<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasColumn('users', 'mentor_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropForeign(['mentor_id']);
                $table->dropColumn('mentor_id');
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasColumn('users', 'mentor_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->unsignedBigInteger('mentor_id')->nullable()->after('is_mentor');
                $table->foreign('mentor_id')->references('id')->on('users')->nullOnDelete();
            });
        }
    }
};


