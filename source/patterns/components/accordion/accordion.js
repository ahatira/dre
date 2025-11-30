/**
 * Accordion Component Behavior
 * - Click/keyboard toggling of sections
 * - Optional single-open behavior via data-single-open
 * - A11y: manages aria-expanded and hidden attributes
 * - Dispatches custom events: accordion:show|shown|hide|hidden
 */

(() => {
  function toggleItem(root, trigger) {
    const panelId = trigger.getAttribute('aria-controls');
    const panel = root.querySelector(`#${CSS.escape(panelId)}`);
    if (!panel) {
      return;
    }

    const single = root.getAttribute('data-single-open') === 'true';
    const expanded = trigger.getAttribute('aria-expanded') === 'true';

    // If single mode and opening this one, close others first
    if (single && !expanded) {
      root.querySelectorAll('[data-accordion-trigger]').forEach((btn) => {
        if (btn !== trigger) {
          btn.setAttribute('aria-expanded', 'false');
        }
      });
      root.querySelectorAll('[data-accordion-panel]').forEach((p) => {
        if (p !== panel) {
          p.hidden = true;
        }
      });
      root.querySelectorAll('.ps-accordion__item').forEach((item) => {
        if (item !== trigger.closest('.ps-accordion__item')) {
          item.classList.remove('ps-accordion__item--open');
        }
      });
    }

    const evtName = expanded ? 'accordion:hide' : 'accordion:show';
    root.dispatchEvent(new CustomEvent(evtName, { bubbles: true, detail: { trigger, panel } }));

    trigger.setAttribute('aria-expanded', String(!expanded));
    panel.hidden = expanded;
    trigger.closest('.ps-accordion__item')?.classList.toggle('ps-accordion__item--open', !expanded);

    const evtDone = expanded ? 'accordion:hidden' : 'accordion:shown';
    root.dispatchEvent(new CustomEvent(evtDone, { bubbles: true, detail: { trigger, panel } }));
  }

  function onClick(e) {
    const btn = e.target.closest?.('[data-accordion-trigger]');
    if (!btn) {
      return;
    }
    const root = btn.closest('[data-accordion]');
    if (!root) {
      return;
    }
    toggleItem(root, btn);
  }

  function onKeydown(e) {
    const btn = e.target.closest?.('[data-accordion-trigger]');
    if (!btn) {
      return;
    }
    if (e.key === 'Enter' || e.key === ' ') {
      e.preventDefault();
      const root = btn.closest('[data-accordion]');
      if (!root) {
        return;
      }
      toggleItem(root, btn);
    }
  }

  function init() {
    // Delegated listeners
    document.addEventListener('click', onClick);
    document.addEventListener('keydown', onKeydown);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
