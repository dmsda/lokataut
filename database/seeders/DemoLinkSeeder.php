<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Link;
use Illuminate\Database\Seeder;

class DemoLinkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $links = [
            [
                'slug' => 'demo-download',
                'destination_url' => 'https://wordpress.org/latest.zip',
                'title' => 'WordPress Latest Core Package',
                'source_website' => 'softwaredown.id',
                'countdown_seconds' => 10,
                'status' => 'active',
                'meta_description' => 'Demo download package untuk uji coba lokal',
            ],
            [
                'slug' => 'sample-pdf',
                'destination_url' => 'https://www.w3.org/WAI/ER/tests/xhtml/testfiles/resources/pdf/dummy.pdf',
                'title' => 'Sample Specification PDF',
                'source_website' => 'arsipdokumen.com',
                'countdown_seconds' => 5,
                'status' => 'active',
                'meta_description' => 'Demo PDF file untuk verifikasi countdown cepat 5 detik',
            ],
        ];

        foreach ($links as $link) {
            Link::updateOrCreate(
                ['slug' => $link['slug']],
                $link
            );
        }
    }
}

