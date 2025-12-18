/**
 * Video Component - Video.js Initialization
 *
 * Initializes Video.js players with YouTube/Vimeo plugin support.
 * Handles Drupal-specific integration and analytics tracking.
 *
 * @see https://videojs.com/
 * @see https://github.com/videojs/videojs-youtube
 */

((Drupal, _drupalSettings, once) => {
  /**
   * Initialize Video.js players
   */
  Drupal.behaviors.psVideo = {
    attach: (context, _settings) => {
      const videoElements = once('ps-video', '[data-ps-video]', context);

      videoElements.forEach((videoElement) => {
        // The element selected by once() IS the <video> tag itself
        if (videoElement.tagName !== 'VIDEO') {
          return;
        }

        const videoId = videoElement.id;

        // Get poster URL from data attribute or poster attribute
        const posterUrl =
          videoElement.getAttribute('data-poster') || videoElement.getAttribute('poster');

        // Determine tech order based on source type
        const sourceElement = videoElement.querySelector('source');
        const sourceType = sourceElement ? sourceElement.getAttribute('type') : '';
        const techOrder = sourceType.includes('youtube') ? ['youtube'] : ['html5'];

        const options = {
          // Responsive fluid layout
          fluid: true,
          responsive: true,

          // Big play button
          bigPlayButton: true,

          // Control bar
          controls: true,
          controlBar: {
            volumePanel: {
              inline: false,
            },
          },

          // YouTube plugin options
          youtube: {
            iv_load_policy: 3,
            modestbranding: 1,
            rel: 0,
            showinfo: 0,
          },

          // Performance
          preload: 'metadata',

          // Tech order (dynamic based on source type)
          techOrder: techOrder,

          // Poster (must be in options for Video.js to generate vjs-poster div)
          poster: posterUrl,

          // Allow techs to override poster (important for YouTube)
          techCanOverridePoster: true,
        };

        // Initialize Video.js player
        const player = videojs(videoId, options, function onPlayerReady() {
          // Track video play
          this.on('play', () => {
            trackVideoPlay(
              videoId,
              videoElement.currentSrc || videoElement.querySelector('source')?.src
            );
          });

          // Track video ended
          this.on('ended', function () {
            // Custom event for analytics
            videoElement.dispatchEvent(
              new CustomEvent('ps:video:ended', {
                bubbles: true,
                detail: {
                  id: videoId,
                  duration: this.duration(),
                },
              })
            );
          });
        });

        // Store player reference
        videoElement.psVideoPlayer = player;
      });
    },

    /**
     * Cleanup on detach
     */
    detach: (context, _settings, trigger) => {
      if (trigger === 'unload') {
        const videoElements = context.querySelectorAll('[data-ps-video]');
        videoElements.forEach((element) => {
          if (element.psVideoPlayer) {
            element.psVideoPlayer.dispose();
            delete element.psVideoPlayer;
          }
        });
      }
    },
  };

  /**
   * Track video play event (Google Analytics)
   */
  function trackVideoPlay(videoId, videoUrl) {
    // Google Analytics (gtag)
    if (typeof gtag !== 'undefined') {
      gtag('event', 'video_play', {
        event_category: 'Video',
        event_label: videoUrl,
        value: videoId,
      });
    }

    // Custom event
    document.dispatchEvent(
      new CustomEvent('ps:video:play', {
        detail: {
          id: videoId,
          url: videoUrl,
        },
      })
    );
  }
})(Drupal, drupalSettings, once);
