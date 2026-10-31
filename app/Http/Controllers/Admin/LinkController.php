<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreLinkRequest;
use App\Http\Requests\Admin\UpdateLinkRequest;
use App\Models\Link;
use App\Services\SecurityLogService;
use App\Services\SlugGeneratorService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LinkController extends Controller
{
    public function __construct(
        protected SlugGeneratorService $slugService,
        protected SecurityLogService $securityLogService
    ) {}

    /**
     * Display a listing of links with search, filters, and pagination.
     */
    public function index(Request $request): View
    {
        $statusFilter = $request->query('status', 'all');
        $search = $request->query('q');

        $query = Link::query();

        if ($statusFilter === 'trash') {
            $query->onlyTrashed();
        } else {
            if ($statusFilter === 'active') {
                $query->active();
            } elseif ($statusFilter === 'inactive') {
                $query->where('status', 'inactive');
            }
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%")
                    ->orWhere('source_website', 'like', "%{$search}%");
            });
        }

        $links = $query->orderByDesc('id')->paginate(15)->withQueryString();
        $counts = [
            'all' => Link::count(),
            'active' => Link::active()->count(),
            'inactive' => Link::where('status', 'inactive')->count(),
            'trash' => Link::onlyTrashed()->count(),
        ];

        return view('admin.links.index', compact('links', 'statusFilter', 'search', 'counts'));
    }

    /**
     * Show the form for creating a new link.
     */
    public function create(): View
    {
        return view('admin.links.create');
    }

    /**
     * Store a newly created link in storage.
     */
    public function store(StoreLinkRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $slug = !empty($validated['slug'])
            ? $this->slugService->formatCustomSlug($validated['slug'])
            : $this->slugService->generateUniqueSlug();

        $link = Link::create([
            'slug' => $slug,
            'destination_url' => $validated['destination_url'],
            'title' => $validated['title'] ?? null,
            'source_website' => $validated['source_website'] ?? null,
            'countdown_seconds' => (int) ($validated['countdown_seconds'] ?? config('services.safelink.default_countdown', 10)),
            'expires_at' => !empty($validated['expires_at']) ? $validated['expires_at'] : null,
            'meta_description' => $validated['meta_description'] ?? null,
            'status' => $validated['status'] ?? 'active',
        ]);

        $this->securityLogService->log(
            eventType: 'link_created',
            ipAddress: $request->ip() ?? '127.0.0.1',
            userAgent: $request->userAgent(),
            details: ['link_id' => $link->id, 'slug' => $link->slug],
            adminId: auth()->id()
        );

        return redirect()->route('admin.links.index')
            ->with('success', "Tautan baru berhasil dibuat dengan slug: /s/{$link->slug}");
    }

    /**
     * Show the form for editing the specified link.
     */
    public function edit(Link $link): View
    {
        return view('admin.links.edit', compact('link'));
    }

    /**
     * Update the specified link in storage.
     */
    public function update(UpdateLinkRequest $request, Link $link): RedirectResponse
    {
        $validated = $request->validated();

        $slug = $this->slugService->formatCustomSlug($validated['slug']);

        $link->update([
            'slug' => $slug,
            'destination_url' => $validated['destination_url'],
            'title' => $validated['title'] ?? null,
            'source_website' => $validated['source_website'] ?? null,
            'countdown_seconds' => (int) $validated['countdown_seconds'],
            'expires_at' => !empty($validated['expires_at']) ? $validated['expires_at'] : null,
            'meta_description' => $validated['meta_description'] ?? null,
            'status' => $validated['status'],
        ]);

        $this->securityLogService->log(
            eventType: 'link_updated',
            ipAddress: $request->ip() ?? '127.0.0.1',
            userAgent: $request->userAgent(),
            details: ['link_id' => $link->id, 'slug' => $link->slug],
            adminId: auth()->id()
        );

        return redirect()->route('admin.links.index')
            ->with('success', "Tautan /s/{$link->slug} berhasil diperbarui.");
    }

    /**
     * Soft delete the specified link.
     */
    public function destroy(Link $link, Request $request): RedirectResponse
    {
        $slug = $link->slug;
        $id = $link->id;

        $link->delete();

        $this->securityLogService->log(
            eventType: 'link_soft_deleted',
            ipAddress: $request->ip() ?? '127.0.0.1',
            userAgent: $request->userAgent(),
            details: ['link_id' => $id, 'slug' => $slug],
            adminId: auth()->id()
        );

        return redirect()->route('admin.links.index')
            ->with('success', "Tautan /s/{$slug} telah dipindahkan ke tong sampah (Soft Deleted).");
    }

    /**
     * Restore a soft deleted link.
     */
    public function restore(int $id, Request $request): RedirectResponse
    {
        $link = Link::onlyTrashed()->findOrFail($id);
        $link->restore();

        $this->securityLogService->log(
            eventType: 'link_restored',
            ipAddress: $request->ip() ?? '127.0.0.1',
            userAgent: $request->userAgent(),
            details: ['link_id' => $link->id, 'slug' => $link->slug],
            adminId: auth()->id()
        );

        return redirect()->route('admin.links.index', ['status' => 'trash'])
            ->with('success', "Tautan /s/{$link->slug} berhasil dipulihkan.");
    }

    /**
     * Permanently purge (hard delete) a link.
     * Cascades visitor_sessions and sets NULL on visits and daily_stats while preserving snapshots.
     */
    public function forceDelete(int $id, Request $request): RedirectResponse
    {
        $link = Link::onlyTrashed()->findOrFail($id);
        $slug = $link->slug;

        $link->forceDelete();

        $this->securityLogService->log(
            eventType: 'link_force_deleted',
            ipAddress: $request->ip() ?? '127.0.0.1',
            userAgent: $request->userAgent(),
            details: ['link_id' => $id, 'slug' => $slug],
            adminId: auth()->id()
        );

        return redirect()->route('admin.links.index', ['status' => 'trash'])
            ->with('success', "Tautan /s/{$slug} telah dihapus permanen (Hard Purge). Riwayat analitik snapshot tetap aman.");
    }
}

