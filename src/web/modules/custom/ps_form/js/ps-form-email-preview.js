/**
 * @file
 * Admin email preview — iframe resize and desktop/mobile viewport toggle.
 */

(function (Drupal, once) {
  'use strict';

  /**
   * Resizes an iframe to fit its document height.
   */
  const resizeIframe = (iframe) => {
    const doc = iframe.contentDocument;
    if (!doc || !doc.body) {
      return;
    }

    iframe.style.height = '0px';
    const height = Math.max(
      doc.body.scrollHeight,
      doc.documentElement?.scrollHeight ?? 0,
    );
    iframe.style.height = `${Math.max(height, 320)}px`;
  };

  /**
   * Wires load/resize handlers for a preview iframe.
   */
  const bindPreviewIframe = (iframe) => {
    const finalize = () => {
      resizeIframe(iframe);
      window.setTimeout(() => resizeIframe(iframe), 100);
      window.setTimeout(() => resizeIframe(iframe), 500);
    };

    iframe.addEventListener('load', () => {
      finalize();
      const doc = iframe.contentDocument;
      if (!doc) {
        return;
      }
      doc.querySelectorAll('img').forEach((img) => {
        if (!img.complete) {
          img.addEventListener('load', finalize, { once: true });
          img.addEventListener('error', finalize, { once: true });
        }
      });
    });
    if (iframe.contentDocument?.readyState === 'complete') {
      finalize();
    }

    if (typeof ResizeObserver !== 'undefined' && iframe.contentDocument?.body) {
      const observer = new ResizeObserver(() => resizeIframe(iframe));
      observer.observe(iframe.contentDocument.body);
      iframe._psFormEmailPreviewObserver = observer;
    }
  };

  /**
   * Toggles desktop/mobile viewport width on the preview chrome.
   */
  const bindViewportToggle = (client) => {
    const buttons = client.querySelectorAll('.ps-form-email-client__viewport-btn');
    if (!buttons.length) {
      return;
    }

    buttons.forEach((button) => {
      button.addEventListener('click', () => {
        const mode = button.getAttribute('data-viewport') || 'desktop';
        client.classList.toggle('ps-form-email-client--mobile', mode === 'mobile');

        buttons.forEach((peer) => {
          peer.classList.toggle('is-active', peer === button);
        });

        const iframe = client.querySelector('.ps-form-email-preview__iframe');
        if (iframe) {
          window.setTimeout(() => resizeIframe(iframe), 220);
        }
      });
    });
  };

  Drupal.behaviors.psFormEmailPreview = {
    attach(context) {
      once('ps-form-email-preview-client', '.ps-form-email-client', context).forEach((client) => {
        bindViewportToggle(client);
      });

      once('ps-form-email-preview', '.ps-form-email-preview__iframe', context).forEach((iframe) => {
        bindPreviewIframe(iframe);
      });
    },
    detach(context, settings, trigger) {
      if (trigger !== 'unload') {
        return;
      }
      context.querySelectorAll('.ps-form-email-preview__iframe').forEach((iframe) => {
        if (iframe._psFormEmailPreviewObserver) {
          iframe._psFormEmailPreviewObserver.disconnect();
          delete iframe._psFormEmailPreviewObserver;
        }
      });
    },
  };
})(Drupal, once);
