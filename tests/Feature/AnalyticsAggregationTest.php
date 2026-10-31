<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\AggregationCheckpoint;
use App\Models\DailyStat;
use App\Models\Link;
use App\Models\Visit;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnalyticsAggregationTest extends TestCase
{
    use RefreshDatabase;

    public function test_aggregation_command_computes_daily_stats_accurately(): void
    {
        $yesterday = Carbon::yesterday()->toDateString();
        $sampleTime = Carbon::parse($yesterday)->setTime(14, 30);

        $link = Link::create([
            'slug' => 'test-aggregate',
            'destination_url' => 'https://example.com/dest',
            'title' => 'Test Aggregate',
            'status' => 'active',
            'countdown_seconds' => 10,
        ]);

        // Insert visits
        Visit::create([
            'link_id' => $link->id,
            'link_id_snapshot' => $link->id,
            'link_slug_snapshot' => $link->slug,
            'event_type' => 'impression',
            'ip_hash' => 'hash_ip_1',
            'device_type' => 'desktop',
            'country_code' => 'ID',
            'created_at' => $sampleTime,
        ]);

        Visit::create([
            'link_id' => $link->id,
            'link_id_snapshot' => $link->id,
            'link_slug_snapshot' => $link->slug,
            'event_type' => 'impression',
            'ip_hash' => 'hash_ip_2',
            'device_type' => 'mobile',
            'country_code' => 'US',
            'created_at' => $sampleTime,
        ]);

        Visit::create([
            'link_id' => $link->id,
            'link_id_snapshot' => $link->id,
            'link_slug_snapshot' => $link->slug,
            'event_type' => 'completed',
            'ip_hash' => 'hash_ip_1',
            'device_type' => 'desktop',
            'country_code' => 'ID',
            'created_at' => $sampleTime->copy()->addSeconds(15),
        ]);

        // Execute aggregation command
        $this->artisan('lokataut:aggregate-daily', ['--date' => $yesterday])
            ->assertSuccessful();

        // Verify DailyStat record
        $stat = DailyStat::where('aggregate_key', 'link:' . $link->id)
            ->where('date', $yesterday)
            ->first();

        $this->assertNotNull($stat);
        $this->assertEquals(2, $stat->impressions);
        $this->assertEquals(1, $stat->completions);
        $this->assertEquals(2, $stat->unique_visitors);
        $this->assertEquals(2, $stat->desktop_count); // 1 impression + 1 completion
        $this->assertEquals(1, $stat->mobile_count);

        // Verify checkpoint
        $checkpoint = AggregationCheckpoint::find($yesterday);
        $this->assertNotNull($checkpoint);
        $this->assertEquals('completed', $checkpoint->status);
    }

    public function test_aggregation_command_is_completely_idempotent_on_retry(): void
    {
        $targetDate = '2026-10-25';
        $sampleTime = Carbon::parse($targetDate)->setTime(10, 0);

        $link = Link::create([
            'slug' => 'idempotent-slug',
            'destination_url' => 'https://example.com/item',
            'title' => 'Idempotent Link',
            'status' => 'active',
            'countdown_seconds' => 10,
        ]);

        Visit::create([
            'link_id' => $link->id,
            'link_id_snapshot' => $link->id,
            'link_slug_snapshot' => $link->slug,
            'event_type' => 'impression',
            'ip_hash' => 'ip_fixed',
            'device_type' => 'desktop',
            'country_code' => 'ID',
            'created_at' => $sampleTime,
        ]);

        // Run 1
        $this->artisan('lokataut:aggregate-daily', ['--date' => $targetDate])->assertSuccessful();
        $countAfterFirstRun = DailyStat::where('date', $targetDate)->count();
        $this->assertEquals(1, $countAfterFirstRun);

        // Run 2 (Retry / Re-run)
        $this->artisan('lokataut:aggregate-daily', ['--date' => $targetDate])->assertSuccessful();
        $countAfterSecondRun = DailyStat::where('date', $targetDate)->count();

        // Must still be exactly 1 record! No duplicates!
        $this->assertEquals(1, $countAfterSecondRun);

        $stat = DailyStat::where('aggregate_key', 'link:' . $link->id)->where('date', $targetDate)->first();
        $this->assertEquals(1, $stat->impressions);
    }
}

