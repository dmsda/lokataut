@extends('layouts.site')

@section('meta_title', ($article->meta_title ?: $article->title) . ' — LokaTaut')
@section('meta_description', $article->meta_description ?: $article->excerpt)
@section('canonical_url', route('blog.show', $article->slug))
@section('og_type', 'article')

@push('schema')
<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@type": "BlogPosting",
  "headline": "{{ addslashes($article->title) }}",
  "description": "{{ addslashes($article->excerpt ?? '') }}",
  "mainEntityOfPage": {
    "@type": "WebPage",
    "@id": "{{ route('blog.show', $article->slug) }}"
  },
  "author": {
    "@type": "Person",
    "name": "{{ addslashes($article->author_name) }}"
  },
  "publisher": {
    "@type": "Organization",
    "name": "LokaTaut",
    "url": "{{ url('/') }}"
  },
  "datePublished": "{{ $article->published_at ? $article->published_at->toAtomString() : '' }}",
  "dateModified": "{{ $article->updated_at ? $article->updated_at->toAtomString() : '' }}"
}
</script>
@endpush

@section('content')
<div class="container section">
  <div class="reading-container">
    <!-- Breadcrumb -->
    <nav class="breadcrumb" aria-label="Breadcrumb">
      <a href="{{ route('home') }}">Beranda</a>
      <span>/</span>
      <a href="{{ route('blog.index') }}">Artikel</a>
      @if ($article->category)
        <span>/</span>
        <a href="{{ route('blog.category', $article->category->slug) }}">{{ $article->category->name }}</a>
      @endif
      <span>/</span>
      <span style="color: var(--text-primary); text-overflow: ellipsis; white-space: nowrap; overflow: hidden; max-width: 280px;">{{ $article->title }}</span>
    </nav>

    <!-- Article Header -->
    <header class="article-header">
      <div style="display: flex; gap: 8px; align-items: center; margin-bottom: 16px;">
        @if ($article->category)
          <a href="{{ route('blog.category', $article->category->slug) }}" class="category-tag">
            {{ $article->category->name }}
          </a>
        @endif
        <span style="font-size: 13px; color: var(--text-muted);">&bull; {{ $article->reading_time }} menit membaca</span>
      </div>

      <h1 class="article-headline">{{ $article->title }}</h1>

      @if (!empty($article->excerpt))
        <p class="article-lead">{{ $article->excerpt }}</p>
      @endif

      <div class="article-author-bar">
        <div>
          Ditulis oleh <strong>{{ $article->author_name }}</strong>
        </div>
        <div>
          {{ $article->published_at ? $article->published_at->translatedFormat('d F Y') : '-' }}
        </div>
      </div>
    </header>

    <!-- Article Prose Content -->
    <article class="prose">
      {!! $article->renderHtml() !!}
    </article>

    <!-- Editorial Trust & E-E-A-T Disclosure -->
    <div class="editorial-trust-box">
      <div class="editorial-trust-avatar" aria-hidden="true">DA</div>
      <div class="editorial-trust-content">
        <h4>Ditulis & Ditinjau oleh {{ $article->author_name }}</h4>
        <p>Artikel di LokaTaut disusun dengan riset faktual seputar rekayasa tautan, keamanan siber, dan kinerja web. Seluruh panduan diuji langsung pada skenario nyata untuk memastikan keakuratan informasi.</p>
      </div>
    </div>

    <!-- Social Share & Action Bar -->
    <div class="article-share-bar">
      <div style="font-size: 14px; font-weight: 700; color: var(--text-primary);">Bagikan Artikel Ini:</div>
      <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
        <button type="button" class="btn btn-secondary btn-sm" data-copy-share="{{ route('blog.show', $article->slug) }}">
          Salin Tautan
        </button>
        <a href="https://api.whatsapp.com/send?text={{ urlencode($article->title . ' ' . route('blog.show', $article->slug)) }}" target="_blank" rel="noopener noreferrer" class="btn btn-secondary btn-sm">
          WhatsApp
        </a>
        <a href="https://twitter.com/intent/tweet?text={{ urlencode($article->title) }}&url={{ urlencode(route('blog.show', $article->slug)) }}" target="_blank" rel="noopener noreferrer" class="btn btn-secondary btn-sm">
          X (Twitter)
        </a>
      </div>
    </div>

    <!-- Prev & Next Article Navigation -->
    <div class="article-nav-grid">
      @if ($previousArticle)
        <a href="{{ route('blog.show', $previousArticle->slug) }}" class="article-nav-card">
          <span style="font-size: 12px; color: var(--text-muted); margin-bottom: 4px;">&larr; Artikel Sebelumnya</span>
          <strong style="font-size: 14px; color: var(--text-primary); line-height: 1.4;">{{ $previousArticle->title }}</strong>
        </a>
      @else
        <div></div>
      @endif

      @if ($nextArticle)
        <a href="{{ route('blog.show', $nextArticle->slug) }}" class="article-nav-card" style="text-align: right;">
          <span style="font-size: 12px; color: var(--text-muted); margin-bottom: 4px;">Artikel Selanjutnya &rarr;</span>
          <strong style="font-size: 14px; color: var(--text-primary); line-height: 1.4;">{{ $nextArticle->title }}</strong>
        </a>
      @endif
    </div>

    <!-- Related Articles -->
    @if ($relatedArticles->isNotEmpty())
      <div style="margin-top: 64px; padding-top: 40px; border-top: 1px solid var(--border-subtle);">
        <h3 style="font-size: 22px; font-weight: 800; margin-bottom: 24px; color: var(--text-primary);">Artikel Terkait Lainnya</h3>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px;">
          @foreach ($relatedArticles as $rel)
            <div style="background: var(--bg-surface); border: 1px solid var(--border-subtle); border-radius: var(--radius-md); padding: 20px; display: flex; flex-direction: column;">
              <span class="category-tag" style="align-self: flex-start; margin-bottom: 8px;">{{ $rel->category->name ?? 'Umum' }}</span>
              <h4 style="font-size: 15px; font-weight: 700; line-height: 1.4; margin-bottom: 8px;">
                <a href="{{ route('blog.show', $rel->slug) }}" style="color: inherit;">{{ $rel->title }}</a>
              </h4>
              <span style="font-size: 12px; color: var(--text-muted); margin-top: auto;">{{ $rel->published_at ? $rel->published_at->translatedFormat('d M Y') : '' }}</span>
            </div>
          @endforeach
        </div>
      </div>
    @endif
  </div>
</div>
@endsection
