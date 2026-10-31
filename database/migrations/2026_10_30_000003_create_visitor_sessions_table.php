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
        Schema::create('visitor_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('link_id')->constrained('links')->onDelete('cascade');
            $table->string('token_hash', 64)->unique('visitor_sessions_token_hash_unique');
            $table->string('user_agent_hash', 64)->nullable();
            $table->boolean('turnstile_verified')->default(false);
            $table->timestamp('turnstile_verified_at')->nullable();
            $table->timestamp('countdown_started_at')->nullable();
            $table->unsignedSmallInteger('countdown_seconds')->default(10);
            $table->boolean('completed')->default(false);
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('expires_at');
            $table->timestamps();

            $table->index(['link_id', 'created_at'], 'visitor_sessions_link_id_created_at_index');
            $table->index(['expires_at', 'completed'], 'visitor_sessions_expires_at_completed_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visitor_sessions');
    }
};

