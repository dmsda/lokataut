{{-- Inline Theme Initializer to prevent Flash of Unstyled Theme (FOUT) --}}
<script>
  (function() {
    try {
      var saved = localStorage.getItem('lokataut_theme') || 'system';
      var prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
      var effective = saved === 'system' ? (prefersDark ? 'dark' : 'light') : saved;
      document.documentElement.setAttribute('data-theme', effective);
      document.documentElement.setAttribute('data-theme-preference', saved);
      document.documentElement.style.colorScheme = effective;
    } catch (e) {}
  })();
</script>

