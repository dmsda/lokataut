<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Panduan Shortlink',
                'slug' => 'panduan-shortlink',
                'description' => 'Panduan praktis, teknik optimasi, dan pengelolaan tautan ringkas untuk berbagai kebutuhan digital.',
            ],
            [
                'name' => 'Safelink & Download',
                'slug' => 'safelink-download',
                'description' => 'Informasi mendalam tentang mekanisme halaman perantara unduhan, proteksi bot, dan alur pengalihan.',
            ],
            [
                'name' => 'Keamanan Digital',
                'slug' => 'keamanan-digital',
                'description' => 'Edukasi keamanan siber, tips mengenali tautan mencurigakan, dan perlindungan privasi bagi pengguna web.',
            ],
            [
                'name' => 'Tips Produktivitas',
                'slug' => 'tips-produktivitas',
                'description' => 'Strategi pengorganisasian berkas digital, manajemen tautan, dan workflow efisien berbagi file.',
            ],
            [
                'name' => 'Teknologi & Internet',
                'slug' => 'teknologi-internet',
                'description' => 'Wawasan seputar infrastruktur web, protokol HTTP redirection, URL encoding, dan ekosistem internet modern.',
            ],
        ];

        foreach ($categories as $cat) {
            Category::updateOrCreate(
                ['slug' => $cat['slug']],
                $cat
            );
        }
    }
}

