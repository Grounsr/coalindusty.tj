/* =====================================================================
   COAL FORUM — Front-end (vanilla JS, ~6KB)
   ===================================================================== */
(function () {
  'use strict';
  // Add js-reveal only if user did not prefer reduced motion
  if (!window.matchMedia || !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
    document.documentElement.classList.add('js-reveal');
  }

  // --- Sticky header ---
  const header = document.getElementById('siteHeader');
  if (header) {
    const onScroll = () => header.classList.toggle('is-scrolled', window.scrollY > 20);
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
  }

  // --- Mobile menu ---
  const toggle = document.getElementById('menuToggle');
  const mobile = document.getElementById('mobileMenu');
  if (toggle && mobile) {
    toggle.addEventListener('click', () => {
      const open = mobile.classList.toggle('is-open');
      toggle.classList.toggle('is-open', open);
      toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
      document.body.style.overflow = open ? 'hidden' : '';
    });
    mobile.querySelectorAll('a').forEach(a => a.addEventListener('click', () => {
      mobile.classList.remove('is-open');
      toggle.classList.remove('is-open');
      document.body.style.overflow = '';
    }));
  }

  // --- Countdown ---
  const targetAttr = document.body.dataset.forumDate;
  if (targetAttr) {
    const target = new Date(targetAttr + 'T09:00:00+05:00').getTime();
    const el = {
      d: document.getElementById('cd-days'),
      h: document.getElementById('cd-hours'),
      m: document.getElementById('cd-minutes'),
      s: document.getElementById('cd-seconds'),
    };
    if (el.d) {
      const tick = () => {
        const diff = Math.max(0, target - Date.now());
        const d = Math.floor(diff / 86400000);
        const h = Math.floor((diff % 86400000) / 3600000);
        const m = Math.floor((diff % 3600000) / 60000);
        const s = Math.floor((diff % 60000) / 1000);
        el.d.textContent = String(d).padStart(3, '0');
        el.h.textContent = String(h).padStart(2, '0');
        el.m.textContent = String(m).padStart(2, '0');
        el.s.textContent = String(s).padStart(2, '0');
      };
      tick();
      setInterval(tick, 1000);
    }
  }

  // --- Animated stats (count up on view) ---
  const counters = document.querySelectorAll('.stat-number[data-count]');
  if (counters.length) {
    const reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const setFinal = (el) => { el.textContent = (+el.dataset.count) + (el.dataset.suffix || ''); };
    if (reduce || !('IntersectionObserver' in window)) {
      counters.forEach(setFinal);
    } else {
      const seen = new WeakSet();
      const animate = (el) => {
        if (seen.has(el)) return;
        seen.add(el);
        const target = +el.dataset.count;
        const suffix = el.dataset.suffix || '';
        const start = performance.now();
        const step = (now) => {
          const t = Math.min(1, (now - start) / 1600);
          const eased = 1 - Math.pow(1 - t, 3);
          el.textContent = Math.round(target * eased) + suffix;
          if (t < 1) requestAnimationFrame(step);
        };
        requestAnimationFrame(step);
      };
      const io = new IntersectionObserver(entries => entries.forEach(e => { if (e.isIntersecting) animate(e.target); }), { threshold: 0.4 });
      counters.forEach(c => io.observe(c));
    }
  }

  // --- Reveal on scroll ---
  const reveals = document.querySelectorAll('.reveal');
  if (reveals.length && 'IntersectionObserver' in window) {
    const ro = new IntersectionObserver(entries => {
      entries.forEach(e => {
        if (e.isIntersecting) {
          e.target.classList.add('is-visible');
          ro.unobserve(e.target);
        }
      });
    }, { threshold: 0.15 });
    reveals.forEach(r => ro.observe(r));
  } else {
    reveals.forEach(r => r.classList.add('is-visible'));
  }

  // --- File input previews (registration page) ---
  const photoInput = document.getElementById('photo_file');
  const passportInput = document.getElementById('passport_file');

  const updatePreview = (input, previewId, labelId, isImage) => {
    if (!input) return;
    input.addEventListener('change', () => {
      const file = input.files && input.files[0];
      const preview = document.getElementById(previewId);
      const label = document.getElementById(labelId);
      if (!file) { preview.style.display = 'none'; preview.innerHTML = ''; return; }
      if (label) label.textContent = '✓ ' + file.name;
      preview.style.display = 'block';
      preview.innerHTML = '';
      const isImg = file.type.startsWith('image/');
      if (isImg) {
        const img = document.createElement('img');
        img.src = URL.createObjectURL(file);
        preview.appendChild(img);
      }
      const name = document.createElement('div');
      name.className = 'file-name';
      const sizeMb = (file.size / 1024 / 1024).toFixed(2);
      name.textContent = file.name + ' · ' + sizeMb + ' MB';
      preview.appendChild(name);
    });
  };
  updatePreview(photoInput, 'photo_preview', 'photo_label_text', true);
  updatePreview(passportInput, 'passport_preview', 'passport_label_text', false);

  // --- 6-digit verification code input ---
  const digits = document.querySelectorAll('.code-digit');
  const codeFinal = document.getElementById('codeFinal');
  if (digits.length === 6 && codeFinal) {
    const updateFinal = () => {
      codeFinal.value = Array.from(digits).map(d => d.value).join('');
    };
    digits.forEach((d, i) => {
      d.addEventListener('input', () => {
        d.value = d.value.replace(/\D/g, '').slice(-1);
        if (d.value && i < 5) digits[i + 1].focus();
        updateFinal();
      });
      d.addEventListener('keydown', (e) => {
        if (e.key === 'Backspace' && !d.value && i > 0) digits[i - 1].focus();
      });
      d.addEventListener('paste', (e) => {
        e.preventDefault();
        const text = (e.clipboardData.getData('text') || '').replace(/\D/g, '').slice(0, 6);
        text.split('').forEach((c, idx) => {
          if (digits[idx]) digits[idx].value = c;
        });
        if (digits[Math.min(text.length, 5)]) digits[Math.min(text.length, 5)].focus();
        updateFinal();
      });
    });
    digits[0].focus();
  }

  // --- Smooth scroll on anchors ---
  document.querySelectorAll('a[href^="#"]').forEach(a => {
    a.addEventListener('click', e => {
      const id = a.getAttribute('href').slice(1);
      if (!id) return;
      const target = document.getElementById(id);
      if (target) {
        e.preventDefault();
        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }
    });
  });

})();
