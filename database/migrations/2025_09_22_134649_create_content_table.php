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
        Schema::create('content', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('type', ['article', 'document', 'video', 'audio', 'file']);
            $table->text('content')->nullable(); // For direct text content
            $table->string('file_path')->nullable(); // For uploaded files
            $table->string('file_original_name')->nullable();
            $table->string('file_mime_type')->nullable();
            $table->integer('file_size')->nullable(); // in bytes
            $table->json('metadata')->nullable(); // For additional type-specific metadata
            $table->unsignedBigInteger('author_id');
            $table->json('accessible_plans'); // Array of plan IDs that can access this content
            $table->boolean('is_published')->default(false);
            $table->integer('view_count')->default(0);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('author_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('content');
    }
};
