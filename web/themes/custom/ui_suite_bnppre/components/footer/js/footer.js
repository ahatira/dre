(function (Drupal, once) {
  const MOBILE_QUERY = '(max-width: 991.98px)';

  const hasClass = (element, className) => element.classList.contains(className);

  const makeCollapsible = (block, index) => {
    if (index === 0) {
      return null;
    }

    const title = block.querySelector(':scope > h2, :scope > .block-title');
    if (!title) {
      return null;
    }

    const bodyClass = 'ps-footer__accordion-body';
    let body = block.querySelector(`:scope > .${bodyClass}`);

    if (!body) {
      body = document.createElement('div');
      body.className = bodyClass;

      const children = Array.from(block.children);
      children.forEach((child) => {
        if (child !== title) {
          body.appendChild(child);
        }
      });
      block.appendChild(body);
    }

    const trigger = document.createElement('button');
    trigger.type = 'button';
    trigger.className = 'ps-footer__accordion-trigger';
    trigger.innerHTML = `<span class="ps-footer__accordion-label">${title.textContent.trim()}</span><span class="ps-footer__accordion-icon" aria-hidden="true"></span>`;

    const bodyId = `ps-footer-accordion-${Math.random().toString(36).slice(2, 10)}`;
    trigger.setAttribute('aria-controls', bodyId);
    body.id = bodyId;

    title.replaceWith(trigger);

    const setExpanded = (expanded) => {
      trigger.setAttribute('aria-expanded', expanded ? 'true' : 'false');
      body.hidden = !expanded;
      block.classList.toggle('is-open', expanded);
    };

    const defaultOpen = hasClass(block, 'is-open');
    setExpanded(defaultOpen);

    trigger.addEventListener('click', () => {
      const expanded = trigger.getAttribute('aria-expanded') === 'true';
      setExpanded(!expanded);
    });

    return { trigger, body, block, setExpanded };
  };

  Drupal.behaviors.psThemeFooter = {
    attach(context) {
      once('ps-theme-footer', '[data-ps-footer-top]', context).forEach((topRegion) => {
        const media = window.matchMedia(MOBILE_QUERY);
        const blocks = Array.from(topRegion.querySelectorAll(':scope > .block'));
        const accordions = blocks
          .map((block, index) => makeCollapsible(block, index))
          .filter(Boolean);

        if (!accordions.length) {
          return;
        }

        const syncMode = () => {
          const isMobile = media.matches;

          accordions.forEach(({ trigger, body, block, setExpanded }) => {
            trigger.disabled = !isMobile;
            trigger.classList.toggle('is-disabled', !isMobile);

            if (!isMobile) {
              setExpanded(true);
              body.hidden = false;
              block.classList.remove('is-open');
            }
            else if (!hasClass(block, 'is-open')) {
              setExpanded(false);
            }
          });
        };

        syncMode();

        if (typeof media.addEventListener === 'function') {
          media.addEventListener('change', syncMode);
        }
        else if (typeof media.addListener === 'function') {
          media.addListener(syncMode);
        }
      });
    },
  };
}(Drupal, once));
