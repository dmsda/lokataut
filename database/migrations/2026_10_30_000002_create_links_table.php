<?php

declare(strict_types=1);

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
        Schema::create('links', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 50)->unique('links_slug_unique');
            $table->text('destination_url');
            $table->string('title', 255)->nullable();
            $table->string('source_website', 255)->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->unsignedSmallInteger('countdown_seconds')->default(10);
            $table->timestamp('expires_at')->nullable();
            $table->unsignedInteger('total_visits')->default(0);
            $table->unsignedInteger('total_completions')->default(0);
            $table->string('meta_description', 255)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'deleted_at'], 'links_status_deleted_at_index');
            $table->index('source_website', 'links_source_website_index');
            $table->index('expires_at', 'links_expires_at_index');
            $table->index('created_at', 'links_created_at_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('links');
    }
};

