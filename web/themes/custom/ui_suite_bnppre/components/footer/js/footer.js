(function (Drupal, once) {
  const MOBILE_QUERY = '(max-width: 991.98px)';

  const hasClass = (element, className) => element.classList.contains(className);

  const makeCollapsible = (column, index) => {
    if (index === 0) {
      return null;
    }

    const firstBlock = column.querySelector(':scope > .block');
    if (!firstBlock) {
      return null;
    }

    const title = firstBlock.querySelector(':scope > h2, :scope > .block-title');
    if (!title) {
      return null;
    }

    const bodyClass = 'ps-footer__accordion-body';
    let body = column.querySelector(`:scope > .${bodyClass}`);

    if (!body) {
      body = document.createElement('div');
      body.className = bodyClass;

      const firstBlockChildren = Array.from(firstBlock.children);
      firstBlockChildren.forEach((child) => {
        if (child !== title) {
          body.appendChild(child);
        }
      });

      const otherBlocks = Array.from(column.children)
        .filter((child) => child !== firstBlock);
      otherBlocks.forEach((child) => {
        body.appendChild(child);
      });

      column.appendChild(body);
    }

    const trigger = document.createElement('button');
    trigger.type = 'button';
    trigger.className = 'ps-footer__accordion-trigger';
    trigger.innerHTML = `<span class="ps-footer__accordion-label">${title.textContent.trim()}</span><span class="ps-footer__accordion-icon" aria-hidden="true"></span>`;

    const bodyId = `ps-footer-accordion-${Math.random().toString(36).slice(2, 10)}`;
    trigger.setAttribute('aria-controls', bodyId);
    body.id = bodyId;

    firstBlock.insertBefore(trigger, title);
    title.classList.add('ps-footer__accordion-title');
    column.classList.add('is-accordion-ready');

    const setExpanded = (expanded) => {
      trigger.setAttribute('aria-expanded', expanded ? 'true' : 'false');
      body.hidden = !expanded;
      column.classList.toggle('is-open', expanded);
    };

    const defaultOpen = hasClass(column, 'is-open');
    setExpanded(defaultOpen);

    trigger.addEventListener('click', () => {
      const expanded = trigger.getAttribute('aria-expanded') === 'true';
      setExpanded(!expanded);
    });

    return { trigger, body, column, setExpanded };
  };

  Drupal.behaviors.psThemeFooter = {
    attach(context) {
      once('ps-theme-footer', '[data-ps-footer-top]', context).forEach((topRegion) => {
        const media = window.matchMedia(MOBILE_QUERY);
        const columns = Array.from(topRegion.querySelectorAll(':scope > .ps-footer__column'));
        const targets = columns.length ? columns : Array.from(topRegion.querySelectorAll(':scope > .block'));

        const accordions = targets
          .map((column, index) => makeCollapsible(column, index))
          .filter(Boolean);

        if (!accordions.length) {
          return;
        }

        const syncMode = () => {
          const isMobile = media.matches;

          accordions.forEach(({ trigger, body, column, setExpanded }) => {
            trigger.disabled = !isMobile;
            trigger.classList.toggle('is-disabled', !isMobile);

            if (!isMobile) {
              setExpanded(true);
              body.hidden = false;
              column.classList.remove('is-open');
            }
            else if (!hasClass(column, 'is-open')) {
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
