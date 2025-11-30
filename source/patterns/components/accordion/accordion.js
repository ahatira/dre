/**
 * @file Accordion Component Behavior (Drupal-friendly)
 * - Click/keyboard toggling of sections
 * - Optional single-open behavior via data-single-open
 * - A11y: manages aria-expanded and hidden attributes
 * - Dispatches custom events: accordion:show|shown|hide|hidden
 */

((Drupal, once) => {
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

  Drupal.behaviors.psAccordion = {
    attach(context) {
      once('ps-accordion', '[data-accordion]', context).forEach((root) => {
        // Bind to triggers found within this root
        root.querySelectorAll('[data-accordion-trigger]').forEach((trigger) => {
          // Click
          trigger.addEventListener('click', () => {
            toggleItem(root, trigger);
          });
          // Keyboard (Enter/Space)
          trigger.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' || e.key === ' ') {
              e.preventDefault();
              toggleItem(root, trigger);
            }
          });
        });
      });
    },
  };
})(Drupal, once);
