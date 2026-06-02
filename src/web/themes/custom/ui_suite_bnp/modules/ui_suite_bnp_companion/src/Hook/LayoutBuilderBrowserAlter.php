<?php

declare(strict_types=1);

namespace Drupal\ui_suite_bnp_companion\Hook;

use Drupal\Component\Render\FormattableMarkup;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\Render\RendererInterface;
use Drupal\ui_suite_bnp\Utility\Element;

/**
 * Hook implementation.
 */
class LayoutBuilderBrowserAlter {

  public function __construct(
    protected RendererInterface $renderer,
  ) {}

  /**
   * Implements hook_layout_builder_browser_alter().
   *
   * Restructure Layout Builder Browser output to be like Core.
   */
  #[Hook('layout_builder_browser_alter')]
  public function alter(array &$build, array $context): void {
    if (!isset($build['block_categories'])) {
      return;
    }

    // @phpstan-ignore-next-line
    $element = Element::create($build);
    if (isset($element->block_categories) && $element->block_categories instanceof Element) {
      $categories = $element->block_categories->children();
      foreach ($categories as $category) {
        $category->appendProperty('context', [
          'usb_details_closed' => FALSE,
        ]);

        $preparedLinks = [];
        /** @var array $links */
        $links = $category->offsetGet('links', []);
        foreach ($links as $link) {
          $preparedLinks[] = [
            'title' => new FormattableMarkup('@image@label', [
              '@image' => \is_array($link['link']['#title']['image']) ? $this->renderer->render($link['link']['#title']['image']) : '',
              '@label' => \is_array($link['link']['#title']['label']) ? $this->renderer->render($link['link']['#title']['label']) : '',
            ]),
            'url' => $link['link']['#url'],
            'attributes' => $link['link']['#attributes'],
          ];
        }

        $category->offsetSet('links', [
          '#theme' => 'links',
          '#links' => $preparedLinks,
        ]);
      }
    }
  }

}
