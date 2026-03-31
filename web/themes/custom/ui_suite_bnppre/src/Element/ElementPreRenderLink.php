<?php

declare(strict_types=1);

namespace Drupal\ui_suite_bnppre\Element;

use Drupal\Component\Render\FormattableMarkup;
use Drupal\Core\Security\TrustedCallbackInterface;
use Drupal\ui_suite_bnppre\Utility\Bootstrap;
use Drupal\ui_suite_bnppre\Utility\Element;

/**
 * Element Prerender methods for link.
 */
class ElementPreRenderLink implements TrustedCallbackInterface {

  /**
   * Handle icon for a link element.
   */
  public static function preRenderLink(array $element): array {
    $element_object = Element::create($element);

    // Injects the icon into the title (the only way this is possible).
    /** @var string|null $icon */
    $icon = &$element_object->getProperty('icon');
    if ($icon) {
      $element_object->addClass('icon-link');
      // Also add the class in the options if it exists as it takes precedence.
      /** @var array $options */
      $options = $element_object->getProperty('options', []);
      if (isset($options['attributes'])) {
        $options['attributes']['class'][] = 'icon-link';
        $element_object->setProperty('options', $options);
      }

      /** @var string $title */
      $title = $element_object->getProperty('title');
      $position = $element_object->getProperty('icon_position', 'before');

      switch ($position) {
        case 'before':
          $rendered_icon = Bootstrap::toString($icon);
          $markup = "{$rendered_icon}@title";
          $element_object->setProperty('title', new FormattableMarkup($markup, ['@title' => $title]));
          break;

        case 'after':
          $rendered_icon = Bootstrap::toString($icon);
          $markup = "@title{$rendered_icon}";
          $element_object->setProperty('title', new FormattableMarkup($markup, ['@title' => $title]));
          break;

        case 'icon_only':
          /** @var string $attribute_title */
          $attribute_title = $element_object->getAttribute('title', '');
          if ($attribute_title) {
            $title .= ' - ' . $attribute_title;
          }
          $element_object
            ->setAttribute('title', $title)
            ->setProperty('title', $icon);
          break;
      }
    }

    // @phpstan-ignore-next-line
    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public static function trustedCallbacks(): array {
    return ['preRenderLink'];
  }

}
