(function (Drupal, once) {
  Drupal.behaviors.bnpMediaActions = {
    attach: function (context, settings) {
      once('bnp-media-actions', '.media-library-item__remove, .media-library-item__edit', context).forEach(function (el) {
        if (el.classList.contains('media-library-item__remove')) {
          el.innerHTML = '<span aria-hidden="true">🗑️</span> <span class="visually-hidden">Remove</span>';
          el.addEventListener('click', function (e) {
            if (!confirm('Confirm media removal?')) {
              e.preventDefault();
              e.stopPropagation();
            }
          });
        }
        if (el.classList.contains('media-library-item__edit')) {
          el.innerHTML = '<span aria-hidden="true">✏️</span> <span class="visually-hidden">Edit</span>';
        }
      });

      var listSelector = '[data-drupal-selector="edit-field-media-gallery-current"], [data-drupal-selector="edit-field-media-document-current"]';

      once('bnp-media-order', listSelector, context).forEach(function (list) {
        var toTypeLabel = function (article) {
          if (!article) {
            return 'MEDIA';
          }

          var cls = article.className || '';
          var match = cls.match(/media--type-([a-z0-9_-]+)/);
          if (!match || !match[1]) {
            return 'MEDIA';
          }

          var raw = match[1];
          if (raw === 'remote-video') {
            return 'REMOTE';
          }
          if (raw === 'mediahub-video') {
            return 'MEDIAHUB';
          }
          if (raw === 'visite-guided') {
            return 'VIRTUAL TOUR';
          }
          return raw.replace(/-/g, ' ').toUpperCase();
        };

        var typeToSvg = function (typeLabel) {
          var map = {
            'VIDEO': 'bnp_video_file.svg',
            'AUDIO': 'bnp_audio_file.svg',
            'REMOTE': 'bnp_remote_video.svg',
            'MEDIAHUB': 'bnp_mediahub_video.svg',
            'VIRTUAL TOUR': 'bnp_virtual_tour.svg',
            'FILE': 'bnp_file_generic.svg',
          };
          return map[typeLabel] || null;
        };

        var injectSvgPlaceholder = function (item, typeLabel) {
          var article = item.querySelector('article');
          if (!article) return;
          if (article.querySelector('img[data-svg-placeholder]')) return;

          // Skip if a real image already loaded.
          var existingImg = article.querySelector('img');
          if (existingImg && existingImg.naturalWidth > 0) return;

          var svgFile = typeToSvg(typeLabel);
          if (!svgFile) return;

          var img = document.createElement('img');
          img.src = '/sites/default/files/bnp-media/placeholders/' + svgFile;
          img.alt = typeLabel + ' placeholder';
          img.setAttribute('data-svg-placeholder', '1');
          img.style.position = 'absolute';
          img.style.top = '0';
          img.style.left = '0';
          img.style.width = '100%';
          img.style.height = '100%';
          img.style.objectFit = 'contain';
          img.style.display = 'block';
          img.style.zIndex = '1';

          var fieldItem = article.querySelector('.field__item');
          if (fieldItem) {
            fieldItem.style.position = 'relative';
            fieldItem.appendChild(img);
          } else {
            article.style.position = 'relative';
            article.appendChild(img);
          }
        };

        var updateOrderBadges = function () {
          list.querySelectorAll('.item-container.rendered-entity').forEach(function (item, index) {
            var badge = item.querySelector('.bnp-media-order-badge');
            if (!badge) {
              badge = document.createElement('span');
              badge.className = 'bnp-media-order-badge';
              item.prepend(badge);
            }
            badge.textContent = String(index + 1);

            var typeBadge = item.querySelector('.bnp-media-type-badge');
            if (!typeBadge) {
              typeBadge = document.createElement('span');
              typeBadge.className = 'bnp-media-type-badge';
              item.prepend(typeBadge);
            }
            var typeLabel = toTypeLabel(item.querySelector('article'));
            typeBadge.textContent = typeLabel;

            // Inject SVG if article has no image
            injectSvgPlaceholder(item, typeLabel);
          });
        };

        updateOrderBadges();

        var observer = new MutationObserver(function () {
          updateOrderBadges();
        });
        observer.observe(list, { childList: true });
      });
    }
  };
})(Drupal, once);