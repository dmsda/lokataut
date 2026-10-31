<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreArticleRequest;
use App\Http\Requests\Admin\UpdateArticleRequest;
use App\Models\Article;
use App\Models\Category;
use App\Services\SecurityLogService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ArticleController extends Controller
{
    public function __construct(
        protected SecurityLogService $securityLogService
    ) {}

    /**
     * Display a listing of articles with search, status filters, and category filters.
     */
    public function index(Request $request): View
    {
        $statusFilter = $request->query('status', 'all');
        $categoryId = $request->query('category_id');
        $search = $request->query('q');

        $query = Article::with('category');

        if ($statusFilter === 'trash') {
            $query->onlyTrashed();
        } else {
            if ($statusFilter === 'published') {
                $query->published();
            } elseif ($statusFilter === 'draft') {
                $query->draft();
            }
        }

        if (!empty($categoryId)) {
            $query->where('category_id', $categoryId);
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%")
                    ->orWhere('excerpt', 'like', "%{$search}%");
            });
        }

        $articles = $query->orderByDesc('id')->paginate(15)->withQueryString();
        $categories = Category::all();

        $counts = [
            'all' => Article::count(),
            'published' => Article::published()->count(),
            'draft' => Article::draft()->count(),
            'trash' => Article::onlyTrashed()->count(),
        ];

        return view('admin.articles.index', compact('articles', 'categories', 'statusFilter', 'categoryId', 'search', 'counts'));
    }

    /**
     * Show the form for creating a new article.
     */
    public function create(): View
    {
        $categories = Category::all();
        return view('admin.articles.create', compact('categories'));
    }

    /**
     * Store a newly created article in storage.
     */
    public function store(StoreArticleRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $slug = !empty($validated['slug'])
            ? Str::slug($validated['slug'])
            : Str::slug($validated['title']);

        // Check collision and ensure unique
        $originalSlug = $slug;
        $counter = 1;
        while (Article::withTrashed()->where('slug', $slug)->exists()) {
            $slug = "{$originalSlug}-{$counter}";
            $counter++;
        }

        $readingTime = Article::estimateReadingTime($validated['content']);

        $publishedAt = null;
        if ($validated['status'] === 'published') {
            $publishedAt = !empty($validated['published_at']) 
                ? Carbon::parse($validated['published_at']) 
                : Carbon::now();
        } elseif ($validated['status'] === 'scheduled' && !empty($validated['published_at'])) {
            $publishedAt = Carbon::parse($validated['published_at']);
        }

        $article = Article::create([
            'category_id' => $validated['category_id'] ?? null,
            'title' => $validated['title'],
            'slug' => $slug,
            'excerpt' => $validated['excerpt'] ?? Str::limit(strip_tags($validated['content']), 160),
            'content' => $validated['content'],
            'featured_image' => $validated['featured_image'] ?? null,
            'image_alt' => $validated['image_alt'] ?? null,
            'meta_title' => $validated['meta_title'] ?? $validated['title'],
            'meta_description' => $validated['meta_description'] ?? null,
            'author_name' => $validated['author_name'] ?: 'Dimas Aditya',
            'reading_time' => $readingTime,
            'status' => $validated['status'],
            'published_at' => $publishedAt,
        ]);

        $this->securityLogService->log(
            eventType: 'article_created',
            ipAddress: $request->ip() ?? '127.0.0.1',
            userAgent: $request->userAgent(),
            details: ['article_id' => $article->id, 'title' => $article->title, 'status' => $article->status],
            adminId: auth()->id()
        );

        return redirect()->route('admin.articles.index')
            ->with('success', "Artikel '{$article->title}' berhasil disimpan.");
    }

    /**
     * Show the form for editing the specified article.
     */
    public function edit(Article $article): View
    {
        $categories = Category::all();
        return view('admin.articles.edit', compact('article', 'categories'));
    }

    /**
     * Update the specified article in storage.
     */
    public function update(UpdateArticleRequest $request, Article $article): RedirectResponse
    {
        $validated = $request->validated();

        $readingTime = Article::estimateReadingTime($validated['content']);

        $publishedAt = $article->published_at;
        if ($validated['status'] === 'published' && $article->status !== 'published' && empty($publishedAt)) {
            $publishedAt = !empty($validated['published_at']) ? Carbon::parse($validated['published_at']) : Carbon::now();
        } elseif (!empty($validated['published_at'])) {
            $publishedAt = Carbon::parse($validated['published_at']);
        }

        $article->update([
            'category_id' => $validated['category_id'] ?? null,
            'title' => $validated['title'],
            'slug' => Str::slug($validated['slug']),
            'excerpt' => $validated['excerpt'] ?? Str::limit(strip_tags($validated['content']), 160),
            'content' => $validated['content'],
            'featured_image' => $validated['featured_image'] ?? null,
            'image_alt' => $validated['image_alt'] ?? null,
            'meta_title' => $validated['meta_title'] ?? $validated['title'],
            'meta_description' => $validated['meta_description'] ?? null,
            'author_name' => $validated['author_name'] ?: 'Dimas Aditya',
            'reading_time' => $readingTime,
            'status' => $validated['status'],
            'published_at' => $publishedAt,
        ]);

        $this->securityLogService->log(
            eventType: 'article_updated',
            ipAddress: $request->ip() ?? '127.0.0.1',
            userAgent: $request->userAgent(),
            details: ['article_id' => $article->id, 'title' => $article->title],
            adminId: auth()->id()
        );

        return redirect()->route('admin.articles.index')
            ->with('success', "Artikel '{$article->title}' berhasil diperbarui.");
    }

    /**
     * Soft delete an article.
     */
    public function destroy(Article $article, Request $request): RedirectResponse
    {
        $title = $article->title;
        $article->delete();

        $this->securityLogService->log(
            eventType: 'article_soft_deleted',
            ipAddress: $request->ip() ?? '127.0.0.1',
            userAgent: $request->userAgent(),
            details: ['article_id' => $article->id, 'title' => $title],
            adminId: auth()->id()
        );

        return redirect()->route('admin.articles.index')
            ->with('success', "Artikel '{$title}' telah dipindahkan ke tong sampah.");
    }

    /**
     * Restore a soft deleted article.
     */
    public function restore(int $id, Request $request): RedirectResponse
    {
        $article = Article::onlyTrashed()->findOrFail($id);
        $article->restore();

        $this->securityLogService->log(
            eventType: 'article_restored',
            ipAddress: $request->ip() ?? '127.0.0.1',
            userAgent: $request->userAgent(),
            details: ['article_id' => $article->id, 'title' => $article->title],
            adminId: auth()->id()
        );

        return redirect()->route('admin.articles.index', ['status' => 'trash'])
            ->with('success', "Artikel '{$article->title}' berhasil dipulihkan.");
    }

    /**
     * Force delete an article permanently.
     */
    public function forceDelete(int $id, Request $request): RedirectResponse
    {
        $article = Article::onlyTrashed()->findOrFail($id);
        $title = $article->title;
        $article->forceDelete();

        $this->securityLogService->log(
            eventType: 'article_force_deleted',
            ipAddress: $request->ip() ?? '127.0.0.1',
            userAgent: $request->userAgent(),
            details: ['article_id' => $id, 'title' => $title],
            adminId: auth()->id()
        );

        return redirect()->route('admin.articles.index', ['status' => 'trash'])
            ->with('success', "Artikel '{$title}' telah dihapus permanen.");
    }
}

