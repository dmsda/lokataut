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
        Schema::create('ad_slots', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('position', 50);
            $table->text('ad_code')->nullable();
            $table->boolean('is_active')->default(true);
            $table->enum('device_target', ['all', 'desktop', 'mobile'])->default('all');
            $table->unsignedTinyInteger('sort_order')->default(0);
            $table->string('notes', 255)->nullable();
            $table->timestamps();

            $table->index(['position', 'is_active', 'device_target'], 'ad_slots_pos_active_dev_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ad_slots');
    }
};

