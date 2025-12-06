var h = Object.defineProperty;
var u = (i, e, t) =>
  e in i ? h(i, e, { enumerable: !0, configurable: !0, writable: !0, value: t }) : (i[e] = t);
var c = (i, e, t) => u(i, typeof e != 'symbol' ? e + '' : e, t);
const a = class a {
  constructor(e, t = {}) {
    (this.root = e),
      (this.options = { ...a.defaults, ...t }),
      (this.controllers = []),
      (this.initialized = !1),
      (this.trigger = e.querySelector('.ps-language-selector__trigger')),
      (this.menu = e.querySelector('.ps-language-selector__menu')),
      (this.options_elements = Array.from(e.querySelectorAll('.ps-language-selector__option'))),
      (this.isOpen = !1),
      (this.currentIndex = this.options_elements.findIndex((s) =>
        s.classList.contains('ps-language-selector__option--active')
      )),
      this.currentIndex === -1 && (this.currentIndex = 0);
  }
  init() {
    if (this.initialized || !this.trigger || !this.menu) return;
    this.initialized = !0;
    const e = new AbortController();
    this.controllers.push(e),
      this.trigger.addEventListener('click', () => this.toggle(), { signal: e.signal }),
      this.options_elements.forEach((t, s) => {
        t.addEventListener(
          'click',
          (n) => {
            n.preventDefault(), this.selectOption(s), this.close();
          },
          { signal: e.signal }
        );
      }),
      this.trigger.addEventListener('keydown', (t) => this.handleTriggerKeydown(t), {
        signal: e.signal,
      }),
      this.menu.addEventListener('keydown', (t) => this.handleMenuKeydown(t), { signal: e.signal }),
      this.options.closeOnOutsideClick &&
        document.addEventListener(
          'click',
          (t) => {
            this.root.contains(t.target) || this.close();
          },
          { signal: e.signal }
        ),
      this.options.closeOnEscape &&
        this.root.addEventListener(
          'keydown',
          (t) => {
            t.key === 'Escape' && this.isOpen && (this.close(), this.trigger.focus());
          },
          { signal: e.signal }
        );
  }
  toggle() {
    this.isOpen ? this.close() : this.open();
  }
  open() {
    (this.isOpen = !0),
      (this.menu.hidden = !1),
      this.trigger.setAttribute('aria-expanded', 'true'),
      this.focusOption(this.currentIndex);
  }
  close() {
    (this.isOpen = !1),
      (this.menu.hidden = !0),
      this.trigger.setAttribute('aria-expanded', 'false');
  }
  selectOption(e) {
    this.options_elements.forEach((o, l) => {
      o.classList.toggle('ps-language-selector__option--active', l === e),
        o.setAttribute('aria-selected', l === e ? 'true' : 'false');
    });
    const t = this.options_elements[e],
      s = this.trigger.querySelector('.ps-language-selector__current');
    s && (s.textContent = t.textContent.trim()), (this.currentIndex = e);
    const n = t.dataset.lang;
    if (n) {
      const o = new CustomEvent('languagechange', {
        bubbles: !0,
        detail: { code: n, label: t.textContent.trim() },
      });
      this.root.dispatchEvent(o);
    }
  }
  handleTriggerKeydown(e) {
    (e.key === 'Enter' || e.key === ' ') && (e.preventDefault(), this.open()),
      e.key === 'ArrowDown' &&
        (e.preventDefault(), this.isOpen ? this.navigateNext() : this.open()),
      e.key === 'ArrowUp' &&
        (e.preventDefault(), this.isOpen ? this.navigatePrevious() : this.open());
  }
  handleMenuKeydown(e) {
    e.key === 'ArrowDown' && (e.preventDefault(), this.navigateNext()),
      e.key === 'ArrowUp' && (e.preventDefault(), this.navigatePrevious()),
      e.key === 'Enter' &&
        (e.preventDefault(),
        this.selectOption(this.currentIndex),
        this.close(),
        this.trigger.focus()),
      e.key === 'Home' && (e.preventDefault(), this.focusOption(0)),
      e.key === 'End' && (e.preventDefault(), this.focusOption(this.options_elements.length - 1));
  }
  navigateNext() {
    const e = (this.currentIndex + 1) % this.options_elements.length;
    this.focusOption(e);
  }
  navigatePrevious() {
    const e = this.currentIndex === 0 ? this.options_elements.length - 1 : this.currentIndex - 1;
    this.focusOption(e);
  }
  focusOption(e) {
    this.currentIndex = e;
    const t = this.options_elements[e];
    t &&
      (this.options_elements.forEach((s) => s.setAttribute('tabindex', '-1')),
      t.setAttribute('tabindex', '0'),
      t.focus());
  }
  destroy() {
    this.controllers.forEach((e) => e.abort()), (this.controllers = []), (this.initialized = !1);
  }
};
c(a, 'defaults', { closeOnOutsideClick: !0, closeOnEscape: !0 });
const r = a;
typeof Drupal < 'u' &&
  (Drupal.behaviors.psLanguageSelector = {
    attach(i) {
      i.querySelectorAll('[data-language-selector]').forEach((t) => {
        if (t.dataset.languageSelectorInitialized) return;
        t.dataset.languageSelectorInitialized = 'true';
        const s = new r(t);
        s.init(), (t.psLanguageSelector = s);
      });
    },
    detach(i, e, t) {
      t === 'unload' &&
        i.querySelectorAll('[data-language-selector]').forEach((n) => {
          n.psLanguageSelector &&
            (n.psLanguageSelector.destroy(),
            delete n.psLanguageSelector,
            delete n.dataset.languageSelectorInitialized);
        });
    },
  });
typeof Drupal > 'u' &&
  document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-language-selector]').forEach((e) => {
      const t = new r(e);
      t.init(), (e.psLanguageSelector = t);
    });
  });
