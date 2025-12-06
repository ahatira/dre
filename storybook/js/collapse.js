((l, u) => {
  l.behaviors.collapse = {
    attach(v) {
      u('collapse', '.ps-collapse', v).forEach((e) => {
        const n = e.querySelector('.ps-collapse__trigger'),
          t = e.querySelector('.ps-collapse__panel');
        if (!n || !t) return;
        const r = window.matchMedia('(prefers-reduced-motion: reduce)').matches,
          a = () => {
            t.removeAttribute('hidden'),
              (t.style.height = '0px'),
              e.classList.add('is-collapsing'),
              e.classList.remove('is-expanded'),
              t.offsetHeight;
            const s = t.scrollHeight;
            t.style.height = `${s}px`;
            const i = (d) => {
              d.propertyName === 'height' &&
                (t.removeEventListener('transitionend', i),
                e.classList.remove('is-collapsing'),
                e.classList.add('is-expanded'),
                (t.style.height = ''),
                e.dispatchEvent(
                  new CustomEvent('collapse:shown', { bubbles: !0, detail: { collapse: e } })
                ));
            };
            r
              ? (e.classList.remove('is-collapsing'),
                e.classList.add('is-expanded'),
                (t.style.height = ''),
                e.dispatchEvent(
                  new CustomEvent('collapse:shown', { bubbles: !0, detail: { collapse: e } })
                ))
              : t.addEventListener('transitionend', i),
              e.dispatchEvent(
                new CustomEvent('collapse:show', { bubbles: !0, detail: { collapse: e } })
              );
          },
          o = () => {
            (t.style.height = `${t.scrollHeight}px`),
              t.offsetHeight,
              e.classList.add('is-collapsing'),
              e.classList.remove('is-expanded'),
              (t.style.height = '0px');
            const s = (i) => {
              i.propertyName === 'height' &&
                (t.removeEventListener('transitionend', s),
                e.classList.remove('is-collapsing'),
                t.setAttribute('hidden', ''),
                (t.style.height = ''),
                e.dispatchEvent(
                  new CustomEvent('collapse:hidden', { bubbles: !0, detail: { collapse: e } })
                ));
            };
            r
              ? (e.classList.remove('is-collapsing'),
                t.setAttribute('hidden', ''),
                (t.style.height = ''),
                e.dispatchEvent(
                  new CustomEvent('collapse:hidden', { bubbles: !0, detail: { collapse: e } })
                ))
              : t.addEventListener('transitionend', s),
              e.dispatchEvent(
                new CustomEvent('collapse:hide', { bubbles: !0, detail: { collapse: e } })
              );
          },
          h = () => {
            const s = e.classList.contains('is-expanded');
            n.setAttribute('aria-expanded', !s), s ? o() : a();
          };
        n.addEventListener('click', h),
          n.addEventListener('keydown', (s) => {
            (s.key === 'Enter' || s.key === ' ') && (s.preventDefault(), h());
          }),
          e.addEventListener('collapse:external-toggle', (s) => {
            var c;
            const i = (c = s.detail) == null ? void 0 : c.expanded,
              d = e.classList.contains('is-expanded');
            i === void 0 || i === d || (n.setAttribute('aria-expanded', i), i ? a() : o());
          }),
          n.getAttribute('aria-expanded') === 'true'
            ? (e.classList.add('is-expanded'), t.removeAttribute('hidden'))
            : t.setAttribute('hidden', '');
      });
    },
  };
})(Drupal, once);
