/**
 * Load SVG placeholders directly when Drupal image formatter fails to render them.
 */
(function(Drupal) {
  Drupal.behaviors.bnpMediaSvgLoader = {
    attach: function(context) {
      // Map media bundle types to SVG filenames
      const bundleToSvg = {
        'video': 'bnp_video_file.svg',
        'audio': 'bnp_audio_file.svg',
        'remote_video': 'bnp_remote_video.svg',
        'mediahub_video': 'bnp_mediahub_video.svg',
        'visite_guided': 'bnp_virtual_tour.svg',
        'file': 'bnp_file_text.svg',
        'audio': 'bnp_audio_file.svg'
      };

      // Find all empty thumbnail containers in gallery/document fields
      const emptyThumbnails = context.querySelectorAll(
        '[data-drupal-selector="edit-field-media-gallery-current"] .field__item:empty, ' +
        '[data-drupal-selector="edit-field-media-document-current"] .field__item:empty'
      );

      emptyThumbnails.forEach((container) => {
        // Find the type badge to determine media type
        const itemContainer = container.closest('.item-container');
        if (!itemContainer) return;

        const typeBadge = itemContainer.querySelector('.bnp-media-type-badge');
        if (!typeBadge) return;

        const typeText = typeBadge.textContent.trim().toLowerCase();

        // Find the SVG filename based on type
        let svgFile = null;
        if (typeText === 'video') svgFile = 'bnp_video_file.svg';
        else if (typeText === 'audio') svgFile = 'bnp_audio_file.svg';
        else if (typeText === 'remote') svgFile = 'bnp_remote_video.svg';
        else if (typeText === 'mediahub') svgFile = 'bnp_mediahub_video.svg';
        else if (typeText === 'virtual tour') svgFile = 'bnp_virtual_tour.svg';
        else if (typeText === 'visite guided') svgFile = 'bnp_virtual_tour.svg';
        else if (typeText === 'file') svgFile = 'bnp_file_text.svg';

        if (!svgFile) return;

        // Load and insert the SVG
        const svgPath = '/sites/default/files/bnp-media/placeholders/' + svgFile;
        
        fetch(svgPath)
          .then(response => {
            if (!response.ok) throw new Error('SVG not found');
            return response.text();
          })
          .then(svgText => {
            // Parse SVG and adjust dimensions
            const parser = new DOMParser();
            const svgDoc = parser.parseFromString(svgText, 'image/svg+xml');
            const svg = svgDoc.documentElement;

            // Add styling for proper container fit
            svg.style.width = '100%';
            svg.style.height = '100%';
            svg.setAttribute('preserveAspectRatio', 'xMidYMid slice');

            // Clear container and insert SVG
            container.innerHTML = '';
            container.appendChild(svg);
          })
          .catch(err => {
            console.warn('Failed to load SVG ' + svgFile + ':', err);
          });
      });
    }
  };
})(Drupal);
