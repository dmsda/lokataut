<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BlogController extends Controller
{
    /**
     * Display public blog index with search, category filtering, and featured article.
     */
    public function index(Request $request): View
    {
        $search = $request->query('q');
        $categorySlug = $request->query('category');

        $categories = Category::withCount(['articles as published_count' => function ($q) {
            $q->published();
        }])->get();

        $currentCategory = null;
        if (!empty($categorySlug)) {
            $currentCategory = Category::where('slug', $categorySlug)->first();
        }

        $query = Article::with('category')->published();

        if ($currentCategory !== null) {
            $query->where('category_id', $currentCategory->id);
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('excerpt', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%");
            });
        }

        // Featured article: the newest published article if no search and no category filter
        $featuredArticle = null;
        if (empty($search) && empty($categorySlug) && $request->query('page', 1) == 1) {
            $featuredArticle = (clone $query)->orderByDesc('published_at')->first();
            if ($featuredArticle !== null) {
                $query->where('id', '!=', $featuredArticle->id);
            }
        }

        $articles = $query->orderByDesc('published_at')->paginate(9)->withQueryString();

        return view('blog.index', compact('articles', 'categories', 'currentCategory', 'search', 'featuredArticle'));
    }

    /**
     * Direct category archive route (/blog/category/{slug}).
     */
    public function category(string $slug): View
    {
        $category = Category::where('slug', $slug)->firstOrFail();

        $categories = Category::withCount(['articles as published_count' => function ($q) {
            $q->published();
        }])->get();

        $articles = Article::with('category')
            ->published()
            ->where('category_id', $category->id)
            ->orderByDesc('published_at')
            ->paginate(9);

        $currentCategory = $category;
        $search = null;
        $featuredArticle = null;

        return view('blog.index', compact('articles', 'categories', 'currentCategory', 'search', 'featuredArticle'));
    }

    /**
     * Display single article page.
     */
    public function show(string $slug): View
    {
        $article = Article::with('category')
            ->published()
            ->where('slug', $slug)
            ->firstOrFail();

        // Related articles from the same category
        $relatedArticles = Article::with('category')
            ->published()
            ->where('id', '!=', $article->id)
            ->where('category_id', $article->category_id)
            ->take(3)
            ->get();

        // Previous and Next article navigation
        $previousArticle = Article::published()
            ->where('published_at', '<', $article->published_at)
            ->orderByDesc('published_at')
            ->first();

        $nextArticle = Article::published()
            ->where('published_at', '>', $article->published_at)
            ->orderBy('published_at')
            ->first();

        return view('blog.show', compact('article', 'relatedArticles', 'previousArticle', 'nextArticle'));
    }
}

