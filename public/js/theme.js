/**
 * LokaTaut Global Theme System
 * Supports Light, Dark, and System (OS preference) themes.
 * Zero Flash of Unstyled Theme (FOUT) via inline head initialization.
 */
(function () {
  'use strict';

  const STORAGE_KEY = 'lokataut_theme';

  /**
   * Determine the effective theme based on preference and OS setting.
   * @param {string} preference - 'light' | 'dark' | 'system'
   * @returns {'light' | 'dark'}
   */
  function resolveTheme(preference) {
    if (preference === 'dark' || preference === 'light') {
      return preference;
    }
    const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
    return prefersDark ? 'dark' : 'light';
  }

  /**
   * Apply the theme to documentElement and sync UI toggles.
   * @param {string} preference - 'light' | 'dark' | 'system'
   */
  function applyTheme(preference) {
    const effective = resolveTheme(preference);
    document.documentElement.setAttribute('data-theme', effective);
    document.documentElement.setAttribute('data-theme-preference', preference);
    
    // Set color-scheme for native browser controls (scrollbar, inputs)
    document.documentElement.style.colorScheme = effective;

    // Update all theme switch buttons on page
    updateToggleButtons(preference, effective);

    // Dispatch custom event for widgets (charts, Turnstile)
    window.dispatchEvent(new CustomEvent('themechanged', {
      detail: { preference: preference, effective: effective }
    }));
  }

  /**
   * Save preference and apply.
   * @param {string} preference - 'light' | 'dark' | 'system'
   */
  function setTheme(preference) {
    if (!['light', 'dark', 'system'].includes(preference)) {
      preference = 'system';
    }
    try {
      localStorage.setItem(STORAGE_KEY, preference);
    } catch (e) {
      console.warn('LokaTaut: localStorage inaccessible for theme storage', e);
    }
    applyTheme(preference);
  }

  /**
   * Retrieve current stored preference.
   * @returns {string} 'light' | 'dark' | 'system'
   */
  function getThemePreference() {
    try {
      return localStorage.getItem(STORAGE_KEY) || 'system';
    } catch (e) {
      return 'system';
    }
  }

  /**
   * Update active states on any theme switcher buttons present in DOM.
   */
  function updateToggleButtons(preference, effective) {
    const buttons = document.querySelectorAll('[data-theme-set]');
    buttons.forEach(function (btn) {
      const target = btn.getAttribute('data-theme-set');
      const isSelected = target === preference;
      btn.setAttribute('aria-pressed', isSelected ? 'true' : 'false');
      btn.classList.toggle('active', isSelected);
    });

    // Update any single-click toggle icons if present
    const toggleIcons = document.querySelectorAll('.theme-toggle-icon');
    toggleIcons.forEach(function (icon) {
      if (effective === 'dark') {
        icon.classList.remove('icon-sun');
        icon.classList.add('icon-moon');
      } else {
        icon.classList.remove('icon-moon');
        icon.classList.add('icon-sun');
      }
    });
  }

  // Listen for OS system theme changes
  if (window.matchMedia) {
    const mql = window.matchMedia('(prefers-color-scheme: dark)');
    const handleMediaChange = function () {
      const currentPref = getThemePreference();
      if (currentPref === 'system') {
        applyTheme('system');
      }
    };
    if (mql.addEventListener) {
      mql.addEventListener('change', handleMediaChange);
    } else if (mql.addListener) {
      mql.addListener(handleMediaChange);
    }
  }

  // Initialize event delegation for any [data-theme-set] clicks
  document.addEventListener('DOMContentLoaded', function () {
    const current = getThemePreference();
    applyTheme(current);

    document.addEventListener('click', function (e) {
      const btn = e.target.closest('[data-theme-set]');
      if (!btn) return;
      const targetTheme = btn.getAttribute('data-theme-set');
      if (targetTheme) {
        e.preventDefault();
        setTheme(targetTheme);
      }
    });
  });

  // Export globally to window
  window.LokaTautTheme = {
    set: setTheme,
    get: getThemePreference,
    resolve: resolveTheme,
    apply: applyTheme
  };
})();

