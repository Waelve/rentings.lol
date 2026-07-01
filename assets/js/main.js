/* ============================================================
   Rentings.lol — JavaScript principal
   ============================================================ */

document.addEventListener('DOMContentLoaded', () => {

  // ── Navbar scroll effect
  const navbar = document.getElementById('navbar');
  if (navbar) {
    window.addEventListener('scroll', () => {
      navbar.classList.toggle('scrolled', window.scrollY > 40);
    }, { passive: true });
  }

  // ── Mobile hamburger menu
  const btnHamburger = document.getElementById('btnHamburger');
  const mobileMenu   = document.getElementById('mobileMenu');
  if (btnHamburger && mobileMenu) {
    btnHamburger.addEventListener('click', () => {
      const open = mobileMenu.classList.toggle('open');
      btnHamburger.classList.toggle('open', open);
      btnHamburger.setAttribute('aria-expanded', open);
    });
    // Close on outside click
    document.addEventListener('click', (e) => {
      if (!navbar.contains(e.target)) {
        mobileMenu.classList.remove('open');
        btnHamburger.classList.remove('open');
        btnHamburger.setAttribute('aria-expanded', 'false');
      }
    });
  }

  // ── User dropdown menu
  const btnUserMenu   = document.getElementById('btnUserMenu');
  const userDropdown  = document.querySelector('.nav-user-menu');
  if (btnUserMenu && userDropdown) {
    btnUserMenu.addEventListener('click', (e) => {
      e.stopPropagation();
      userDropdown.classList.toggle('open');
    });
    document.addEventListener('click', () => {
      userDropdown.classList.remove('open');
    });
  }

  // ── Search tabs
  document.querySelectorAll('.search-tab').forEach(tab => {
    tab.addEventListener('click', () => {
      document.querySelectorAll('.search-tab').forEach(t => t.classList.remove('active'));
      tab.classList.add('active');
      const op = document.getElementById('searchOperacion');
      if (op) op.value = tab.dataset.op || '';
    });
  });

  // ── Smooth counter animation
  function animateCounter(el) {
    const target = parseFloat(el.dataset.target || el.textContent.replace(/[^0-9.]/g,''));
    const suffix = el.dataset.suffix || '';
    const prefix = el.dataset.prefix || '';
    const duration = 1600;
    const start = performance.now();
    const isDecimal = target % 1 !== 0;

    function update(now) {
      const elapsed = now - start;
      const progress = Math.min(elapsed / duration, 1);
      const eased = 1 - Math.pow(1 - progress, 4);
      const current = target * eased;
      el.textContent = prefix + (isDecimal ? current.toFixed(1) : Math.round(current)) + suffix;
      if (progress < 1) requestAnimationFrame(update);
    }
    requestAnimationFrame(update);
  }

  // Intersection Observer for counters
  const counters = document.querySelectorAll('[data-counter]');
  if (counters.length) {
    const obs = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          animateCounter(entry.target);
          obs.unobserve(entry.target);
        }
      });
    }, { threshold: 0.5 });
    counters.forEach(c => obs.observe(c));
  }

  // ── Flash messages auto-dismiss
  document.querySelectorAll('.alert').forEach(alert => {
    const closeBtn = alert.querySelector('.alert-close');
    if (closeBtn) {
      closeBtn.addEventListener('click', () => {
        alert.style.opacity = '0';
        alert.style.transform = 'translateY(-8px)';
        alert.style.transition = 'all .3s ease';
        setTimeout(() => alert.remove(), 300);
      });
    }
    // Auto-dismiss after 6s
    setTimeout(() => {
      if (alert.isConnected) {
        alert.style.opacity = '0';
        alert.style.transform = 'translateY(-8px)';
        alert.style.transition = 'all .4s ease';
        setTimeout(() => alert.remove(), 400);
      }
    }, 6000);
  });

  // ── Image gallery (propiedad detalle)
  const thumbs = document.querySelectorAll('.gallery-thumb');
  const mainImg = document.getElementById('mainImage');
  if (thumbs.length && mainImg) {
    thumbs.forEach(thumb => {
      thumb.addEventListener('click', () => {
        mainImg.src = thumb.src;
        thumbs.forEach(t => t.classList.remove('active'));
        thumb.classList.add('active');
      });
    });
  }

  // ── Form validation
  document.querySelectorAll('form[data-validate]').forEach(form => {
    form.addEventListener('submit', (e) => {
      let valid = true;
      form.querySelectorAll('[required]').forEach(field => {
        const err = field.parentElement.querySelector('.form-error');
        if (!field.value.trim()) {
          valid = false;
          field.classList.add('error');
          if (err) err.textContent = 'Este campo es obligatorio.';
        } else {
          field.classList.remove('error');
          if (err) err.textContent = '';
        }
        // Email validation
        if (field.type === 'email' && field.value) {
          const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
          if (!re.test(field.value)) {
            valid = false;
            field.classList.add('error');
            if (err) err.textContent = 'Ingresa un correo válido.';
          }
        }
      });
      if (!valid) e.preventDefault();
    });
  });

  // ── Password toggle
  document.querySelectorAll('.toggle-password').forEach(btn => {
    btn.addEventListener('click', () => {
      const input = btn.previousElementSibling;
      if (!input) return;
      if (input.type === 'password') {
        input.type = 'text';
        btn.innerHTML = '🙈';
      } else {
        input.type = 'password';
        btn.innerHTML = '👁';
      }
    });
  });

  // ── Lazy load images
  const lazyImgs = document.querySelectorAll('img[data-src]');
  if (lazyImgs.length) {
    const imgObs = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          const img = entry.target;
          img.src = img.dataset.src;
          img.removeAttribute('data-src');
          imgObs.unobserve(img);
        }
      });
    });
    lazyImgs.forEach(img => imgObs.observe(img));
  }

  // ── Sticky search bar
  const searchCard = document.querySelector('.search-card');
  if (searchCard) {
    const searchObs = new IntersectionObserver(([entry]) => {
      document.body.classList.toggle('search-stuck', !entry.isIntersecting);
    }, { threshold: 0, rootMargin: '-68px 0px 0px 0px' });
    searchObs.observe(searchCard);
  }

  // ── Tooltips simples
  document.querySelectorAll('[data-tooltip]').forEach(el => {
    el.addEventListener('mouseenter', () => {
      const tip = document.createElement('div');
      tip.className = 'tooltip';
      tip.textContent = el.dataset.tooltip;
      tip.style.cssText = `
        position:fixed;background:#1a1a2e;color:#fff;padding:6px 12px;
        border-radius:6px;font-size:.78rem;white-space:nowrap;z-index:9999;
        pointer-events:none;box-shadow:0 4px 12px rgba(0,0,0,.3);
      `;
      document.body.appendChild(tip);
      const rect = el.getBoundingClientRect();
      tip.style.top  = (rect.top - tip.offsetHeight - 8) + 'px';
      tip.style.left = (rect.left + rect.width/2 - tip.offsetWidth/2) + 'px';
      el._tooltip = tip;
    });
    el.addEventListener('mouseleave', () => {
      if (el._tooltip) { el._tooltip.remove(); el._tooltip = null; }
    });
  });

});
