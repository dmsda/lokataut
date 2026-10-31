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
        Schema::create('daily_stats', function (Blueprint $table) {
            $table->id();
            $table->string('aggregate_key', 100);
            $table->foreignId('link_id')->nullable()->constrained('links')->nullOnDelete();
            $table->string('link_slug', 50);
            $table->date('date');
            $table->unsignedInteger('impressions')->default(0);
            $table->unsignedInteger('verified')->default(0);
            $table->unsignedInteger('completions')->default(0);
            $table->unsignedInteger('unique_visitors')->default(0);
            $table->unsignedInteger('desktop_count')->default(0);
            $table->unsignedInteger('mobile_count')->default(0);
            $table->unsignedInteger('tablet_count')->default(0);
            $table->unsignedInteger('bot_count')->default(0);
            $table->json('top_countries')->nullable();
            $table->timestamps();

            $table->unique(['aggregate_key', 'date'], 'daily_stats_key_date_unique');
            $table->index(['link_id', 'date'], 'daily_stats_link_date_index');
            $table->index(['link_slug', 'date'], 'daily_stats_slug_date_index');
            $table->index('date', 'daily_stats_date_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_stats');
    }
};

