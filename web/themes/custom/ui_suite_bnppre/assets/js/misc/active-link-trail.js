/**
 * @file
 * Attaches behaviors for Drupal's active trail link marking.
 */

((Drupal, drupalSettings) => {
  const activeClass = 'active';

  /**
   * Append active class.
   *
   * The link is only active if it has data-drupal-active-trail=true.
   *
   * Does not discriminate based on element type, so allows you to set the
   * active class on any element: a, li…
   *
   * @type {Drupal~behavior}
   */
  Drupal.behaviors.ui_suite_bnppre_activeTrailLinks = {
    attach(context) {
      // Start by finding all potentially active links.
      const { path } = drupalSettings;
      const queryString = JSON.stringify(path.currentQuery);
      const querySelector = queryString
        ? `[data-drupal-link-query="${CSS.escape(queryString)}"]`
        : ':not([data-drupal-link-query])';
      const originalSelectors = [`[data-drupal-active-trail=true]`];
      let selectors;

      // Add language filtering.
      selectors = [].concat(
        // Links without any hreflang attributes (most of them).
        originalSelectors.map((selector) => `${selector}:not([hreflang])`),
        // Links with hreflang equals to the current language.
        originalSelectors.map(
          (selector) => `${selector}[hreflang="${path.currentLanguage}"]`,
        ),
      );

      // Add query string selector for pagers, exposed filters.
      selectors = selectors.map((current) => current + querySelector);

      // Query the DOM.
      const activeLinks = context.querySelectorAll(selectors.join(','));
      const il = activeLinks.length;
      for (let i = 0; i < il; i++) {
        activeLinks[i].classList.add(activeClass);
      }
    },
    detach(context, settings, trigger) {
      if (trigger === 'unload') {
        const activeLinks = context.querySelectorAll(
          `[data-drupal-active-trail=true]`,
        );
        const il = activeLinks.length;
        for (let i = 0; i < il; i++) {
          activeLinks[i].classList.remove(activeClass);
        }
      }
    },
  };
})(Drupal, drupalSettings);
