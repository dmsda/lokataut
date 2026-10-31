@extends('layouts.site')

@section('meta_title', $currentCategory ? "Artikel: {$currentCategory->name} — LokaTaut" : 'Pusat Artikel & Edukasi Manajemen Tautan — LokaTaut')
@section('meta_description', $currentCategory ? "Kumpulan artikel edukatif mengenai {$currentCategory->name}: {$currentCategory->description}" : 'Panduan mendalam mengenai teknologi shortlink, safelink, keamanan siber, dan tips produktivitas mengelola berkas digital.')

@section('content')
<div class="container section">
  <div style="text-align: center; max-width: 680px; margin: 0 auto 40px auto;">
    <span class="section-badge">Pusat Edukasi & Publikasi</span>
    <h1 class="section-title">
      {{ $currentCategory ? $currentCategory->name : 'Artikel & Panduan Teknologi' }}
    </h1>
    <p class="section-sub">
      {{ $currentCategory ? $currentCategory->description : 'Wawasan praktis seputar pengelolaan tautan, proteksi unduhan, dan keamanan digital.' }}
    </p>
  </div>

  <!-- Search & Category Filters Bar -->
  <div style="background: var(--bg-surface); border: 1px solid var(--border-subtle); border-radius: var(--radius-md); padding: 18px 24px; margin-bottom: 40px; box-shadow: var(--shadow-sm);">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
      <!-- Category Filter Pills -->
      <div style="display: flex; gap: 8px; flex-wrap: wrap; align-items: center;">
        <a href="{{ route('blog.index') }}" class="btn {{ empty($currentCategory) ? 'btn-primary' : 'btn-secondary' }}" style="font-size: 13px; padding: 6px 14px;">
          Semua Topik
        </a>
        @foreach ($categories as $cat)
          <a href="{{ route('blog.category', $cat->slug) }}" class="btn {{ ($currentCategory && $currentCategory->id === $cat->id) ? 'btn-primary' : 'btn-secondary' }}" style="font-size: 13px; padding: 6px 14px;">
            {{ $cat->name }} <span style="opacity: 0.7; font-size: 11px; margin-left: 2px;">({{ $cat->published_count }})</span>
          </a>
        @endforeach
      </div>

      <!-- Search Input -->
      <form action="{{ route('blog.index') }}" method="GET" style="display: flex; gap: 8px; flex: 1; min-width: 200px; max-width: 320px;">
        @if ($currentCategory)
          <input type="hidden" name="category" value="{{ $currentCategory->slug }}">
        @endif
        <input type="text" name="q" value="{{ $search }}" placeholder="Cari artikel..." style="padding: 7px 14px; border: 1px solid var(--border-subtle); border-radius: var(--radius-sm); font-size: 13px; width: 100%; background: var(--bg-surface); color: var(--text-primary);">
        <button type="submit" class="btn btn-secondary" style="font-size: 13px; padding: 7px 12px;">Cari</button>
      </form>
    </div>
  </div>

  <!-- Featured Article (if available) -->
  @if ($featuredArticle && empty($search))
    <div style="margin-bottom: 48px;">
      <article class="featured-article-card">
        <div style="padding: clamp(24px, 4vw, 40px); display: flex; flex-direction: column; justify-content: center;">
          <div style="display: flex; gap: 8px; align-items: center; margin-bottom: 14px; flex-wrap: wrap;">
            <span class="category-tag" style="background: var(--primary); color: #fff; border-color: var(--primary);">ARTIKEL UTAMA</span>
            <span class="category-tag">{{ $featuredArticle->category->name ?? 'Umum' }}</span>
            <span style="font-size: 12px; color: var(--text-muted);">&bull; {{ $featuredArticle->reading_time }} mnt baca</span>
          </div>

          <h2 style="font-size: clamp(22px, 3.5vw, 28px); font-weight: 800; line-height: 1.3; margin-bottom: 14px;">
            <a href="{{ route('blog.show', $featuredArticle->slug) }}" style="color: inherit;">{{ $featuredArticle->title }}</a>
          </h2>

          <p style="font-size: 15px; color: var(--text-secondary); line-height: 1.6; margin-bottom: 24px;">
            {{ $featuredArticle->excerpt }}
          </p>

          <div style="display: flex; align-items: center; justify-content: space-between; font-size: 13px; color: var(--text-muted); flex-wrap: wrap; gap: 12px;">
            <span>Oleh <strong>{{ $featuredArticle->author_name }}</strong> &bull; {{ $featuredArticle->published_at ? $featuredArticle->published_at->translatedFormat('d M Y') : '' }}</span>
            <a href="{{ route('blog.show', $featuredArticle->slug) }}" class="btn btn-primary" style="font-size: 13px; padding: 8px 16px;">Baca Artikel &rarr;</a>
          </div>
        </div>

        <div style="background: linear-gradient(135deg, #1e293b, #0f172a); display: flex; align-items: center; justify-content: center; padding: 32px; color: #fff; text-align: center;">
          <div style="max-width: 260px;">
            <div style="width: 56px; height: 56px; border-radius: 14px; background: rgba(59, 130, 246, 0.2); border: 1px solid rgba(59, 130, 246, 0.4); display: flex; align-items: center; justify-content: center; margin: 0 auto 16px auto; color: var(--primary);">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path>
                <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>
              </svg>
            </div>
            <div style="font-size: 15px; font-weight: 700; color: #f8fafc; margin-bottom: 6px;">Editorial LokaTaut</div>
            <div style="font-size: 12px; color: #94a3b8; line-height: 1.5;">Ulasan mendalam teknologi web dan keamanan tautan digital.</div>
          </div>
        </div>
      </article>
    </div>
  @endif

  <!-- Articles Grid -->
  <div class="grid-3">
    @forelse ($articles as $article)
      <article class="article-card">
        <div class="article-card-body">
          <div class="article-meta">
            <span class="category-tag">{{ $article->category->name ?? 'Umum' }}</span>
            <span>&bull;</span>
            <span>{{ $article->reading_time }} mnt baca</span>
          </div>

          <h2 class="article-card-title">
            <a href="{{ route('blog.show', $article->slug) }}">{{ $article->title }}</a>
          </h2>

          <p class="article-card-excerpt">{{ $article->excerpt }}</p>

          <div class="article-card-footer">
            <span>{{ $article->published_at ? $article->published_at->translatedFormat('d M Y') : '-' }}</span>
            <a href="{{ route('blog.show', $article->slug) }}" style="font-weight: 600; font-size: 13px;">Baca Selengkapnya &rarr;</a>
          </div>
        </div>
      </article>
    @empty
      <div style="grid-column: 1 / -1; text-align: center; padding: 64px 20px; background: var(--bg-surface); border: 1px solid var(--border-subtle); border-radius: var(--radius-md);">
        <div style="font-size: 32px; margin-bottom: 12px;">🔍</div>
        <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 6px;">Tidak ada artikel ditemukan</h3>
        <p style="font-size: 14px; color: var(--text-muted); margin-bottom: 18px;">
          @if (!empty($search))
            Tidak ditemukan artikel dengan kata kunci "{{ $search }}".
          @else
            Belum ada artikel yang tersedia pada kategori ini.
          @endif
        </p>
        <a href="{{ route('blog.index') }}" class="btn btn-secondary">Kembali ke Semua Artikel</a>
      </div>
    @endforelse
  </div>

  <!-- Pagination -->
  @if ($articles->hasPages())
    <div style="margin-top: 48px; display: flex; justify-content: center;">
      {{ $articles->links() }}
    </div>
  @endif
</div>
@endsection

