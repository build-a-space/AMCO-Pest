// Amco Pest Solutions – site behaviour. Vanilla JS, no dependencies.
(function () {
  'use strict';

  // Mobile navigation
  const toggle = document.querySelector('.nav-toggle');
  if (toggle) {
    toggle.addEventListener('click', () => {
      const open = document.body.classList.toggle('nav-open');
      toggle.setAttribute('aria-expanded', String(open));
    });
  }
  document.querySelectorAll('.sub-toggle').forEach((btn) => {
    btn.addEventListener('click', () => {
      const li = btn.closest('li');
      const open = li.classList.toggle('sub-open');
      btn.setAttribute('aria-expanded', String(open));
    });
  });

  // Live filter for pest library / blog lists
  document.querySelectorAll('[data-filter]').forEach((input) => {
    const scope = document.querySelector(input.dataset.filter);
    if (!scope) return;
    input.addEventListener('input', () => {
      const q = input.value.trim().toLowerCase();
      scope.querySelectorAll('[data-filter-item]').forEach((el) => {
        el.hidden = q !== '' && !el.textContent.toLowerCase().includes(q);
      });
      scope.querySelectorAll('[data-filter-group]').forEach((g) => {
        g.hidden = !g.querySelector('[data-filter-item]:not([hidden])');
      });
    });
  });

  // Lead forms: submit with fetch, show inline errors, fall back to normal POST.
  document.querySelectorAll('[data-lead-form]').forEach((form) => {
    form.addEventListener('submit', async (e) => {
      const status = form.querySelector('.lead-form__status');
      form.querySelectorAll('.field-error').forEach((n) => n.remove());
      form.querySelectorAll('.has-error').forEach((n) => n.classList.remove('has-error'));

      const name = form.elements.name.value.trim();
      const phone = form.elements.phone.value.trim();
      const email = form.elements.email.value.trim();
      const errors = {};
      if (!name) errors.name = 'Please enter your name.';
      if (!phone && !email) errors.phone = 'Please give us a phone number or email address.';
      if (Object.keys(errors).length) {
        e.preventDefault();
        showErrors(form, errors);
        return;
      }

      if (!window.fetch) return; // let the browser post normally
      e.preventDefault();
      const btn = form.querySelector('[type=submit]');
      btn.disabled = true;
      status.className = 'lead-form__status';
      status.textContent = 'Sending…';
      try {
        const res = await fetch(form.action, {
          method: 'POST',
          headers: { Accept: 'application/json' },
          body: new FormData(form),
        });
        const data = await res.json();
        status.textContent = data.message;
        status.classList.add(data.ok ? 'ok' : 'err');
        if (data.ok) form.reset();
        else if (data.errors) showErrors(form, data.errors);
      } catch (err) {
        status.textContent = 'Sorry, something went wrong. Please call us instead.';
        status.classList.add('err');
      } finally {
        btn.disabled = false;
      }
    });
  });

  function showErrors(form, errors) {
    Object.entries(errors).forEach(([field, msg]) => {
      const input = form.elements[field];
      if (!input) return;
      const wrap = input.closest('.field');
      wrap.classList.add('has-error');
      const p = document.createElement('p');
      p.className = 'field-error';
      p.textContent = msg;
      wrap.appendChild(p);
    });
    const first = form.querySelector('.has-error input');
    if (first) first.focus();
  }
})();

// Homepage tabs (Residential / Commercial / Property Managers)
document.querySelectorAll('[data-tabs]').forEach((wrap) => {
  const tabs = [...wrap.querySelectorAll('[role="tab"]')];
  const select = (tab) => {
    tabs.forEach((t) => {
      const on = t === tab;
      t.setAttribute('aria-selected', String(on));
      t.tabIndex = on ? 0 : -1;
      document.getElementById(t.getAttribute('aria-controls')).hidden = !on;
    });
  };
  tabs.forEach((t, i) => {
    t.addEventListener('click', () => select(t));
    t.addEventListener('keydown', (e) => {
      if (e.key !== 'ArrowRight' && e.key !== 'ArrowLeft') return;
      const next = tabs[(i + (e.key === 'ArrowRight' ? 1 : tabs.length - 1)) % tabs.length];
      select(next);
      next.focus();
    });
  });
});

// "Why Choose Us" video: pick a random pest video and autoplay it while in view.
document.querySelectorAll('[data-pest-videos]').forEach((box) => {
  let list;
  try { list = JSON.parse(box.dataset.pestVideos); } catch (e) { return; }
  if (!list.length) return;
  const [name, url, link] = list[Math.floor(Math.random() * list.length)];
  const iframe = box.querySelector('iframe');
  const linkEl = box.querySelector('[data-video-link]');
  box.querySelector('[data-video-title]').textContent = 'Learn About ' + name;
  linkEl.textContent = 'Learn More About ' + name;
  linkEl.href = link;
  iframe.title = 'Learn about ' + name;
  iframe.src = url;
  if (!('IntersectionObserver' in window)) return;
  new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
      const playing = iframe.src.includes('&autoplay=1');
      if (entry.isIntersecting && !playing) iframe.src = url + '&autoplay=1';
      else if (!entry.isIntersecting && playing) iframe.src = url;
    });
  }, { threshold: 0.5 }).observe(box);
});
