(function () {
  'use strict';

  const cookie = {
    set(name, value, days) {
      const expires = new Date(Date.now() + (days || 365) * 864e5).toUTCString();
      document.cookie = `${name}=${encodeURIComponent(value)}; expires=${expires}; path=/; SameSite=Lax`;
    },
    get(name) {
      return document.cookie.split('; ').reduce((found, part) => {
        const [key, value] = part.split('=');
        return key === name ? decodeURIComponent(value) : found;
      }, null);
    }
  };

  function applyTheme(theme) {
    document.documentElement.setAttribute('data-theme', theme);
    document.querySelectorAll('[data-theme-toggle]').forEach((button) => {
      button.setAttribute('aria-pressed', theme === 'dark' ? 'true' : 'false');
      const label = button.querySelector('[data-theme-label]');
      if (label) {
        label.textContent = button.dataset[theme === 'dark' ? 'labelLight' : 'labelDark'] || label.textContent;
      }
    });
    if (window.FlavorCharts) {
      window.FlavorCharts.repaint();
    }
  }

  function initTheme() {
    const stored = cookie.get('flavor_theme');
    const preferred = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
    applyTheme(stored || document.documentElement.getAttribute('data-theme') || preferred);

    document.querySelectorAll('[data-theme-toggle]').forEach((button) => {
      button.addEventListener('click', () => {
        const next = document.documentElement.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
        cookie.set('flavor_theme', next, 365);
        applyTheme(next);
      });
    });
  }

  function initToggles() {
    document.querySelectorAll('[data-toggle-target]').forEach((trigger) => {
      trigger.addEventListener('click', (event) => {
        event.stopPropagation();
        const target = document.querySelector(trigger.dataset.toggleTarget);
        if (!target) return;
        const open = target.classList.toggle('is-open');
        trigger.setAttribute('aria-expanded', open ? 'true' : 'false');
      });
    });

    document.addEventListener('click', (event) => {
      document.querySelectorAll('.menu-pop__panel.is-open').forEach((panel) => {
        if (!panel.parentElement.contains(event.target)) {
          panel.classList.remove('is-open');
        }
      });
    });

    document.addEventListener('keydown', (event) => {
      if (event.key !== 'Escape') return;
      document.querySelectorAll('.menu-pop__panel.is-open, .sidebar.is-open, .nav.is-open').forEach((element) => {
        element.classList.remove('is-open');
      });
    });
  }

  function initTabs() {
    document.querySelectorAll('[data-tabs]').forEach((group) => {
      const buttons = group.querySelectorAll('[data-tab]');
      buttons.forEach((button) => {
        button.addEventListener('click', () => {
          const panelId = button.dataset.tab;
          buttons.forEach((other) => other.classList.toggle('is-active', other === button));
          group.parentElement.querySelectorAll('.tab-panel').forEach((panel) => {
            panel.classList.toggle('is-active', panel.id === panelId);
          });
        });
      });
    });
  }

  function initQuantity() {
    document.querySelectorAll('[data-qty]').forEach((widget) => {
      const input = widget.querySelector('input');
      widget.querySelectorAll('button').forEach((button) => {
        button.addEventListener('click', () => {
          const step = Number(button.dataset.step || 1);
          const min = Number(input.min || 1);
          const max = Number(input.max || 100);
          input.value = Math.min(max, Math.max(min, Number(input.value || min) + step));
          input.dispatchEvent(new Event('change', { bubbles: true }));
        });
      });
    });
  }

  function initConfirm() {
    document.querySelectorAll('form[data-confirm]').forEach((form) => {
      form.addEventListener('submit', (event) => {
        if (!window.confirm(form.dataset.confirm)) {
          event.preventDefault();
        }
      });
    });
  }

  function initToasts() {
    document.querySelectorAll('.toast').forEach((toast) => {
      const close = toast.querySelector('.toast__close');
      const remove = () => toast.remove();
      if (close) close.addEventListener('click', remove);
      window.setTimeout(remove, 6000);
    });
  }

  function initReveal() {
    const items = document.querySelectorAll('.reveal');
    if (!items.length || !('IntersectionObserver' in window)) {
      items.forEach((item) => item.classList.add('is-visible'));
      return;
    }
    const observer = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add('is-visible');
          observer.unobserve(entry.target);
        }
      });
    }, { threshold: 0.12 });
    items.forEach((item) => observer.observe(item));
  }

  function initAutoSubmit() {
    document.querySelectorAll('[data-auto-submit]').forEach((control) => {
      control.addEventListener('change', () => control.form.submit());
    });
  }

  function initCookieBar() {
    const bar = document.querySelector('[data-cookie-bar]');
    if (!bar) return;
    if (cookie.get('flavor_cookie_consent')) {
      bar.remove();
      return;
    }
    bar.classList.remove('hidden');
    bar.querySelectorAll('[data-cookie-accept]').forEach((button) => {
      button.addEventListener('click', () => {
        cookie.set('flavor_cookie_consent', button.dataset.cookieAccept, 365);
        bar.remove();
      });
    });
  }

  const registry = [];

  window.FlavorCharts = {
    palette() {
      const styles = getComputedStyle(document.documentElement);
      return {
        primary: styles.getPropertyValue('--color-primary').trim(),
        secondary: styles.getPropertyValue('--color-secondary').trim(),
        info: styles.getPropertyValue('--color-info').trim(),
        success: styles.getPropertyValue('--color-success').trim(),
        warning: styles.getPropertyValue('--color-warning').trim(),
        danger: styles.getPropertyValue('--color-danger').trim(),
        purple: styles.getPropertyValue('--color-purple').trim(),
        border: styles.getPropertyValue('--color-border').trim(),
        text: styles.getPropertyValue('--color-text-secondary').trim()
      };
    },
    render(canvasId, factory) {
      const canvas = document.getElementById(canvasId);
      if (!canvas || typeof Chart === 'undefined') return;
      const entry = { canvasId, factory, instance: null };
      const build = () => {
        if (entry.instance) entry.instance.destroy();
        entry.instance = new Chart(canvas, factory(this.palette()));
      };
      entry.build = build;
      registry.push(entry);
      build();
    },
    repaint() {
      registry.forEach((entry) => entry.build());
    }
  };

  function initChartDefaults() {
    if (typeof Chart === 'undefined') return;
    const palette = window.FlavorCharts.palette();
    Chart.defaults.font.family = getComputedStyle(document.body).fontFamily;
    Chart.defaults.font.size = 12;
    Chart.defaults.color = palette.text;
    Chart.defaults.plugins.legend.labels.usePointStyle = true;
    Chart.defaults.plugins.legend.labels.boxWidth = 8;
    Chart.defaults.maintainAspectRatio = false;
  }

  document.addEventListener('DOMContentLoaded', () => {
    initTheme();
    initToggles();
    initTabs();
    initQuantity();
    initConfirm();
    initToasts();
    initReveal();
    initAutoSubmit();
    initCookieBar();
    initChartDefaults();
  });
})();
