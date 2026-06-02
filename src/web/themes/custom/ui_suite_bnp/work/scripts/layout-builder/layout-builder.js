/**
 * @file
 * Attaches the behaviors for the Layout Builder module.
 */

(($, Drupal) => {
  const { behaviors, debounce, announce, formatPlural } = Drupal;

  /*
   * Boolean that tracks if block listing is currently being filtered. Declared
   * outside of behaviors so value is retained on rebuild.
   */
  let layoutBuilderBlocksFiltered = false;

  /**
   * Provides the ability to filter the block listing in "Add block" dialog.
   *
   * @type {Drupal~behavior}
   *
   * @prop {Drupal~behaviorAttach} attach
   *   Attach block filtering behavior to "Add block" dialog.
   */
  behaviors.layoutBuilderBlockFilter = {
    attach(context) {
      const $categories = $('.js-layout-builder-categories', context);
      const $filterLinks = $categories.find('.js-layout-builder-block-link');

      /**
       * Filters the block list.
       *
       * @param {jQuery.Event} e
       *   The jQuery event for the keyup event that triggered the filter.
       */
      const filterBlockList = (e) => {
        const query = e.target.value.toLowerCase();

        /**
         * Shows or hides the block entry based on the query.
         *
         * @param {number} index
         *   The index in the loop, as provided by `jQuery.each`
         * @param {HTMLElement} link
         *   The link to add the block.
         */
        const toggleBlockEntry = (index, link) => {
          const $link = $(link);
          const textMatch = link.textContent.toLowerCase().includes(query);
          // Checks if a category is currently hidden.
          // Toggles the category on if so.
          if (
            Drupal.elementIsHidden(
              $link.closest('.js-layout-builder-category')[0],
            )
          ) {
            $link.closest('.js-layout-builder-category').show();
          }
          // Toggle the li tag of the matching link.
          $link.parent().toggle(textMatch);
        };

        // Filter if the length of the query is at least 2 characters.
        if (query.length >= 2) {
          // Attribute to note which categories are closed before opening all.
          $categories
            .find('.js-layout-builder-category .accordion-button.collapsed')
            .attr('remember-closed', '');

          // Open all categories so every block is available to filtering.
          $categories
            .find('.js-layout-builder-category .accordion-button.collapsed')
            .click();
          // Toggle visibility of links based on query.
          $filterLinks.each(toggleBlockEntry);

          // Only display categories containing visible links.
          $categories
            .find(
              '.js-layout-builder-category:not(:has(.js-layout-builder-block-link:visible))',
            )
            .hide();

          announce(
            formatPlural(
              $categories.find('.js-layout-builder-block-link:visible').length,
              '1 block is available in the modified list.',
              '@count blocks are available in the modified list.',
            ),
          );
          layoutBuilderBlocksFiltered = true;
        } else if (layoutBuilderBlocksFiltered) {
          layoutBuilderBlocksFiltered = false;
          // Re-open categories that were closed pre-filtering.
          $categories
            .find(
              '.js-layout-builder-category .accordion-button[remember-closed]',
            )
            .removeAttr('remember-closed')
            .click();
          // Show all categories since filter is turned off.
          $categories.find('.js-layout-builder-category').show();
          // Show all li tags since filter is turned off.
          $filterLinks.parent().show();
          announce(Drupal.t('All available blocks are listed.'));
        }
      };

      $(
        once('block-filter-text', 'input.js-layout-builder-filter', context),
      ).on('input', debounce(filterBlockList, 200));
    },
  };
})(jQuery, Drupal, Sortable);
