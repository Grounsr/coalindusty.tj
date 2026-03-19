/* app.js — Navigation, Countdown, Animations, Forms, Mobile Menu */

const API = "port/8000".startsWith("__") ? "http://localhost:8000" : "port/8000";

document.addEventListener('DOMContentLoaded', () => {
  initI18n();
  initStickyHeader();
  initMobileMenu();
  initCountdown();
  initScrollAnimations();
  initAnimatedCounters();
  initSmoothScroll();
  initRegistrationForm();
  initParallax();
  setActiveNav();
});

/* ============================================
   STICKY HEADER
   ============================================ */
function initStickyHeader() {
  const header = document.querySelector('.site-header');
  if (!header) return;
  
  let lastScroll = 0;
  window.addEventListener('scroll', () => {
    const scrollY = window.scrollY;
    header.classList.toggle('scrolled', scrollY > 50);
    lastScroll = scrollY;
  }, { passive: true });
}

/* ============================================
   MOBILE MENU
   ============================================ */
function initMobileMenu() {
  const toggle = document.querySelector('.menu-toggle');
  const menu = document.querySelector('.mobile-menu');
  if (!toggle || !menu) return;

  toggle.addEventListener('click', () => {
    const isOpen = toggle.classList.toggle('open');
    menu.classList.toggle('open', isOpen);
    toggle.setAttribute('aria-expanded', isOpen);
    document.body.style.overflow = isOpen ? 'hidden' : '';
  });

  // Close on link click
  menu.querySelectorAll('a').forEach(link => {
    link.addEventListener('click', () => {
      toggle.classList.remove('open');
      menu.classList.remove('open');
      toggle.setAttribute('aria-expanded', 'false');
      document.body.style.overflow = '';
    });
  });

  // Close on escape
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && menu.classList.contains('open')) {
      toggle.classList.remove('open');
      menu.classList.remove('open');
      toggle.setAttribute('aria-expanded', 'false');
      document.body.style.overflow = '';
    }
  });
}

/* ============================================
   COUNTDOWN TIMER
   ============================================ */
function initCountdown() {
  const countdownEl = document.querySelector('.countdown');
  if (!countdownEl) return;

  const targetDate = new Date('2026-11-25T09:00:00+05:00').getTime(); // Dushanbe is UTC+5

  function update() {
    const now = Date.now();
    const diff = targetDate - now;

    if (diff <= 0) {
      const daysEl = document.getElementById('countdown-days');
      const hoursEl = document.getElementById('countdown-hours');
      const minutesEl = document.getElementById('countdown-minutes');
      const secondsEl = document.getElementById('countdown-seconds');
      if (daysEl) daysEl.textContent = '0';
      if (hoursEl) hoursEl.textContent = '0';
      if (minutesEl) minutesEl.textContent = '0';
      if (secondsEl) secondsEl.textContent = '0';
      return;
    }

    const days = Math.floor(diff / (1000 * 60 * 60 * 24));
    const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
    const seconds = Math.floor((diff % (1000 * 60)) / 1000);

    const daysEl = document.getElementById('countdown-days');
    const hoursEl = document.getElementById('countdown-hours');
    const minutesEl = document.getElementById('countdown-minutes');
    const secondsEl = document.getElementById('countdown-seconds');

    if (daysEl) daysEl.textContent = days;
    if (hoursEl) hoursEl.textContent = String(hours).padStart(2, '0');
    if (minutesEl) minutesEl.textContent = String(minutes).padStart(2, '0');
    if (secondsEl) secondsEl.textContent = String(seconds).padStart(2, '0');
  }

  update();
  setInterval(update, 1000);
}

/* ============================================
   SCROLL ANIMATIONS (IntersectionObserver)
   ============================================ */
function initScrollAnimations() {
  const animatedElements = document.querySelectorAll('.fade-in, .fade-in-left, .fade-in-right');
  if (!animatedElements.length) return;

  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('visible');
        observer.unobserve(entry.target);
      }
    });
  }, {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
  });

  animatedElements.forEach(el => observer.observe(el));
}

/* ============================================
   ANIMATED NUMBER COUNTERS
   ============================================ */
function initAnimatedCounters() {
  const counters = document.querySelectorAll('[data-count]');
  if (!counters.length) return;

  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        animateCounter(entry.target);
        observer.unobserve(entry.target);
      }
    });
  }, { threshold: 0.5 });

  counters.forEach(el => observer.observe(el));
}

function animateCounter(el) {
  const target = parseInt(el.dataset.count, 10);
  const suffix = el.dataset.suffix || '';
  const prefix = el.dataset.prefix || '';
  const duration = 2000;
  const startTime = performance.now();

  function step(currentTime) {
    const elapsed = currentTime - startTime;
    const progress = Math.min(elapsed / duration, 1);
    // Ease out cubic
    const easedProgress = 1 - Math.pow(1 - progress, 3);
    const current = Math.floor(easedProgress * target);
    el.textContent = prefix + current + suffix;

    if (progress < 1) {
      requestAnimationFrame(step);
    } else {
      el.textContent = prefix + target + suffix;
    }
  }

  requestAnimationFrame(step);
}

/* ============================================
   SMOOTH SCROLL
   ============================================ */
