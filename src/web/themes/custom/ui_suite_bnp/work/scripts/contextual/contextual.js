(($, Drupal) => {
  /**
   * Theme function for a contextual trigger.
   *
   * @return {string}
   *   A string representing a DOM fragment.
   */
  Drupal.theme.contextualTrigger = () => {
    return (
      '<button class="trigger focusable visually-hidden dropdown-toggle"' +
      'type="button"' +
      'data-bs-toggle="dropdown"' +
      'aria-expanded="false"' +
      '></button>'
    );
  };
})(jQuery, Drupal);
