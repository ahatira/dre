<?php

declare(strict_types=1);

namespace Drupal\ui_suite_bnppre\Element;

use Drupal\Core\Security\TrustedCallbackInterface;
use Drupal\Core\Url;
use Drupal\ui_suite_bnppre\Utility\Bootstrap;
use Drupal\ui_suite_bnppre\Utility\Element;

/**
 * Element Prerender methods for layout builder.
 */
class ElementPreRenderLayoutBuilder implements TrustedCallbackInterface
{
  /**
   * Weight to ensure the section label is placed first.
   */
    public const int SECTION_LABEL_WEIGHT = -50;

  /**
   * Weight to ensure the remove link is placed second.
   */
    public const int REMOVE_WEIGHT = -45;

  /**
   * Weight to ensure the configure link is placed third.
   */
    public const int CONFIGURE_WEIGHT = -40;

  /**
   * Weight to ensure the Section Library add to library link is placed fourth.
   */
    public const int SECTION_LIBRARY_ADD_WEIGHT = -35;

  /**
   * Weight to ensure the Layout Builder Reorder up link is placed fifth.
   */
    public const int LAYOUT_BUILDER_REORDER_UP_WEIGHT = -30;

  /**
   * Weight to ensure the Layout Builder Reorder down link is placed sixth.
   */
    public const int LAYOUT_BUILDER_REORDER_DOWN_WEIGHT = -25;

