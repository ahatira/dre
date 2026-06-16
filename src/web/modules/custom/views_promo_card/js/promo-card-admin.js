/**
 * @file
 * Live preview for promo card admin forms (iframe with ps_theme CSS).
 */

(function (Drupal, once, $) {
  'use strict';

  const DEBOUNCE_MS = 450;

  /**
   * Removes script tags from preview HTML (iframe sandbox is CSS-only).
   */
  const stripScriptsFromHtml = (html) => {
    const template = document.createElement('template');
    template.innerHTML = html.trim();
    template.content.querySelectorAll('script').forEach((script) => {
      script.remove();
    });
    return template.innerHTML;
  };

  /**
   * Returns true when a Drupal AJAX request is in progress on the form.
   */
  const isAjaxBusy = (form) => form.querySelector('.ajax-progress') !== null;

  /**
   * Ensures the preview target contains a sandboxed iframe.
   */
  const ensurePreviewIframe = (target) => {
    let iframe = target.querySelector('.promo-card-admin__preview-iframe');
    if (iframe) {
      return iframe;
    }

    iframe = document.createElement('iframe');
    iframe.className = 'promo-card-admin__preview-iframe';
    iframe.setAttribute('title', Drupal.t('Card preview'));
    iframe.setAttribute('sandbox', 'allow-same-origin');
    iframe.setAttribute('loading', 'lazy');
    target.innerHTML = '';
    target.appendChild(iframe);
    return iframe;
  };

  /**
   * Stops any active iframe resize observer.
   */
  const disconnectPreviewObserver = (iframe) => {
    if (iframe._previewResizeObserver) {
      iframe._previewResizeObserver.disconnect();
      iframe._previewResizeObserver = null;
    }
  };

  /**
   * Resizes the iframe to fit its document height.
   */
  const resizePreviewIframe = (iframe) => {
    const doc = iframe.contentDocument;
    if (!doc || !doc.body) {
      return;
    }

    iframe.style.height = '0px';

    const height = Math.max(
      doc.body.scrollHeight,
      doc.documentElement?.scrollHeight ?? 0,
    );
    iframe.style.height = `${Math.max(height, 160)}px`;
  };

  /**
   * Measures iframe height once after a preview document loads.
   */
  const finalizePreviewIframe = (iframe) => {
    disconnectPreviewObserver(iframe);
    resizePreviewIframe(iframe);
  };

  Drupal.behaviors.viewsPromoCardAdmin = {
    attach(context, settings) {
      once('views-promo-card-admin', '.promo-card-admin-form', context).forEach((form) => {
        const target = form.querySelector('#promo-card-preview-target');
        if (!target) {
          return;
        }

        const previewUrl = form.dataset.previewUrl || settings.viewsPromoCard?.previewUrl;
        if (!previewUrl) {
          return;
        }

        const isPlacementForm = form.classList.contains('promo-card-placement-form');
        let debounceTimer = null;
        let ajaxWaitTimer = null;
        let previewEnabled = true;

        const showEmptyPreview = () => {
          const emptyMessage = target.dataset.previewEmpty || '';
          target.innerHTML = emptyMessage
            ? `<p class="promo-card-admin__preview-empty">${emptyMessage}</p>`
            : '';
        };

        const executePreviewFetch = () => {
          if (!previewEnabled) {
            return;
          }

          if (isAjaxBusy(form)) {
            window.clearTimeout(ajaxWaitTimer);
            ajaxWaitTimer = window.setTimeout(executePreviewFetch, 100);
            return;
          }

          form.classList.remove('promo-card-admin-form--preview-error');

          const body = new FormData(form);
          fetch(previewUrl, {
            method: 'POST',
            body,
            credentials: 'same-origin',
            headers: {
              'X-Requested-With': 'XMLHttpRequest',
            },
          })
            .then((response) => {
              if (response.status === 204) {
                showEmptyPreview();
                return '';
              }
              if (!response.ok) {
                throw new Error('Preview request failed');
              }
              return response.text();
            })
            .then((html) => {
              if (typeof html !== 'string' || html.length === 0) {
                return;
              }
              const iframe = ensurePreviewIframe(target);
              disconnectPreviewObserver(iframe);
              iframe.style.height = '160px';
              iframe.srcdoc = stripScriptsFromHtml(html);
              iframe.onload = () => {
                finalizePreviewIframe(iframe);
                window.setTimeout(() => finalizePreviewIframe(iframe), 50);
              };
            })
            .catch(() => {
              form.classList.add('promo-card-admin-form--preview-error');
            });
        };

        const queuePreviewRefresh = (debounceMs = DEBOUNCE_MS) => {
          if (!previewEnabled) {
            return;
          }
          window.clearTimeout(debounceTimer);
          debounceTimer = window.setTimeout(executePreviewFetch, debounceMs);
        };

        form.addEventListener('input', () => {
          previewEnabled = true;
          queuePreviewRefresh();
        });

        form.addEventListener('change', (event) => {
          previewEnabled = true;
          if (event.target?.name === 'layout[editor][pattern_id]') {
            return;
          }
          const isAppearance = event.target?.name?.includes('[pattern_form][appearance][');
          queuePreviewRefresh(isAppearance ? 0 : DEBOUNCE_MS);
        });

        if (!isPlacementForm) {
          const editorRoot = form.querySelector('.promo-card-admin__editor') ?? form;
          const observer = new MutationObserver(() => queuePreviewRefresh(150));
          observer.observe(editorRoot, { childList: true, subtree: true });
        }

        $(document).on('ajaxComplete.viewsPromoCardAdmin', () => {
          if (document.body.contains(form)) {
            queuePreviewRefresh(100);
          }
        });

        executePreviewFetch();
      });
    },
  };
})(Drupal, once, jQuery);
