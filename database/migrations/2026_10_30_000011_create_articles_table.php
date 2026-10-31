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
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->string('title', 255);
            $table->string('slug', 255)->unique('articles_slug_unique');
            $table->text('excerpt')->nullable();
            $table->longText('content');
            $table->string('featured_image', 500)->nullable();
            $table->string('image_alt', 255)->nullable();
            $table->string('meta_title', 255)->nullable();
            $table->string('meta_description', 500)->nullable();
            $table->string('author_name', 100)->default('Tim Editorial LokaTaut');
            $table->unsignedSmallInteger('reading_time')->default(5);
            $table->enum('status', ['draft', 'published', 'scheduled'])->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'published_at'], 'articles_status_published_at_index');
            $table->index('category_id', 'articles_category_id_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};

