<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\AdSlot;
use Illuminate\Database\Seeder;

class AdSlotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $slots = [
            [
                'name' => 'Header Banner',
                'position' => 'header',
                'ad_code' => '<!-- Adsterra / Monetag Banner Header (Placeholder) --><div class="ad-placeholder"><span class="ad-badge">SPONSORED</span><div class="ad-box">Adsterra Responsive Leaderboard</div></div>',
                'is_active' => true,
                'device_target' => 'all',
                'sort_order' => 1,
                'notes' => 'Banner di bagian atas header halaman safelink',
            ],
            [
                'name' => 'Above Timer Banner',
                'position' => 'above_timer',
                'ad_code' => '<!-- Adsterra / Monetag Banner Above Timer (Placeholder) --><div class="ad-placeholder"><span class="ad-badge">SPONSORED</span><div class="ad-box">Adsterra 300x250 Above Timer</div></div>',
                'is_active' => true,
                'device_target' => 'all',
                'sort_order' => 2,
                'notes' => 'Banner tepat di atas area timer hitung mundur',
            ],
            [
                'name' => 'Below Timer Banner',
                'position' => 'below_timer',
                'ad_code' => '<!-- Adsterra / Monetag Banner Below Timer (Placeholder) --><div class="ad-placeholder"><span class="ad-badge">SPONSORED</span><div class="ad-box">Adsterra 300x250 Below Timer</div></div>',
                'is_active' => true,
                'device_target' => 'all',
                'sort_order' => 3,
                'notes' => 'Banner di bawah timer dengan jarak aman 30px ke tombol navigasi',
            ],
            [
                'name' => 'Footer Banner',
                'position' => 'footer',
                'ad_code' => '<!-- Adsterra / Monetag Banner Footer (Placeholder) --><div class="ad-placeholder"><span class="ad-badge">SPONSORED</span><div class="ad-box">Adsterra Responsive Footer</div></div>',
                'is_active' => true,
                'device_target' => 'all',
                'sort_order' => 4,
                'notes' => 'Banner di bagian bawah halaman sebelum copyright',
            ],
        ];

        foreach ($slots as $slot) {
            AdSlot::updateOrCreate(
                ['position' => $slot['position']],
                $slot
            );
        }
    }
}

