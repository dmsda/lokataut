/**
 * LokaTaut Safelink Client Engine - Vanilla JavaScript ES6+
 */
(() => {
  'use strict';

  document.addEventListener('DOMContentLoaded', () => {
    const config = window.__LOKATAUT_CONFIG__;
    if (!config) return;

    const verificationBox = document.getElementById('verification-box');
    const timerBox = document.getElementById('timer-box');
    const timerCount = document.getElementById('timer-count');
    const actionBox = document.getElementById('action-box');
    const proceedBtn = document.getElementById('proceed-btn');
    const alertError = document.getElementById('alert-error');

    let remainingSeconds = config.countdownSeconds || 10;
    let timerInterval = null;

    function showError(message) {
      if (alertError) {
        alertError.textContent = message;
        alertError.classList.add('active');
      }
    }

    function startCountdown() {
      if (verificationBox) verificationBox.style.display = 'none';
      if (timerBox) timerBox.classList.add('active');
      if (timerCount) timerCount.textContent = remainingSeconds;

      timerInterval = setInterval(() => {
        remainingSeconds--;
        if (timerCount) timerCount.textContent = remainingSeconds;

        if (remainingSeconds <= 0) {
          clearInterval(timerInterval);
          if (timerBox) timerBox.style.display = 'none';
          if (actionBox) actionBox.classList.add('active');
        }
      }, 1000);
    }

    // Cloudflare Turnstile Callback
    window.onTurnstileSuccess = async (turnstileToken) => {
      try {
        const response = await fetch(config.verifyUrl, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': config.csrfToken,
          },
          body: JSON.stringify({
            token: config.token,
            turnstile_token: turnstileToken,
          }),
        });

        const data = await response.json();

        if (response.ok && data.success) {
          startCountdown();
        } else {
          showError(data.message || 'Verifikasi keamanan gagal.');
        }
      } catch (err) {
        showError('Terjadi kesalahan koneksi saat verifikasi keamanan.');
      }
    };

    // If Turnstile mode is bypass, start countdown immediately
    if (config.turnstileMode === 'bypass') {
      startCountdown();
    }

    // Proceed Redemption Click Handler
    if (proceedBtn) {
      proceedBtn.addEventListener('click', async (e) => {
        e.preventDefault();
        proceedBtn.disabled = true;
        proceedBtn.innerHTML = '<span>Mengalihkan...</span>';

        try {
          const response = await fetch(config.proceedUrl, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'Accept': 'application/json',
              'X-CSRF-TOKEN': config.csrfToken,
            },
            body: JSON.stringify({
              token: config.token,
            }),
          });

          const data = await response.json();

          if (response.ok && data.success && data.redirect_url) {
            // Immediate secure browser redirect
            window.location.replace(data.redirect_url);
          } else {
            showError(data.message || 'Gagal memproses pengalihan tautan.');
            proceedBtn.disabled = false;
            proceedBtn.innerHTML = '<span>Buka Tautan Tujuan</span>';
          }
        } catch (err) {
          showError('Terjadi kesalahan jaringan saat membuka tautan.');
          proceedBtn.disabled = false;
          proceedBtn.innerHTML = '<span>Buka Tautan Tujuan</span>';
        }
      });
    }
  });
})();