function initSmoothScroll() {
  document.querySelectorAll('a[href^="#"]').forEach(link => {
    link.addEventListener('click', (e) => {
      const targetId = link.getAttribute('href');
      if (targetId === '#') return;
      const targetEl = document.querySelector(targetId);
      if (targetEl) {
        e.preventDefault();
        targetEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }
    });
  });
}

/* ============================================
   REGISTRATION FORM
   ============================================ */
function initRegistrationForm() {
  const form = document.getElementById('registration-form');
  if (!form) return;

  form.addEventListener('submit', async (e) => {
    e.preventDefault();

    // Clear errors
    form.querySelectorAll('.form-error').forEach(el => el.classList.remove('visible'));
    form.querySelectorAll('.form-input, .form-select').forEach(el => el.classList.remove('error'));

    // Validate
    let isValid = true;
    const required = form.querySelectorAll('[required]');
    required.forEach(input => {
      if (!input.value.trim()) {
        isValid = false;
        input.classList.add('error');
        const errorEl = input.parentElement.querySelector('.form-error');
        if (errorEl) {
          const lang = window.CoalForumI18n ? window.CoalForumI18n.getCurrentLang() : 'ru';
          const t = window.CoalForumI18n ? window.CoalForumI18n.translations[lang] : {};
          errorEl.textContent = t['form.required'] || 'This field is required';
          errorEl.classList.add('visible');
        }
      }
    });

    // Email validation
    const emailInput = form.querySelector('[name="email"]');
    if (emailInput && emailInput.value.trim()) {
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!emailRegex.test(emailInput.value.trim())) {
        isValid = false;
        emailInput.classList.add('error');
        const errorEl = emailInput.parentElement.querySelector('.form-error');
        if (errorEl) {
          const lang = window.CoalForumI18n ? window.CoalForumI18n.getCurrentLang() : 'ru';
          const t = window.CoalForumI18n ? window.CoalForumI18n.translations[lang] : {};
          errorEl.textContent = t['form.email.invalid'] || 'Please enter a valid email address';
          errorEl.classList.add('visible');
        }
      }
    }

    if (!isValid) return;

    // Submit
    const submitBtn = form.querySelector('[type="submit"]');
    const originalText = submitBtn.textContent;
    const lang = window.CoalForumI18n ? window.CoalForumI18n.getCurrentLang() : 'ru';
    const t = window.CoalForumI18n ? window.CoalForumI18n.translations[lang] : {};
    submitBtn.textContent = t['form.submitting'] || 'Submitting...';
    submitBtn.disabled = true;

    const formData = {
      full_name: form.querySelector('[name="full_name"]').value.trim(),
      organization: form.querySelector('[name="organization"]').value.trim(),
      position: form.querySelector('[name="position"]').value.trim(),
      country: form.querySelector('[name="country"]').value.trim(),
      city: form.querySelector('[name="city"]').value.trim(),
      email: form.querySelector('[name="email"]').value.trim(),
      phone: form.querySelector('[name="phone"]').value.trim(),
      participation_type: form.querySelector('[name="participation_type"]').value,
    };

    try {
      const res = await fetch(`${API}/api/register`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(formData),
      });

      if (res.ok) {
        form.style.display = 'none';
        const successEl = document.querySelector('.form-success');
        if (successEl) successEl.classList.add('visible');
      } else {
        const data = await res.json().catch(() => ({}));
        alert(data.detail || t['form.error.general'] || 'An error occurred.');
        submitBtn.textContent = originalText;
        submitBtn.disabled = false;
      }
    } catch (err) {
      alert(t['form.error.general'] || 'An error occurred. Please try again later.');
      submitBtn.textContent = originalText;
      submitBtn.disabled = false;
    }
  });

  // Live validation — remove error on input
  form.querySelectorAll('.form-input, .form-select').forEach(input => {
    input.addEventListener('input', () => {
      input.classList.remove('error');
      const errorEl = input.parentElement.querySelector('.form-error');
      if (errorEl) errorEl.classList.remove('visible');
    });
  });
}

/* ============================================
   PARALLAX (subtle hero)
   ============================================ */
function initParallax() {
  const heroBg = document.querySelector('.hero-bg img');
  if (!heroBg) return;

  let ticking = false;
  window.addEventListener('scroll', () => {
    if (!ticking) {
      requestAnimationFrame(() => {
        const scrollY = window.scrollY;
        const heroHeight = document.querySelector('.hero')?.offsetHeight || 800;
        if (scrollY < heroHeight) {
          heroBg.style.transform = `translateY(${scrollY * 0.3}px) scale(1.1)`;
        }
        ticking = false;
      });
      ticking = true;
    }
  }, { passive: true });
}

/* ============================================
   SET ACTIVE NAV LINK
   ============================================ */
function setActiveNav() {
  const currentPage = window.location.pathname.split('/').pop() || 'index.html';
  document.querySelectorAll('.main-nav a, .mobile-menu a').forEach(link => {
    const href = link.getAttribute('href');
    if (href) {
      const linkPage = href.split('?')[0];
      if (linkPage === currentPage || (currentPage === '' && linkPage === 'index.html')) {
        link.classList.add('active');
      }
    }
  });
}
