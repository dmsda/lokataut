/**
 * LokaTaut Admin Dashboard Scripts
 * Vanilla ES6+ — Mobile Sidebar Drawer, Copy Helpers, Form Utilities, Confirmation Modals
 */
document.addEventListener('DOMContentLoaded', () => {
  'use strict';

  // ==========================================================================
  // 1. Mobile Sidebar Drawer Toggle & Backdrop
  // ==========================================================================
  const sidebar = document.getElementById('app-sidebar');
  const sidebarToggle = document.getElementById('admin-sidebar-toggle');
  const sidebarClose = document.getElementById('admin-sidebar-close');
  const sidebarBackdrop = document.getElementById('admin-sidebar-backdrop');

  function openSidebar() {
    if (!sidebar || !sidebarBackdrop) return;
    sidebar.classList.add('open');
    sidebarBackdrop.classList.add('open');
    document.body.style.overflow = 'hidden';
    if (sidebarToggle) sidebarToggle.setAttribute('aria-expanded', 'true');
  }

  function closeSidebar() {
    if (!sidebar || !sidebarBackdrop) return;
    sidebar.classList.remove('open');
    sidebarBackdrop.classList.remove('open');
    document.body.style.overflow = '';
    if (sidebarToggle) sidebarToggle.setAttribute('aria-expanded', 'false');
  }

  if (sidebarToggle) {
    sidebarToggle.addEventListener('click', openSidebar);
  }

  if (sidebarClose) {
    sidebarClose.addEventListener('click', closeSidebar);
  }

  if (sidebarBackdrop) {
    sidebarBackdrop.addEventListener('click', closeSidebar);
  }

  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && sidebar && sidebar.classList.contains('open')) {
      closeSidebar();
    }
  });

  // ==========================================================================
  // 2. Single-Click Copy Link Helper
  // ==========================================================================
  document.querySelectorAll('[data-copy-url]').forEach((btn) => {
    btn.addEventListener('click', async (e) => {
      e.preventDefault();
      const url = btn.getAttribute('data-copy-url');
      if (!url) return;

      try {
        await navigator.clipboard.writeText(url);
        const originalText = btn.innerHTML;
        btn.innerHTML = '<span style="color:var(--success); font-weight:700;">✓ Disalin!</span>';
        setTimeout(() => {
          btn.innerHTML = originalText;
        }, 2000);
      } catch (err) {
        prompt('Salin tautan ini secara manual:', url);
      }
    });
  });

  // ==========================================================================
  // 3. Auto-slug Generation Helper in Forms
  // ==========================================================================
  const titleInput = document.getElementById('title');
  const slugInput = document.getElementById('slug');

  if (titleInput && slugInput) {
    let slugManuallyEdited = slugInput.value.trim().length > 0;

    slugInput.addEventListener('input', () => {
      slugManuallyEdited = slugInput.value.trim().length > 0;
    });

    titleInput.addEventListener('input', () => {
      if (!slugManuallyEdited) {
        const val = titleInput.value
          .toLowerCase()
          .trim()
          .replace(/[^\w\s-]/g, '')
          .replace(/[\s_-]+/g, '-')
          .replace(/^-+|-+$/g, '')
          .substring(0, 40);
        slugInput.placeholder = val || 'acak-otomatis';
      }
    });
  }

  // ==========================================================================
  // 4. Form Deletion Confirmation Dialogs
  // ==========================================================================
  document.querySelectorAll('form[data-confirm]').forEach((form) => {
    form.addEventListener('submit', (e) => {
      const message = form.getAttribute('data-confirm') || 'Apakah Anda yakin ingin melanjutkan tindakan ini?';
      if (!confirm(message)) {
        e.preventDefault();
      }
    });
  });
});
