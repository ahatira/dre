(($, Drupal, once) => {
  const debounce = Drupal.debounce;
  const announce = Drupal.announce;
  const formatPlural = Drupal.formatPlural;

  Drupal.behaviors.layoutBuilderBrowser = {
    attach(context) {
      $(once('lb-browser-filter-input', 'input.js-layout-builder-filter', context)).each((index, input) => {
        const $input = $(input);
        const $dialogScope = $input.closest('.ui-dialog-content, .ui-dialog, .ui-widget-content');
        const $scope = $dialogScope.length ? $dialogScope : $(context);
        const $categories = $scope.find('.js-layout-builder-categories');
        const $filterLinks = $categories.find('.js-layout-builder-block-link');

        if (!$categories.length || !$filterLinks.length) {
          return;
        }

        let isFiltered = false;

        const filterBlockList = (event) => {
          const query = event.target.value.trim().toLowerCase();

          if (query.length >= 2) {
            $categories
              .find('.js-layout-builder-category .accordion-button.collapsed')
              .attr('data-lb-remember-closed', '1')
              .trigger('click');

            $filterLinks.each((_, link) => {
              const $link = $(link);
              const textMatch = link.textContent.toLowerCase().includes(query);
              $link.parent().toggle(textMatch);
            });

            $categories
              .find('.js-layout-builder-category')
              .each((_, category) => {
                const $category = $(category);
                const hasVisible = $category.find('.js-layout-builder-block-link:visible').length > 0;
                $category.toggle(hasVisible);
              });

            announce(
              formatPlural(
                $categories.find('.js-layout-builder-block-link:visible').length,
                '1 block is available in the modified list.',
                '@count blocks are available in the modified list.',
              ),
            );
            isFiltered = true;
            return;
          }

          if (!isFiltered) {
            return;
          }

          isFiltered = false;

          $categories
            .find('.js-layout-builder-category .accordion-button[data-lb-remember-closed="1"]')
            .each((_, button) => {
              const $button = $(button);
              if (!$button.hasClass('collapsed')) {
                $button.trigger('click');
              }
              $button.removeAttr('data-lb-remember-closed');
            });

          $categories.find('.js-layout-builder-category').show();
          $filterLinks.parent().show();
          announce(Drupal.t('All available blocks are listed.'));
        };

        $input.on('input', debounce(filterBlockList, 200));
      });
    },
  };
})(jQuery, Drupal, once);
