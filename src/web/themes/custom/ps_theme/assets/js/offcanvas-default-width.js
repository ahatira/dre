(function (Drupal, once) {
  const CORE_DEFAULT_OFFCANVAS_WIDTH = 300;

  const isCoreDefaultWidth = (width) => (
    width === CORE_DEFAULT_OFFCANVAS_WIDTH
    || width === String(CORE_DEFAULT_OFFCANVAS_WIDTH)
    || width === `${CORE_DEFAULT_OFFCANVAS_WIDTH}px`
  );

  const isOffCanvasDialog = (settings) => {
    const classes = settings?.classes?.['ui-dialog'] ?? '';
    return classes.includes('ui-dialog-off-canvas');
  };

  Drupal.behaviors.psThemeOffcanvasDefaultWidth = {
    attach(context) {
      once('ps-theme-offcanvas-default-width', 'body', context).forEach(() => {
        document.addEventListener('dialog:beforecreate', (event) => {
          if (!isOffCanvasDialog(event.settings)) {
            return;
          }
          if (isCoreDefaultWidth(event.settings.width)) {
            delete event.settings.width;
          }
        });
      });
    },
  };
})(Drupal, once);
