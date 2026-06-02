(($, Drupal, once) => {
  const debounce = Drupal.debounce;
  const announce = Drupal.announce;
  const formatPlural = Drupal.formatPlural;

  let layoutBuilderBlocksFiltered = false;

  Drupal.behaviors.layoutBuilderBrowser = {
    attach: function attach() {
      // Override core behaviors.layoutBuilderBlockFilter.attach().
      Drupal.behaviors.layoutBuilderBlockFilter.attach = function attach(
        context,
      ) {
        // Custom selector, remove context to ensure filter works in modal.
        const $categories = $('.js-layout-builder-categories', context);
        const $filterLinks = $categories.find('.js-layout-builder-block-link');

        const filterBlockList = function filterBlockList(e) {
          const query = e.target.value.toLowerCase();

          const toggleBlockEntry = function toggleBlockEntry(index, link) {
            const $link = $(link);
            const textMatch = link.textContent.toLowerCase().includes(query);
            $link.toggle(textMatch);
          };

          if (query.length >= 2) {
            $categories
              .find('.js-layout-builder-category .accordion-button.collapsed')
              .attr('remember-closed', '');

            $categories
              .find('.js-layout-builder-category .accordion-button.collapsed')
              .click();

            $filterLinks.each(toggleBlockEntry);

            $categories
              .find(
                '.js-layout-builder-category:not(:has(.js-layout-builder-block-link:visible))',
              )
              .hide();

            announce(
              formatPlural(
                $categories.find('.js-layout-builder-block-link:visible')
                  .length,
                '1 block is available in the modified list.',
                '@count blocks are available in the modified list.',
              ),
            );
            layoutBuilderBlocksFiltered = true;
          } else if (layoutBuilderBlocksFiltered) {
            layoutBuilderBlocksFiltered = false;

            $categories
              .find(
                '.js-layout-builder-category .accordion-button[remember-closed]',
              )
              .removeAttr('remember-closed')
              .click();

            $categories.find('.js-layout-builder-category').show();
            $filterLinks.show();
            announce(Drupal.t('All available blocks are listed.'));
          }
        };

        $(once('input.js-layout-builder-filter', context)).on(
          'keyup',
          debounce(filterBlockList, 200),
        );
      };
    },
  };
})(jQuery, Drupal, once);