  /**
   * Handle styling for layout builder element.
   */
    public static function preRenderLayoutBuilder(array $element): array
    {
      // @phpstan-ignore-next-line
        $element_object = Element::create($element);

        if (!isset($element_object->layout_builder) || !($element_object->layout_builder instanceof Element)) {
          // @phpstan-ignore-next-line
            return $element;
        }

      // Main wrapper.
        $element_object->layout_builder->addClass([
        'border',
        'border-3',
        'border-primary',
        'p-4',
        'pb-2',
        ]);

      /** @var \Drupal\ui_suite_bnppre\Utility\Element $layoutBuilderArea */
        foreach ($element_object->layout_builder->children() as $layoutBuilderArea) {
          // Add section.
            if (
                isset($layoutBuilderArea->link)
                && $layoutBuilderArea->link instanceof Element
                && $layoutBuilderArea->link->isType('link')
            ) {
                $url = $layoutBuilderArea->link->getProperty('url');
                if ($url instanceof Url && $url->getRouteName() == 'layout_builder.choose_section') {
                // Wrapper.
                    $layoutBuilderArea->addClass([
                    'mb-3',
                    'py-4',
                    'text-center',
                    'bg-light',
                    ]);

                // Link.
                // @phpstan-ignore-next-line
                    $layoutBuilderArea->link->addClass([
                    'btn',
                    'btn-secondary',
                    ]);
                // @phpstan-ignore-next-line
                    $layoutBuilderArea->link->setIcon(Bootstrap::icon('plus-lg'));
                }
            }

          // Section label.
          // Display section label first. So we can display action links with icon
          // only.
            if (isset($layoutBuilderArea->section_label) && $layoutBuilderArea->section_label instanceof Element) {
                $layoutBuilderArea->section_label->setProperty('access', true);
                $layoutBuilderArea->section_label->setProperty('weight', static::SECTION_LABEL_WEIGHT);
            }

          // Remove link.
            if (
                isset($layoutBuilderArea->remove)
                && $layoutBuilderArea->remove instanceof Element
                && $layoutBuilderArea->remove->isType('link')
            ) {
                $url = $layoutBuilderArea->remove->getProperty('url');
                if ($url instanceof Url) {
                    $layoutBuilderArea->remove->addClass('mx-1');
                    $layoutBuilderArea->remove->setIcon(Bootstrap::icon('trash'));
                    $layoutBuilderArea->remove->setProperty('icon_position', 'icon_only');
                    $layoutBuilderArea->remove->setProperty('weight', static::REMOVE_WEIGHT);
                }
            }

          // Configure link.
            if (
                isset($layoutBuilderArea->configure)
                && $layoutBuilderArea->configure instanceof Element
                && $layoutBuilderArea->configure->isType('link')
            ) {
                $url = $layoutBuilderArea->configure->getProperty('url');
                if ($url instanceof Url) {
                    $layoutBuilderArea->configure->addClass('mx-1');
                    $layoutBuilderArea->configure->setIcon(Bootstrap::icon('pencil-fill'));
                    $layoutBuilderArea->configure->setProperty('icon_position', 'icon_only');
                    $layoutBuilderArea->configure->setProperty('weight', static::CONFIGURE_WEIGHT);
                }
            }

          // Section.
            if (isset($layoutBuilderArea->{'layout-builder__section'})) {
                $section = $layoutBuilderArea->{'layout-builder__section'};
              // @phpstan-ignore-next-line
                foreach ($section->children() as $region) {
                  /** @var \Drupal\ui_suite_bnppre\Utility\Element $region */
                    $region->addClass('usb-layout-builder-region');

                  // Region label.
                    if (isset($region->region_label)) {
                        /** @var \Drupal\ui_suite_bnppre\Utility\Element $regionLabel */
                        $regionLabel = $region->region_label;
                        $regionLabel->addClass([
                          'text-bg-light',
                          'usb-layout-builder-region-label',
                        ]);
                    }

                  // Add block.
                    if (isset($region->layout_builder_add_block)) {
                      /** @var \Drupal\ui_suite_bnppre\Utility\Element $addBlockArea */
                        $addBlockArea = $region->layout_builder_add_block;
                        if (
                            isset($addBlockArea->link)
                            && $addBlockArea->link instanceof Element
                            && $addBlockArea->link->isType('link')
                        ) {
                            $url = $addBlockArea->link->getProperty('url');
                            if ($url instanceof Url) {
                            // Wrapper.
                                $addBlockArea->addClass([
                                'py-4',
                                'text-center',
                                'bg-primary-subtle',
                                'usb-layout-builder-add-block',
                                ]);

                            // Link.
                            // @phpstan-ignore-next-line
                                $addBlockArea->link->addClass([
                                'btn',
                                'btn-primary',
                                ]);
                            // @phpstan-ignore-next-line
                                $addBlockArea->link->setIcon(Bootstrap::icon('plus-lg'));
                            }
                        }
                    }
                }
            }

          // Section Library: Add this template to library.
            if (
                isset($layoutBuilderArea->add_template_to_library)
                && $layoutBuilderArea->add_template_to_library instanceof Element
                && $layoutBuilderArea->add_template_to_library->isType('link')
            ) {
                $url = $layoutBuilderArea->add_template_to_library->getProperty('url');
                if ($url instanceof Url) {
                    $layoutBuilderArea->add_template_to_library->addClass([
                    'btn',
                    'btn-outline-secondary',
                    ]);
                    $layoutBuilderArea->add_template_to_library->setIcon(Bootstrap::icon('folder-plus'));

                    $layoutBuilderArea->add_template_to_library->appendProperty('theme_wrappers', [
                    'container' => [
                      '#attributes' => [
                      'class' => [
                      'mb-3',
                      'text-center',
                      ],
                      ],
                    ],
                    ]);
                }
            }

          // Section Library: Import from library.
            if (
                isset($layoutBuilderArea->choose_template_from_library)
                && $layoutBuilderArea->choose_template_from_library instanceof Element
                && $layoutBuilderArea->choose_template_from_library->isType('link')
            ) {
                $url = $layoutBuilderArea->choose_template_from_library->getProperty('url');
                if ($url instanceof Url) {
                    $layoutBuilderArea->choose_template_from_library->addClass([
                    'btn',
                    'btn-outline-secondary',
                    'ms-3',
                    ]);
                    $layoutBuilderArea->choose_template_from_library->setIcon(Bootstrap::icon('download'));
                }
            }

          // Section Library: Add to library.
            if (
                isset($layoutBuilderArea->add_to_library)
                && $layoutBuilderArea->add_to_library instanceof Element
                && $layoutBuilderArea->add_to_library->isType('link')
            ) {
                $url = $layoutBuilderArea->add_to_library->getProperty('url');
                if ($url instanceof Url) {
                    $layoutBuilderArea->add_to_library->addClass('mx-1');
                    $layoutBuilderArea->add_to_library->setIcon(Bootstrap::icon('folder-plus'));
                    $layoutBuilderArea->add_to_library->setProperty('icon_position', 'icon_only');
                    $layoutBuilderArea->add_to_library->setProperty('weight', static::SECTION_LIBRARY_ADD_WEIGHT);
                }
            }

          // Layout Builder Reorder: Move section up.
            if (
                isset($layoutBuilderArea->rearrange_up)
                && $layoutBuilderArea->rearrange_up instanceof Element
                && $layoutBuilderArea->rearrange_up->isType('link')
            ) {
                $url = $layoutBuilderArea->rearrange_up->getProperty('url');
                if ($url instanceof Url) {
                    $layoutBuilderArea->rearrange_up->addClass('mx-1');
                    $layoutBuilderArea->rearrange_up->setIcon(Bootstrap::icon('arrow-up'));
                    $layoutBuilderArea->rearrange_up->setProperty('icon_position', 'icon_only');
                    $layoutBuilderArea->rearrange_up->setProperty('weight', static::LAYOUT_BUILDER_REORDER_UP_WEIGHT);
                }
            }

          // Layout Builder Reorder: Move section down.
            if (
                isset($layoutBuilderArea->rearrange_down)
                && $layoutBuilderArea->rearrange_down instanceof Element
                && $layoutBuilderArea->rearrange_down->isType('link')
            ) {
                $url = $layoutBuilderArea->rearrange_down->getProperty('url');
                if ($url instanceof Url) {
                    $layoutBuilderArea->rearrange_down->addClass('mx-1');
                    $layoutBuilderArea->rearrange_down->setIcon(Bootstrap::icon('arrow-down'));
                    $layoutBuilderArea->rearrange_down->setProperty('icon_position', 'icon_only');
                    $layoutBuilderArea->rearrange_down->setProperty('weight', static::LAYOUT_BUILDER_REORDER_DOWN_WEIGHT);
                }
            }
        }

      // @phpstan-ignore-next-line
        return $element;
    }

  /**
   * {@inheritdoc}
   */
    public static function trustedCallbacks(): array
    {
        return ['preRenderLayoutBuilder'];
    }
}
