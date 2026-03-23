/* PSBoost Admin JS */
(function () {
  'use strict';

  const TOTAL_TOGGLES = document.querySelectorAll('.psb-checkbox').length;
  const CIRCUMFERENCE = 2 * Math.PI * 50; // r=50

  /* ---- Score ring ---- */
  function injectSVGGradient() {
    const svg = document.querySelector('.psb-ring-svg');
    if (!svg) return;
    const defs = document.createElementNS('http://www.w3.org/2000/svg', 'defs');
    defs.innerHTML = `
      <linearGradient id="psb-grad" x1="0%" y1="0%" x2="100%" y2="100%">
        <stop offset="0%" stop-color="#10b981"/>
        <stop offset="100%" stop-color="#6366f1"/>
      </linearGradient>`;
    svg.prepend(defs);
  }

  function updateScore() {
    const checked = document.querySelectorAll('.psb-checkbox:checked').length;
    const score   = Math.round((checked / TOTAL_TOGGLES) * 100);
    const fill    = (score / 100) * CIRCUMFERENCE;

    const numEl  = document.getElementById('psb-score');
    const ringEl = document.getElementById('psb-ring-fill');

    if (numEl)  numEl.textContent = score;
    if (ringEl) ringEl.style.strokeDasharray = fill + 'px ' + CIRCUMFERENCE + 'px';

    // Colour the number
    if (numEl) {
      if (score >= 90) {
        numEl.style.background = 'linear-gradient(135deg, #fff 0%, #86efac 100%)';
      } else if (score >= 50) {
        numEl.style.background = 'linear-gradient(135deg, #fff 0%, #fde68a 100%)';
      } else {
        numEl.style.background = 'linear-gradient(135deg, #fff 0%, #fca5a5 100%)';
      }
      numEl.style.webkitBackgroundClip = 'text';
      numEl.style.webkitTextFillColor  = 'transparent';
      numEl.style.backgroundClip       = 'text';
    }
  }

  /* ---- Row highlight ---- */
  function syncRowState(checkbox) {
    const row = checkbox.closest('.psb-toggle-row');
    if (!row) return;
    if (checkbox.checked) {
      row.classList.add('psb-enabled');
    } else {
      row.classList.remove('psb-enabled');
    }
  }

  /* ---- Bulk actions ---- */
  function setAll(state) {
    document.querySelectorAll('.psb-checkbox').forEach(cb => {
      cb.checked = state;
      syncRowState(cb);
    });
    updateScore();
  }

  /* ---- Init ---- */
  document.addEventListener('DOMContentLoaded', function () {
    injectSVGGradient();
    updateScore(); // animate in on load

    // Checkbox interactions
    document.querySelectorAll('.psb-checkbox').forEach(cb => {
      cb.addEventListener('change', function () {
        syncRowState(this);
        updateScore();
      });
    });

    // Enable / Disable All
    const enableBtn  = document.getElementById('psb-enable-all');
    const disableBtn = document.getElementById('psb-disable-all');
    if (enableBtn)  enableBtn.addEventListener('click', () => setAll(true));
    if (disableBtn) disableBtn.addEventListener('click', () => setAll(false));

    // Animate score ring on load
    const ring = document.getElementById('psb-ring-fill');
    if (ring) {
      ring.style.transition = 'stroke-dasharray 1.2s cubic-bezier(0.4, 0, 0.2, 1)';
    }
  });
})();
