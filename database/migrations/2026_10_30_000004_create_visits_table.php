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
        Schema::create('visits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('link_id')->nullable()->constrained('links')->nullOnDelete();
            $table->unsignedBigInteger('link_id_snapshot');
            $table->string('link_slug_snapshot', 50);
            $table->foreignId('visitor_session_id')->nullable()->constrained('visitor_sessions')->nullOnDelete();
            $table->enum('event_type', ['impression', 'verified', 'completed', 'error']);
            $table->string('ip_hash', 64);
            $table->enum('device_type', ['desktop', 'mobile', 'tablet', 'bot', 'unknown'])->default('unknown');
            $table->string('browser', 50)->nullable();
            $table->string('os', 50)->nullable();
            $table->char('country_code', 2)->nullable();
            $table->string('referrer', 500)->nullable();
            $table->timestamp('created_at')->nullable();

            $table->index(['link_id_snapshot', 'created_at'], 'visits_link_id_snapshot_created_index');
            $table->index(['link_id', 'event_type', 'created_at'], 'visits_link_event_created_index');
            $table->index('created_at', 'visits_created_at_index');
            $table->index('country_code', 'visits_country_code_index');
            $table->index('device_type', 'visits_device_type_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visits');
    }
};

