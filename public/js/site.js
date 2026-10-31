/**
 * LokaTaut Public Website Scripts
 * Vanilla ES6+ — Mobile navigation, FAQ accordion, clipboard copy, micro-interactions
 */
document.addEventListener('DOMContentLoaded', function () {
  'use strict';

  // ==========================================================================
  // 1. Mobile Navigation Drawer
  // ==========================================================================
  const mobileMenuBtn = document.getElementById('mobile-menu-btn');
  const mobileDrawer = document.getElementById('mobile-nav-drawer');
  const mobileBackdrop = document.getElementById('mobile-nav-backdrop');
  const mobileCloseBtn = document.getElementById('mobile-drawer-close');

  function openMobileNav() {
    if (!mobileDrawer || !mobileBackdrop) return;
    mobileDrawer.classList.add('open');
    mobileBackdrop.classList.add('open');
    document.body.style.overflow = 'hidden';
    if (mobileMenuBtn) mobileMenuBtn.setAttribute('aria-expanded', 'true');
  }

  function closeMobileNav() {
    if (!mobileDrawer || !mobileBackdrop) return;
    mobileDrawer.classList.remove('open');
    mobileBackdrop.classList.remove('open');
    document.body.style.overflow = '';
    if (mobileMenuBtn) mobileMenuBtn.setAttribute('aria-expanded', 'false');
  }

  if (mobileMenuBtn) {
    mobileMenuBtn.addEventListener('click', openMobileNav);
  }

  if (mobileCloseBtn) {
    mobileCloseBtn.addEventListener('click', closeMobileNav);
  }

  if (mobileBackdrop) {
    mobileBackdrop.addEventListener('click', closeMobileNav);
  }

  // Close drawer on Escape key
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && mobileDrawer && mobileDrawer.classList.contains('open')) {
      closeMobileNav();
    }
  });

  // ==========================================================================
  // 2. FAQ Accordion
  // ==========================================================================
  const faqItems = document.querySelectorAll('.faq-item');
  faqItems.forEach(function (item) {
    const questionBtn = item.querySelector('.faq-question');
    if (!questionBtn) return;

    questionBtn.addEventListener('click', function () {
      const isActive = item.classList.contains('active');

      // Close all other items in same group
      faqItems.forEach(function (other) {
        if (other !== item) {
          other.classList.remove('active');
          const otherBtn = other.querySelector('.faq-question');
          if (otherBtn) otherBtn.setAttribute('aria-expanded', 'false');
        }
      });

      // Toggle current
      item.classList.toggle('active', !isActive);
      questionBtn.setAttribute('aria-expanded', !isActive ? 'true' : 'false');
    });
  });

  // ==========================================================================
  // 3. Copy to Clipboard (Article Share & Link Utilities)
  // ==========================================================================
  const copyButtons = document.querySelectorAll('[data-copy-share]');
  copyButtons.forEach(function (btn) {
    btn.addEventListener('click', function () {
      const textToCopy = btn.getAttribute('data-copy-share');
      if (!textToCopy) return;

      navigator.clipboard.writeText(textToCopy).then(function () {
        const originalText = btn.innerHTML;
        btn.innerHTML = '<span style="color: var(--success); font-weight: 700;">✓ Tersalin!</span>';
        btn.disabled = true;

        setTimeout(function () {
          btn.innerHTML = originalText;
          btn.disabled = false;
        }, 2200);
      }).catch(function (err) {
        console.error('LokaTaut: Gagal menyalin tautan:', err);
      });
    });
  });
});
