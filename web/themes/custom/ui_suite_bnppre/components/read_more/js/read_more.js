((Drupal, once) => {
  Drupal.ui_suite_bnppre_read_more = Drupal.ui_suite_bnppre_read_more || {};

  /**
   * Attach behavior for content truncation and expansion.
   *
   * @type {Drupal~behavior}
   */
  Drupal.behaviors.ui_suite_bnppre_read_more = {
    attach(context) {
      once('ui-suite-bnppre-read-more', '[data-ps-read-more]', context).forEach(
        (container) => {
          const content = container.querySelector('[data-read-more-content]');
          const toggle = container.querySelector('[data-read-more-toggle]');
          const label = container.querySelector('[data-read-more-label]');

          if (!content || !toggle || !label) {
            return;
          }

          const maxHeight = Number.parseInt(
            container.dataset.readMoreMaxHeight || '150',
            10,
          );
          const expandLabel =
            container.dataset.readMoreExpandLabel || Drupal.t('Read more');
          const collapseLabel =
            container.dataset.readMoreCollapseLabel || Drupal.t('Read less');

          let fullHeight = 0;
          let resizeDebounce;

          const measureFullHeight = () => {
            const previous = content.style.maxHeight;
            content.style.maxHeight = 'none';
            const measured = content.scrollHeight;
            content.style.maxHeight = previous;
            return measured;
          };

          const setExpandedState = (expanded) => {
            container.classList.toggle('is-expanded', expanded);
            toggle.setAttribute('aria-expanded', expanded ? 'true' : 'false');
            label.textContent = expanded ? collapseLabel : expandLabel;
          };

          const applyExpandedLayout = (animate) => {
            if (animate) {
              content.style.maxHeight = `${fullHeight}px`;
              const transitionDone = () => {
                if (container.classList.contains('is-expanded')) {
                  content.style.maxHeight = 'none';
                }
                content.removeEventListener('transitionend', transitionDone);
              };
              content.addEventListener('transitionend', transitionDone);
              return;
            }

            content.style.maxHeight = 'none';
          };

          const applyCollapsedLayout = (animate) => {
            if (animate && content.style.maxHeight === 'none') {
              content.style.maxHeight = `${fullHeight}px`;
              void content.offsetHeight;
            }

            content.style.maxHeight = `${maxHeight}px`;
          };

          const refresh = () => {
            fullHeight = measureFullHeight();
            const isTruncatable = fullHeight > maxHeight;

            if (!isTruncatable) {
              container.classList.remove('is-truncatable');
              setExpandedState(true);
              content.style.maxHeight = 'none';
              toggle.classList.add('d-none');
              return;
            }

            container.classList.add('is-truncatable');
            toggle.classList.remove('d-none');

            if (container.classList.contains('is-expanded')) {
              applyExpandedLayout(false);
            } else {
              applyCollapsedLayout(false);
              setExpandedState(false);
            }
          };

          refresh();

          toggle.addEventListener('click', () => {
            const isExpanded = container.classList.contains('is-expanded');

            if (isExpanded) {
              setExpandedState(false);
              applyCollapsedLayout(true);
              return;
            }

            setExpandedState(true);
            applyExpandedLayout(true);
          });

          window.addEventListener('resize', () => {
            clearTimeout(resizeDebounce);
            resizeDebounce = window.setTimeout(refresh, 200);
          });
        },
      );
    },
  };
})(Drupal, once);
