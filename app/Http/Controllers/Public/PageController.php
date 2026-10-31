<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\Public\ContactRequest;
use App\Models\Article;
use App\Models\Link;
use App\Services\SecurityLogService;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\View\View;

class PageController extends Controller
{
    /**
     * Display the public homepage.
     */
    public function home(): View
    {
        $latestArticles = Article::with('category')
            ->published()
            ->orderByDesc('published_at')
            ->take(6)
            ->get();

        $stats = [
            'total_links' => Link::count(),
            'total_visits' => (int) Link::sum('total_visits'),
            'total_completions' => (int) Link::sum('total_completions'),
        ];

        return view('public.home', compact('latestArticles', 'stats'));
    }

    /**
     * Display About Us page.
     */
    public function about(): View
    {
        return view('public.about');
    }

    /**
     * Display Contact page.
     */
    public function contact(): View
    {
        return view('public.contact');
    }

    /**
     * Handle contact form submission.
     */
    public function submitContact(ContactRequest $request, SecurityLogService $securityLogService)
    {
        $validated = $request->validated();

        $securityLogService->log(
            eventType: 'contact_form_submitted',
            ipAddress: $request->ip() ?? '127.0.0.1',
            userAgent: $request->userAgent(),
            details: [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'subject' => $validated['subject'],
            ]
        );

        return redirect()->route('contact')
            ->with('success', 'Pesan Anda telah berhasil dikirimkan kepada tim LokaTaut. Kami akan meninjau dan merespon secepatnya.');
    }

    /**
     * Display Privacy Policy.
     */
    public function privacyPolicy(): View
    {
        return view('public.privacy-policy');
    }

    /**
     * Display Terms of Service.
     */
    public function terms(): View
    {
        return view('public.terms');
    }

    /**
     * Display Help & FAQ page.
     */
    public function help(): View
    {
        return view('public.help');
    }

    /**
     * Generate dynamic XML sitemap.
     */
    public function sitemap(): Response
    {
        $articles = Article::published()->orderByDesc('published_at')->get();

        $staticPages = [
            ['url' => route('home'), 'priority' => '1.0', 'changefreq' => 'daily'],
            ['url' => route('blog.index'), 'priority' => '0.9', 'changefreq' => 'daily'],
            ['url' => route('about'), 'priority' => '0.7', 'changefreq' => 'monthly'],
            ['url' => route('help'), 'priority' => '0.7', 'changefreq' => 'weekly'],
            ['url' => route('contact'), 'priority' => '0.6', 'changefreq' => 'monthly'],
            ['url' => route('privacy-policy'), 'priority' => '0.5', 'changefreq' => 'monthly'],
            ['url' => route('terms'), 'priority' => '0.5', 'changefreq' => 'monthly'],
        ];

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        foreach ($staticPages as $page) {
            $xml .= "  <url>\n";
            $xml .= "    <loc>{$page['url']}</loc>\n";
            $xml .= "    <changefreq>{$page['changefreq']}</changefreq>\n";
            $xml .= "    <priority>{$page['priority']}</priority>\n";
            $xml .= "  </url>\n";
        }

        foreach ($articles as $article) {
            $loc = route('blog.show', $article->slug);
            $lastmod = ($article->updated_at ?? $article->published_at ?? Carbon::now())->toAtomString();
            $xml .= "  <url>\n";
            $xml .= "    <loc>{$loc}</loc>\n";
            $xml .= "    <lastmod>{$lastmod}</lastmod>\n";
            $xml .= "    <changefreq>weekly</changefreq>\n";
            $xml .= "    <priority>0.8</priority>\n";
            $xml .= "  </url>\n";
        }

        $xml .= '</urlset>';

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=utf-8']);
    }

    /**
     * Generate dynamic robots.txt.
     */
    public function robots(): Response
    {
        $sitemapUrl = route('sitemap');

        $content = "User-agent: *\n";
        $content .= "Disallow: /admin/\n";
        $content .= "Disallow: /login\n";
        $content .= "Disallow: /s/\n";
        $content .= "Allow: /\n";
        $content .= "Allow: /blog/\n";
        $content .= "Allow: /about\n";
        $content .= "Allow: /help\n";
        $content .= "Allow: /contact\n";
        $content .= "Allow: /privacy-policy\n";
        $content .= "Allow: /terms\n\n";
        $content .= "Sitemap: {$sitemapUrl}\n";

        return response($content, 200, ['Content-Type' => 'text/plain; charset=utf-8']);
    }
}

