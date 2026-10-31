<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Link;
use Illuminate\Support\Str;

class SlugGeneratorService
{
    public const RESERVED_SLUGS = [
        'admin', 'login', 'logout', 'blog', 'articles', 'article', 'category', 'categories',
        'about', 'contact', 'privacy-policy', 'terms', 'terms-of-service', 'help', 'faq',
        's', 'api', 'up', 'sitemap', 'sitemap.xml', 'robots.txt', 'feed', 'rss', 'css', 'js',
        'images', 'storage', 'assets', 'favicon.ico', 'dashboard', 'profile', 'settings',
        'features', 'how-it-works', 'pricing', 'home'
    ];

    /**
     * Check if a given slug is reserved by system routes.
     */
    public function isReserved(string $slug): bool
    {
        return in_array(strtolower(trim($slug)), self::RESERVED_SLUGS, true);
    }

    /**
     * Generate a unique random alphanumeric slug (6-8 characters).
     */
    public function generateUniqueSlug(int $length = 6): string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $maxAttempts = 10;
        $attempt = 0;

        do {
            $slug = '';
            for ($i = 0; $i < $length; $i++) {
                $slug .= $characters[random_int(0, strlen($characters) - 1)];
            }

            $exists = Link::withTrashed()->where('slug', $slug)->exists() || $this->isReserved($slug);
            $attempt++;

            if ($attempt >= $maxAttempts) {
                // Expand length slightly if high collision rate
                $length++;
                $attempt = 0;
            }
        } while ($exists);

        return $slug;
    }

    /**
     * Sanitize or generate a slug from input.
     */
    public function formatCustomSlug(string $input): string
    {
        return Str::slug($input, '-');
    }
}

