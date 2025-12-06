var p = (o, t) => () => (t || o((t = { exports: {} }).exports, t), t.exports);
var c = p((u, i) => {
  class d {
    constructor(t) {
      (this.element = t),
        (this.button = t.querySelector('[data-dropdown-button]')),
        (this.list = t.querySelector('[data-dropdown-list]')),
        (this.nativeSelect = t.querySelector('.ps-dropdown__native')),
        (this.options = this.list
          ? Array.from(
              this.list.querySelectorAll('.ps-dropdown__option:not(.ps-dropdown__option--disabled)')
            )
          : []),
        (this.currentIndex = this.options.findIndex(
          (e) => e.getAttribute('aria-selected') === 'true'
        )),
        this.currentIndex === -1 && (this.currentIndex = 0);
    }
    init() {
      !this.button ||
        !this.list ||
        (this.button.addEventListener('click', () => this.toggle()),
        this.options.forEach((t, e) => {
          t.addEventListener('click', () => {
            this.selectOption(e), this.close();
          });
        }),
        this.button.addEventListener('keydown', (t) => this.handleButtonKeydown(t)),
        this.list.addEventListener('keydown', (t) => this.handleListKeydown(t)),
        document.addEventListener('click', (t) => {
          this.element.contains(t.target) || this.close();
        }),
        this.element.addEventListener('keydown', (t) => {
          t.key === 'Escape' && (this.close(), this.button.focus());
        }));
    }
    toggle() {
      this.button.getAttribute('aria-expanded') === 'true' ? this.close() : this.open();
    }
    open() {
      (this.list.hidden = !1),
        this.button.setAttribute('aria-expanded', 'true'),
        this.focusOption(this.currentIndex);
    }
    close() {
      (this.list.hidden = !0), this.button.setAttribute('aria-expanded', 'false');
    }
    selectOption(t) {
      this.options.forEach((r, a) => {
        r.setAttribute('aria-selected', a === t ? 'true' : 'false');
      });
      const e = this.options[t],
        s = this.button.querySelector('.ps-dropdown__label');
      s && (s.textContent = e.textContent.trim());
      const n = e.dataset.value;
      if (this.nativeSelect && n) {
        this.nativeSelect.value = n;
        const r = new Event('change', { bubbles: !0 });
        this.nativeSelect.dispatchEvent(r);
      }
      this.currentIndex = t;
    }
    focusOption(t) {
      t >= 0 && t < this.options.length && this.options[t].focus();
    }
    handleButtonKeydown(t) {
      const e = this.button.getAttribute('aria-expanded') === 'true';
      switch (t.key) {
        case 'ArrowDown':
        case 'ArrowUp':
        case 'Enter':
        case ' ':
          t.preventDefault(), e || this.open();
          break;
      }
    }
    handleListKeydown(t) {
      switch (t.key) {
        case 'ArrowDown':
          t.preventDefault(),
            (this.currentIndex = Math.min(this.currentIndex + 1, this.options.length - 1)),
            this.focusOption(this.currentIndex);
          break;
        case 'ArrowUp':
          t.preventDefault(),
            (this.currentIndex = Math.max(this.currentIndex - 1, 0)),
            this.focusOption(this.currentIndex);
          break;
        case 'Enter':
        case ' ':
          t.preventDefault(),
            this.selectOption(this.currentIndex),
            this.close(),
            this.button.focus();
          break;
        case 'Home':
          t.preventDefault(), (this.currentIndex = 0), this.focusOption(this.currentIndex);
          break;
        case 'End':
          t.preventDefault(),
            (this.currentIndex = this.options.length - 1),
            this.focusOption(this.currentIndex);
          break;
        case 'Tab':
          this.close();
          break;
      }
    }
    destroy() {}
  }
  typeof Drupal < 'u' &&
    (Drupal.behaviors.psDropdown = {
      attach(o) {
        o.querySelectorAll('[data-dropdown]').forEach((e) => {
          if (typeof once < 'u')
            once('ps-dropdown', e).forEach((s) => {
              const n = new d(s);
              n.init(), (s.psDropdownWrapper = n);
            });
          else if (!e.dataset.dropdownInitialized) {
            const s = new d(e);
            s.init(), (e.dataset.dropdownInitialized = 'true'), (e.psDropdownWrapper = s);
          }
        });
      },
      detach(o, t, e) {
        e === 'unload' &&
          o.querySelectorAll('[data-dropdown]').forEach((n) => {
            n.psDropdownWrapper && (n.psDropdownWrapper.destroy(), delete n.psDropdownWrapper);
          });
      },
    });
  typeof i < 'u' && i.exports && (i.exports = { PsDropdownWrapper: d });
});
export default c();
