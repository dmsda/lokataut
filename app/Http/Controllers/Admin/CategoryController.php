<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCategoryRequest;
use App\Http\Requests\Admin\UpdateCategoryRequest;
use App\Models\Category;
use App\Services\SecurityLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function __construct(
        protected SecurityLogService $securityLogService
    ) {}

    /**
     * Display a listing of categories.
     */
    public function index(): View
    {
        $categories = Category::withCount('articles')->orderBy('name')->get();
        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Store a newly created category in storage.
     */
    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $slug = !empty($validated['slug'])
            ? Str::slug($validated['slug'])
            : Str::slug($validated['name']);

        $category = Category::create([
            'name' => $validated['name'],
            'slug' => $slug,
            'description' => $validated['description'] ?? null,
        ]);

        $this->securityLogService->log(
            eventType: 'category_created',
            ipAddress: $request->ip() ?? '127.0.0.1',
            userAgent: $request->userAgent(),
            details: ['category_id' => $category->id, 'name' => $category->name],
            adminId: auth()->id()
        );

        return redirect()->route('admin.categories.index')
            ->with('success', "Kategori '{$category->name}' berhasil ditambahkan.");
    }

    /**
     * Update the specified category in storage.
     */
    public function update(UpdateCategoryRequest $request, Category $category): RedirectResponse
    {
        $validated = $request->validated();

        $category->update([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['slug']),
            'description' => $validated['description'] ?? null,
        ]);

        $this->securityLogService->log(
            eventType: 'category_updated',
            ipAddress: $request->ip() ?? '127.0.0.1',
            userAgent: $request->userAgent(),
            details: ['category_id' => $category->id, 'name' => $category->name],
            adminId: auth()->id()
        );

        return redirect()->route('admin.categories.index')
            ->with('success', "Kategori '{$category->name}' berhasil diperbarui.");
    }

    /**
     * Remove the specified category from storage.
     */
    public function destroy(Category $category): RedirectResponse
    {
        if ($category->articles()->exists()) {
            return redirect()->route('admin.categories.index')
                ->with('error', "Kategori '{$category->name}' tidak dapat dihapus karena masih memiliki artikel terkait.");
        }

        $name = $category->name;
        $category->delete();

        $this->securityLogService->log(
            eventType: 'category_deleted',
            ipAddress: request()->ip() ?? '127.0.0.1',
            userAgent: request()->userAgent(),
            details: ['name' => $name],
            adminId: auth()->id()
        );

        return redirect()->route('admin.categories.index')
            ->with('success', "Kategori '{$name}' berhasil dihapus.");
    }
}
