(function (Drupal) {
  Drupal.behaviors.psMediaGallery = {
    attach: function (context) {
      context.querySelectorAll('.ps-media-gallery').forEach(function (gallery) {
        gallery.setAttribute('data-ps-media-gallery', 'ready');
      });
    }
  };
})(Drupal);
