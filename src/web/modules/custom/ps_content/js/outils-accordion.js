/**
 * @file
 * Syncs tools accordion illustration with the active panel (bnppre.fr §3).
 */

((Drupal, drupalSettings, once) => {
  Drupal.behaviors.psContentOutilsAccordion = {
    attach(context) {
      once('ps-content-outils-accordion', '[data-tools-illustration-panel]', context).forEach((panel) => {
        const layout = panel.closest('.ps-homepage-tools__layout');
        if (!layout) {
          return;
        }

        const slides = drupalSettings.ps_content?.outilsAccordion?.slides ?? [];
        if (slides.length === 0) {
          return;
        }

        const slideById = Object.fromEntries(slides.map((slide) => [slide.item_id, slide]));
        const imageEl = panel.querySelector('img');
        const creditEl = panel.querySelector('.credit-tooltip-text');

        const applySlide = (itemId) => {
          const slide = slideById[itemId] ?? slides[0];
          if (!slide || !imageEl) {
            return;
          }
          imageEl.setAttribute('src', slide.url);
          imageEl.setAttribute('alt', slide.alt || '');
          const creditWrapper = panel.querySelector('.credit-tooltip');
          if (creditWrapper) {
            if (slide.credit) {
              creditWrapper.hidden = false;
              if (creditEl) {
                const copyright = creditEl.querySelector('.credit-tooltip-copyright');
                if (copyright && copyright.nextSibling) {
                  copyright.nextSibling.textContent = slide.credit ? ' ' + slide.credit : '';
                }
              }
            }
            else {
              creditWrapper.hidden = true;
            }
          }
        };

        const accordion = layout.querySelector('.accordion');
        if (!accordion) {
          return;
        }

        accordion.querySelectorAll('.accordion-collapse').forEach((collapse) => {
          collapse.addEventListener('shown.bs.collapse', (event) => {
            const target = event.target;
            if (target instanceof HTMLElement && target.id) {
              applySlide(target.id);
            }
          });
        });

        const initial = accordion.querySelector('.accordion-collapse.show');
        if (initial instanceof HTMLElement && initial.id) {
          applySlide(initial.id);
        }
      });
    },
  };
})(Drupal, drupalSettings, once);
