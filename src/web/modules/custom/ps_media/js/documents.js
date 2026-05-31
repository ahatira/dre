(function (Drupal) {
  Drupal.behaviors.psMediaDocuments = {
    attach: function (context) {
      context.querySelectorAll('.ps-media-documents').forEach(function (list) {
        list.setAttribute('data-ps-media-documents', 'ready');
      });
    }
  };
})(Drupal);
